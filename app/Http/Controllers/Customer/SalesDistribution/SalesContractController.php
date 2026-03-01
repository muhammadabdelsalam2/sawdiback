<?php

namespace App\Http\Controllers\Customer\SalesDistribution;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SalesDistribution\SalesContractStoreRequest;
use App\Http\Requests\Customer\SalesDistribution\SalesContractUpdateRequest;
use App\Models\SalesDistribution\SalesContract;
use App\Services\SalesDistribution\SalesContractService;
use App\Services\SalesDistribution\SalesCustomerService;
use App\Services\SalesDistribution\SalesDistributionContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesContractController extends Controller
{
    public function __construct(
        private readonly SalesContractService $service,
        private readonly SalesCustomerService $customers,
        private readonly SalesDistributionContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $contracts = $this->service->paginate($tenantId, request()->only(['customer_id', 'status', 'date_from', 'date_to']));
        $customerOptions = $this->customers->listActive($tenantId);

        return view('dashboard.customer.sales_distribution.contracts.index', compact('contracts', 'customerOptions'));
    }

    public function create(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $customers = $this->customers->listActive($tenantId);

        return view('dashboard.customer.sales_distribution.contracts.create', compact('customers'));
    }

    public function store(SalesContractStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $this->service->create($tenantId, $request->validated());

        return redirect()->route('customer.sales-distribution.contracts.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.created', ['entity' => __('sales_dist.entities.contract')]));
    }

    public function show(string $locale, SalesContract $contract): View
    {
        $this->authorizeTenant($contract->tenant_id);
        $contract->load('customer');

        return view('dashboard.customer.sales_distribution.contracts.show', compact('contract'));
    }

    public function edit(string $locale, SalesContract $contract): View
    {
        $this->authorizeTenant($contract->tenant_id);
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $customers = $this->customers->listActive($tenantId);

        return view('dashboard.customer.sales_distribution.contracts.edit', compact('contract', 'customers'));
    }

    public function update(SalesContractUpdateRequest $request, string $locale, SalesContract $contract): RedirectResponse
    {
        $this->authorizeTenant($contract->tenant_id);
        $this->service->update($contract, $request->validated());

        return redirect()->route('customer.sales-distribution.contracts.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.updated', ['entity' => __('sales_dist.entities.contract')]));
    }

    public function destroy(string $locale, SalesContract $contract): RedirectResponse
    {
        $this->authorizeTenant($contract->tenant_id);
        $this->service->delete($contract);

        return redirect()->route('customer.sales-distribution.contracts.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.deleted', ['entity' => __('sales_dist.entities.contract')]));
    }

    private function authorizeTenant(string $tenantId): void
    {
        if ((string) auth()->user()->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
