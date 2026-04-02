<?php

namespace App\Http\Controllers\Customer\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Procurement\PurchaseRequisitionStoreRequest;
use App\Http\Requests\Customer\Procurement\PurchaseRequisitionUpdateRequest;
use App\Models\Department;
use App\Models\InventoryProduct;
use App\Models\PurchaseRequisition;
use App\Models\User;
use App\Services\Procurement\PurchaseRequisitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PurchaseRequisitionController extends Controller
{
    public function __construct(
        private readonly PurchaseRequisitionService $service
    ) {}

    public function index(string $locale): View
    {
        $rows = $this->service->paginate(request()->only(['status', 'department_id']));
        $departments = Department::query()->orderBy('name')->get();

        return view('dashboard.customer.procurement.requisitions.index', compact('rows', 'departments'));
    }

    public function create(string $locale): View
    {
        $requisition = new PurchaseRequisition();
        $departments = Department::query()->orderBy('name')->get();
        $users = User::query()->orderBy('name')->get();
        $products = InventoryProduct::query()->orderBy('name')->get();
        $items = [['product_id' => '', 'quantity' => 1, 'estimated_price' => 0]];

        return view('dashboard.customer.procurement.requisitions.create', compact('requisition', 'departments', 'users', 'products', 'items'));
    }

    public function store(PurchaseRequisitionStoreRequest $request, string $locale): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('customer.procurement.requisitions.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.created', ['entity' => __('procurement.entities.requisition')]));
    }

    public function show(string $locale, PurchaseRequisition $requisition): View
    {
        $requisition->load(['department', 'requester', 'items.product', 'rfqs']);

        return view('dashboard.customer.procurement.requisitions.show', compact('requisition'));
    }

    public function edit(string $locale, PurchaseRequisition $requisition): View
    {
        $departments = Department::query()->orderBy('name')->get();
        $users = User::query()->orderBy('name')->get();
        $products = InventoryProduct::query()->orderBy('name')->get();
        $items = $requisition->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'estimated_price' => $item->estimated_price,
        ])->toArray();

        return view('dashboard.customer.procurement.requisitions.edit', compact('requisition', 'departments', 'users', 'products', 'items'));
    }

    public function update(PurchaseRequisitionUpdateRequest $request, string $locale, PurchaseRequisition $requisition): RedirectResponse
    {
        $this->service->update($requisition, $request->validated());

        return redirect()->route('customer.procurement.requisitions.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.updated', ['entity' => __('procurement.entities.requisition')]));
    }

    public function destroy(string $locale, PurchaseRequisition $requisition): RedirectResponse
    {
        $this->service->delete($requisition);

        return redirect()->route('customer.procurement.requisitions.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.deleted', ['entity' => __('procurement.entities.requisition')]));
    }
}
