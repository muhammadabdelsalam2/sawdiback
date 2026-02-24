<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\InventoryBatchStoreRequest;
use App\Http\Requests\Warehouse\InventoryDeliveryStoreRequest;
use App\Http\Requests\Warehouse\InventoryMovementStoreRequest;
use App\Http\Requests\Warehouse\InventoryProductionStoreRequest;
use App\Http\Requests\Warehouse\WarehouseAlertQueryRequest;
use App\Models\InventoryBatch;
use App\Models\InventoryDeliveryItem;
use App\Models\InventoryProduct;
use App\Models\LivestockAnimal;
use App\Services\Warehouse\CreateDeliveryService;
use App\Services\Warehouse\ReceiveInventoryBatchService;
use App\Services\Warehouse\RecordInventoryMovementService;
use App\Services\Warehouse\RecordProductionService;
use App\Services\Warehouse\WarehouseAlertService;
use App\Services\Warehouse\WarehouseStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class WarehouseController extends Controller
{
    public function __construct(
        private readonly WarehouseStockService $stockService,
        private readonly WarehouseAlertService $alertService,
        private readonly ReceiveInventoryBatchService $receiveBatchService,
        private readonly RecordInventoryMovementService $movementService,
        private readonly RecordProductionService $productionService,
        private readonly CreateDeliveryService $deliveryService
    ) {
    }

    public function index(string $locale): View
    {
        $products = InventoryProduct::query()->where('is_active', true)->orderBy('name')->get();
        $animals = LivestockAnimal::query()->orderBy('tag_number')->get();
        $batches = InventoryBatch::query()
            ->with('product')
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $stockRows = $products->map(function (InventoryProduct $product) {
            $onHand = $this->stockService->stockOnHand($product->id);
            return [
                'product' => $product,
                'stock_on_hand' => $onHand,
                'is_low_stock' => $onHand <= (float) $product->low_stock_threshold,
            ];
        });

        return view('dashboard.warehouse.index', compact('products', 'animals', 'batches', 'stockRows'));
    }

    public function storeBatch(InventoryBatchStoreRequest $request, string $locale): RedirectResponse
    {
        try {
            $this->receiveBatchService->execute($request->validated());
            return redirect()->back()->with('success', 'Batch received successfully.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeMovement(InventoryMovementStoreRequest $request, string $locale): RedirectResponse
    {
        try {
            $this->movementService->execute($request->validated());
            return redirect()->back()->with('success', 'Inventory movement recorded successfully.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeProduction(InventoryProductionStoreRequest $request, string $locale): RedirectResponse
    {
        try {
            $this->productionService->execute($request->validated());
            return redirect()->back()->with('success', 'Production recorded successfully.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function storeDelivery(InventoryDeliveryStoreRequest $request, string $locale): RedirectResponse
    {
        try {
            $this->deliveryService->execute($request->validated());
            return redirect()->back()->with('success', 'Delivery recorded successfully.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function alerts(WarehouseAlertQueryRequest $request, string $locale): View
    {
        $days = (int) ($request->validated('days') ?? 30);
        $lowStockRows = $this->alertService->lowStockProducts();
        $expiringRows = $this->alertService->expiringBatches($days);

        return view('dashboard.warehouse.alerts', compact('days', 'lowStockRows', 'expiringRows'));
    }

    public function traceability(string $locale): View
    {
        $products = InventoryProduct::query()->orderBy('name')->get();
        $selectedProductId = request()->integer('product_id');

        $batches = InventoryBatch::query()
            ->with(['product', 'deliveryItems.delivery'])
            ->when($selectedProductId, fn ($q) => $q->where('inventory_product_id', $selectedProductId))
            ->orderByDesc('id')
            ->get();

        $deliveryItems = InventoryDeliveryItem::query()
            ->with(['delivery', 'product', 'batch'])
            ->when($selectedProductId, fn ($q) => $q->where('inventory_product_id', $selectedProductId))
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return view('dashboard.warehouse.traceability', compact('products', 'selectedProductId', 'batches', 'deliveryItems'));
    }
}
