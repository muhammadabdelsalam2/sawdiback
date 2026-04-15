<?php

namespace App\Http\Controllers\Customer\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Procurement\QuotationStoreRequest;
use App\Http\Requests\Customer\Procurement\QuotationUpdateRequest;
use App\Models\InventoryProduct;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Models\Supplier;
use App\Services\Procurement\QuotationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function __construct(
        private readonly QuotationService $service
    ) {}

    public function index(string $locale): View
    {
        $rows = $this->service->paginate(request()->only(['status', 'rfq_id', 'supplier_id']));
        $rfqs = Rfq::query()->orderByDesc('created_at')->get();
        $suppliers = Supplier::query()->orderBy('name')->get();

        return view('dashboard.customer.procurement.quotations.index', compact('rows', 'rfqs', 'suppliers'));
    }

    public function create(string $locale): View
    {
        $quotation = new Quotation();
        $rfqs = Rfq::query()->orderByDesc('created_at')->get();
        $suppliers = Supplier::query()->orderBy('name')->get();
        $products = InventoryProduct::query()->orderBy('name')->get();
        $items = [['product_id' => '', 'quantity' => 1, 'unit_price' => 0]];
        $selectedRfqId = request('rfq_id');

        return view('dashboard.customer.procurement.quotations.create', compact('quotation', 'rfqs', 'suppliers', 'products', 'items', 'selectedRfqId'));
    }

    public function store(QuotationStoreRequest $request, string $locale): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('customer.procurement.quotations.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.created', ['entity' => __('procurement.entities.quotation')]));
    }

    public function show(string $locale, Quotation $quotation): View
    {
        $quotation->load(['rfq', 'supplier', 'items.product']);

        return view('dashboard.customer.procurement.quotations.show', compact('quotation'));
    }

    public function edit(string $locale, Quotation $quotation): View
    {
        $rfqs = Rfq::query()->orderByDesc('created_at')->get();
        $suppliers = Supplier::query()->orderBy('name')->get();
        $products = InventoryProduct::query()->orderBy('name')->get();
        $items = $quotation->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
        ])->toArray();
        $selectedRfqId = null;

        return view('dashboard.customer.procurement.quotations.edit', compact('quotation', 'rfqs', 'suppliers', 'products', 'items', 'selectedRfqId'));
    }

    public function update(QuotationUpdateRequest $request, string $locale, Quotation $quotation): RedirectResponse
    {
        $this->service->update($quotation, $request->validated());

        return redirect()->route('customer.procurement.quotations.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.updated', ['entity' => __('procurement.entities.quotation')]));
    }

    public function destroy(string $locale, Quotation $quotation): RedirectResponse
    {
        $this->service->delete($quotation);

        return redirect()->route('customer.procurement.quotations.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.deleted', ['entity' => __('procurement.entities.quotation')]));
    }
}
