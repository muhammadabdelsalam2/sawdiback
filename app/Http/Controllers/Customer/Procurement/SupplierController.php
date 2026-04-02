<?php

namespace App\Http\Controllers\Customer\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Procurement\SupplierStoreRequest;
use App\Http\Requests\Customer\Procurement\SupplierUpdateRequest;
use App\Models\Supplier;
use App\Services\Procurement\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierService $service
    ) {}

    public function index(string $locale): View
    {
        $rows = $this->service->paginate(request()->only(['status', 'q']));

        return view('dashboard.customer.procurement.suppliers.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        $supplier = new Supplier();

        return view('dashboard.customer.procurement.suppliers.create', compact('supplier'));
    }

    public function store(SupplierStoreRequest $request, string $locale): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('customer.procurement.suppliers.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.created', ['entity' => __('procurement.entities.supplier')]));
    }

    public function edit(string $locale, Supplier $supplier): View
    {
        return view('dashboard.customer.procurement.suppliers.edit', compact('supplier'));
    }

    public function update(SupplierUpdateRequest $request, string $locale, Supplier $supplier): RedirectResponse
    {
        $this->service->update($supplier, $request->validated());

        return redirect()->route('customer.procurement.suppliers.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.updated', ['entity' => __('procurement.entities.supplier')]));
    }

    public function destroy(string $locale, Supplier $supplier): RedirectResponse
    {
        $this->service->delete($supplier);

        return redirect()->route('customer.procurement.suppliers.index', ['locale' => $locale])
            ->with('success', __('procurement.messages.deleted', ['entity' => __('procurement.entities.supplier')]));
    }
}
