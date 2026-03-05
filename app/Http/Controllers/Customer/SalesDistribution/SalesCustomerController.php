<?php

namespace App\Http\Controllers\Customer\SalesDistribution;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SalesDistribution\SalesCustomerStoreRequest;
use App\Http\Requests\Customer\SalesDistribution\SalesCustomerUpdateRequest;
use App\Models\SalesDistribution\SalesCustomer;
use App\Services\SalesDistribution\SalesCustomerService;
use App\Services\SalesDistribution\SalesDistributionContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesCustomerController extends Controller
{
    public function __construct(
        private readonly SalesCustomerService $service,
        private readonly SalesDistributionContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $customers = $this->service->paginate($tenantId, request()->only(['type', 'status', 'q']));

        return view('dashboard.customer.sales_distribution.customers.index', compact('customers'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.customer.sales_distribution.customers.create');
    }

    public function store(SalesCustomerStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $this->service->create($tenantId, $request->validated());

        return redirect()->route('customer.sales-distribution.customers.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.created', ['entity' => __('sales_dist.entities.customer')]));
    }

    public function show(string $locale, SalesCustomer $customer): View
    {
        $this->authorizeTenant($customer->tenant_id);
        $customer->load(['contracts', 'orders', 'invoices']);

        return view('dashboard.customer.sales_distribution.customers.show', compact('customer'));
    }

    public function edit(string $locale, SalesCustomer $customer): View
    {
        $this->authorizeTenant($customer->tenant_id);

        return view('dashboard.customer.sales_distribution.customers.edit', compact('customer'));
    }

    public function update(SalesCustomerUpdateRequest $request, string $locale, SalesCustomer $customer): RedirectResponse
    {
        $this->authorizeTenant($customer->tenant_id);
        $this->service->update($customer, $request->validated());

        return redirect()->route('customer.sales-distribution.customers.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.updated', ['entity' => __('sales_dist.entities.customer')]));
    }

    public function destroy(string $locale, SalesCustomer $customer): RedirectResponse
    {
        $this->authorizeTenant($customer->tenant_id);
        $this->service->delete($customer);

        return redirect()->route('customer.sales-distribution.customers.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.deleted', ['entity' => __('sales_dist.entities.customer')]));
    }

    private function authorizeTenant(string $tenantId): void
    {
        if ((string) auth()->user()->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
