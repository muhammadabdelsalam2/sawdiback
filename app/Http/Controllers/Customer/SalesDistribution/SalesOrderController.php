<?php

namespace App\Http\Controllers\Customer\SalesDistribution;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SalesDistribution\SalesOrderStoreRequest;
use App\Http\Requests\Customer\SalesDistribution\SalesOrderUpdateRequest;
use App\Models\SalesDistribution\SalesOrder;
use App\Services\SalesDistribution\SalesContractService;
use App\Services\SalesDistribution\SalesCustomerService;
use App\Services\SalesDistribution\SalesDistributionContextService;
use App\Services\SalesDistribution\SalesOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesOrderController extends Controller
{
    public function __construct(
        private readonly SalesOrderService $service,
        private readonly SalesCustomerService $customers,
        private readonly SalesContractService $contracts,
        private readonly SalesDistributionContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $orders = $this->service->paginate($tenantId, request()->only(['customer_id', 'status', 'date_from', 'date_to']));
        $customerOptions = $this->customers->listActive($tenantId);

        return view('dashboard.customer.sales_distribution.orders.index', compact('orders', 'customerOptions'));
    }

    public function create(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $customers = $this->customers->listActive($tenantId);
        $contracts = $this->contracts->listActive($tenantId);
        $items = [['product_id' => '', 'qty' => '', 'unit_price' => '', 'discount' => '0']];

        return view('dashboard.customer.sales_distribution.orders.create', compact('customers', 'contracts', 'items'));
    }

    public function store(SalesOrderStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $this->service->create($tenantId, $request->validated());

        return redirect()->route('customer.sales-distribution.orders.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.created', ['entity' => __('sales_dist.entities.order')]));
    }

    public function show(string $locale, SalesOrder $order): View
    {
        $this->authorizeTenant($order->tenant_id);
        $order->load(['customer', 'contract', 'items']);
        $orderItemsExportTitle = __('sales_dist.export.titles.order_items_show', ['order_no' => $order->order_no]);

        return view('dashboard.customer.sales_distribution.orders.show', compact('order', 'orderItemsExportTitle'));
    }

    public function edit(string $locale, SalesOrder $order): View
    {
        $this->authorizeTenant($order->tenant_id);
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $customers = $this->customers->listActive($tenantId);
        $contracts = $this->contracts->listActive($tenantId);
        $order->load('items');
        $items = $order->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'qty' => $item->qty,
            'unit_price' => $item->unit_price,
            'discount' => $item->discount,
        ])->toArray();

        return view('dashboard.customer.sales_distribution.orders.edit', compact('order', 'customers', 'contracts', 'items'));
    }

    public function update(SalesOrderUpdateRequest $request, string $locale, SalesOrder $order): RedirectResponse
    {
        $this->authorizeTenant($order->tenant_id);
        $this->service->update($order, $request->validated());

        return redirect()->route('customer.sales-distribution.orders.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.updated', ['entity' => __('sales_dist.entities.order')]));
    }

    public function destroy(string $locale, SalesOrder $order): RedirectResponse
    {
        $this->authorizeTenant($order->tenant_id);
        $this->service->delete($order);

        return redirect()->route('customer.sales-distribution.orders.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.deleted', ['entity' => __('sales_dist.entities.order')]));
    }

    private function authorizeTenant(string $tenantId): void
    {
        if ((string) auth()->user()->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
