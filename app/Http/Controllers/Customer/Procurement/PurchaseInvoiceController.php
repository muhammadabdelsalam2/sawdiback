<?php

namespace App\Http\Controllers\Customer\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Procurement\PurchaseInvoiceStoreRequest;
use App\Http\Requests\Customer\Procurement\PurchaseInvoiceUpdateRequest;
use App\Models\Department;
use App\Models\GoodsReceipt;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\Procurement\PurchaseInvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PurchaseInvoiceController extends Controller
{
    public function __construct(
        private readonly PurchaseInvoiceService $service
    ) {}

    public function index(string $locale): View
    {
        $rows = $this->service->paginate(request()->only(['status', 'supplier_id', 'date_from', 'date_to']));
        $suppliers = Supplier::query()->orderBy('name')->get();

        return view('dashboard.customer.procurement.invoices.index', compact('rows', 'suppliers'));
    }

    public function create(string $locale): View
    {
        $invoice = new Invoice();
        $suppliers = Supplier::query()->orderBy('name')->get();
        $departments = Department::query()->orderBy('name')->get();
        $orders = PurchaseOrder::query()->orderByDesc('created_at')->get();
        $receipts = GoodsReceipt::query()->orderByDesc('created_at')->get();
        $selectedOrderId = request('purchase_order_id');
        $selectedReceiptId = request('goods_receipt_id');

        return view('dashboard.customer.procurement.invoices.create', compact('invoice', 'suppliers', 'departments', 'orders', 'receipts', 'selectedOrderId', 'selectedReceiptId'));
    }

    public function store(PurchaseInvoiceStoreRequest $request, string $locale): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('customer.procurement.invoices.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.created', ['entity' => __('procurement.entities.invoice')]));
    }

    public function show(string $locale, Invoice $invoice): View
    {
        $this->ensurePurchaseInvoice($invoice);
        $invoice->load(['supplier', 'department', 'purchaseOrder', 'goodsReceipt']);

        return view('dashboard.customer.procurement.invoices.show', compact('invoice'));
    }

    public function edit(string $locale, Invoice $invoice): View
    {
        $this->ensurePurchaseInvoice($invoice);
        $suppliers = Supplier::query()->orderBy('name')->get();
        $departments = Department::query()->orderBy('name')->get();
        $orders = PurchaseOrder::query()->orderByDesc('created_at')->get();
        $receipts = GoodsReceipt::query()->orderByDesc('created_at')->get();
        $selectedOrderId = null;
        $selectedReceiptId = null;

        return view('dashboard.customer.procurement.invoices.edit', compact('invoice', 'suppliers', 'departments', 'orders', 'receipts', 'selectedOrderId', 'selectedReceiptId'));
    }

    public function update(PurchaseInvoiceUpdateRequest $request, string $locale, Invoice $invoice): RedirectResponse
    {
        $this->ensurePurchaseInvoice($invoice);
        $this->service->update($invoice, $request->validated());

        return redirect()->route('customer.procurement.invoices.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.updated', ['entity' => __('procurement.entities.invoice')]));
    }

    public function destroy(string $locale, Invoice $invoice): RedirectResponse
    {
        $this->ensurePurchaseInvoice($invoice);
        $this->service->delete($invoice);

        return redirect()->route('customer.procurement.invoices.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.deleted', ['entity' => __('procurement.entities.invoice')]));
    }

    private function ensurePurchaseInvoice(Invoice $invoice): void
    {
        if ($invoice->type !== 'purchase') {
            abort(404);
        }
    }
}
