<?php

namespace App\Http\Controllers\Customer\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Procurement\GoodsReceiptStoreRequest;
use App\Http\Requests\Customer\Procurement\GoodsReceiptUpdateRequest;
use App\Models\GoodsReceipt;
use App\Models\InventoryProduct;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\Procurement\GoodsReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GoodsReceiptController extends Controller
{
    public function __construct(
        private readonly GoodsReceiptService $service
    ) {}

    public function index(string $locale): View
    {
        $rows = $this->service->paginate(request()->only(['status', 'purchase_order_id']));
        $orders = PurchaseOrder::query()->orderByDesc('created_at')->get();

        return view('dashboard.customer.procurement.goods_receipts.index', compact('rows', 'orders'));
    }

    public function create(string $locale): View
    {
        $receipt = new GoodsReceipt();
        $orders = PurchaseOrder::query()->orderByDesc('created_at')->get();
        $users = User::query()->orderBy('name')->get();
        $products = InventoryProduct::query()->orderBy('name')->get();
        $items = [['product_id' => '', 'quantity' => 1]];
        $selectedOrderId = request('purchase_order_id');

        return view('dashboard.customer.procurement.goods_receipts.create', compact('receipt', 'orders', 'users', 'products', 'items', 'selectedOrderId'));
    }

    public function store(GoodsReceiptStoreRequest $request, string $locale): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('customer.procurement.goods-receipts.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.created', ['entity' => __('procurement.entities.goods_receipt')]));
    }

    public function show(string $locale, GoodsReceipt $receipt): View
    {
        $receipt->load(['purchaseOrder', 'receiver', 'items.product']);

        return view('dashboard.customer.procurement.goods_receipts.show', compact('receipt'));
    }

    public function edit(string $locale, GoodsReceipt $receipt): View
    {
        $orders = PurchaseOrder::query()->orderByDesc('created_at')->get();
        $users = User::query()->orderBy('name')->get();
        $products = InventoryProduct::query()->orderBy('name')->get();
        $items = $receipt->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
        ])->toArray();
        $selectedOrderId = null;

        return view('dashboard.customer.procurement.goods_receipts.edit', compact('receipt', 'orders', 'users', 'products', 'items', 'selectedOrderId'));
    }

    public function update(GoodsReceiptUpdateRequest $request, string $locale, GoodsReceipt $receipt): RedirectResponse
    {
        $this->service->update($receipt, $request->validated());

        return redirect()->route('customer.procurement.goods-receipts.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.updated', ['entity' => __('procurement.entities.goods_receipt')]));
    }

    public function destroy(string $locale, GoodsReceipt $receipt): RedirectResponse
    {
        $this->service->delete($receipt);

        return redirect()->route('customer.procurement.goods-receipts.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.deleted', ['entity' => __('procurement.entities.goods_receipt')]));
    }
}
