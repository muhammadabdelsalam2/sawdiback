<?php

namespace App\Http\Controllers\Customer\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Procurement\RfqStoreRequest;
use App\Http\Requests\Customer\Procurement\RfqUpdateRequest;
use App\Models\PurchaseRequisition;
use App\Models\Rfq;
use App\Services\Procurement\RfqService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RfqController extends Controller
{
    public function __construct(
        private readonly RfqService $service
    ) {}

    public function index(string $locale): View
    {
        $rows = $this->service->paginate(request()->only(['status', 'purchase_requisition_id']));
        $requisitions = PurchaseRequisition::query()->orderByDesc('created_at')->get();

        return view('dashboard.customer.procurement.rfqs.index', compact('rows', 'requisitions'));
    }

    public function create(string $locale): View
    {
        $rfq = new Rfq();
        $requisitions = PurchaseRequisition::query()->orderByDesc('created_at')->get();
        $selectedRequisitionId = request('purchase_requisition_id');

        return view('dashboard.customer.procurement.rfqs.create', compact('rfq', 'requisitions', 'selectedRequisitionId'));
    }

    public function store(RfqStoreRequest $request, string $locale): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('customer.procurement.rfqs.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.created', ['entity' => __('procurement.entities.rfq')]));
    }

    public function show(string $locale, Rfq $rfq): View
    {
        $rfq->load(['requisition', 'quotations']);

        return view('dashboard.customer.procurement.rfqs.show', compact('rfq'));
    }

    public function edit(string $locale, Rfq $rfq): View
    {
        $requisitions = PurchaseRequisition::query()->orderByDesc('created_at')->get();
        $selectedRequisitionId = null;

        return view('dashboard.customer.procurement.rfqs.edit', compact('rfq', 'requisitions', 'selectedRequisitionId'));
    }

    public function update(RfqUpdateRequest $request, string $locale, Rfq $rfq): RedirectResponse
    {
        $this->service->update($rfq, $request->validated());

        return redirect()->route('customer.procurement.rfqs.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.updated', ['entity' => __('procurement.entities.rfq')]));
    }

    public function destroy(string $locale, Rfq $rfq): RedirectResponse
    {
        $this->service->delete($rfq);

        return redirect()->route('customer.procurement.rfqs.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.deleted', ['entity' => __('procurement.entities.rfq')]));
    }
}
