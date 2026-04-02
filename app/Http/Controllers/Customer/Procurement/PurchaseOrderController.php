<?php

namespace App\Http\Controllers\Customer\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Procurement\PurchaseOrderStoreRequest;
use App\Http\Requests\Customer\Procurement\PurchaseOrderUpdateRequest;
use App\Models\InventoryProduct;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Supplier;
use App\Services\Procurement\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly PurchaseOrderService $service
    ) {}

    public function index(string $locale): View
    {
        $rows = $this->service->paginate(request()->only(['status', 'supplier_id']));
        $suppliers = Supplier::query()->orderBy('name')->get();

        return view('dashboard.customer.procurement.purchase_orders.index', compact('rows', 'suppliers'));
    }

    public function create(string $locale): View
    {
        $order = new PurchaseOrder();
        $suppliers = Supplier::query()->orderBy('name')->get();
        $rfqs = Rfq::query()->orderByDesc('created_at')->get();
        $quotations = Quotation::query()->orderByDesc('created_at')->get();
        $products = InventoryProduct::query()->orderBy('name')->get();
        $items = [['product_id' => '', 'quantity' => 1, 'unit_price' => 0]];
        $selectedQuotationId = request('quotation_id');
        $selectedRfqId = request('rfq_id');

        return view('dashboard.customer.procurement.purchase_orders.create', compact('order', 'suppliers', 'rfqs', 'quotations', 'products', 'items', 'selectedQuotationId', 'selectedRfqId'));
    }

    public function store(PurchaseOrderStoreRequest $request, string $locale): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('customer.procurement.purchase-orders.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.created', ['entity' => __('procurement.entities.purchase_order')]));
    }

    public function show(string $locale, PurchaseOrder $order): View
    {
        $order->load(['supplier', 'items.product', 'goodsReceipts', 'rfq', 'quotation']);

        return view('dashboard.customer.procurement.purchase_orders.show', compact('order'));
    }

    public function edit(string $locale, PurchaseOrder $order): View
    {
        $suppliers = Supplier::query()->orderBy('name')->get();
        $rfqs = Rfq::query()->orderByDesc('created_at')->get();
        $quotations = Quotation::query()->orderByDesc('created_at')->get();
        $products = InventoryProduct::query()->orderBy('name')->get();
        $items = $order->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
        ])->toArray();
        $selectedQuotationId = null;
        $selectedRfqId = null;

        return view('dashboard.customer.procurement.purchase_orders.edit', compact('order', 'suppliers', 'rfqs', 'quotations', 'products', 'items', 'selectedQuotationId', 'selectedRfqId'));
    }

    public function update(PurchaseOrderUpdateRequest $request, string $locale, PurchaseOrder $order): RedirectResponse
    {
        $this->service->update($order, $request->validated());

        return redirect()->route('customer.procurement.purchase-orders.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.updated', ['entity' => __('procurement.entities.purchase_order')]));
    }

    public function destroy(string $locale, PurchaseOrder $order): RedirectResponse
    {
        $this->service->delete($order);

        return redirect()->route('customer.procurement.purchase-orders.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.deleted', ['entity' => __('procurement.entities.purchase_order')]));
    }
}
