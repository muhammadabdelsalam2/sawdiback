<?php

namespace App\Http\Controllers\Customer\SalesDistribution;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SalesDistribution\SalesShipmentStoreRequest;
use App\Http\Requests\Customer\SalesDistribution\SalesShipmentUpdateRequest;
use App\Models\SalesDistribution\SalesShipment;
use App\Services\SalesDistribution\SalesDistributionContextService;
use App\Services\SalesDistribution\SalesOrderService;
use App\Services\SalesDistribution\SalesShipmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesShipmentController extends Controller
{
    public function __construct(
        private readonly SalesShipmentService $service,
        private readonly SalesOrderService $orders,
        private readonly SalesDistributionContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $shipments = $this->service->paginate($tenantId, request()->only(['status', 'date_from', 'date_to']));

        return view('dashboard.customer.sales_distribution.shipments.index', compact('shipments'));
    }

    public function create(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $orders = $this->orders->listForSelection($tenantId);

        return view('dashboard.customer.sales_distribution.shipments.create', compact('orders'));
    }

    public function store(SalesShipmentStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $this->service->create($tenantId, $request->validated());

        return redirect()->route('customer.sales-distribution.shipments.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.created', ['entity' => __('sales_dist.entities.shipment')]));
    }

    public function show(string $locale, SalesShipment $shipment): View
    {
        $this->authorizeTenant($shipment->tenant_id);
        $shipment->load([
            'order.customer',
            'statusHistory' => fn ($q) => $q->with('changedBy')->orderByDesc('changed_at'),
        ]);
        $shipmentHistoryExportTitle = __('sales_dist.export.titles.shipment_history_show', ['shipment_no' => $shipment->shipment_no]);

        return view('dashboard.customer.sales_distribution.shipments.show', compact('shipment', 'shipmentHistoryExportTitle'));
    }

    public function edit(string $locale, SalesShipment $shipment): View
    {
        $this->authorizeTenant($shipment->tenant_id);
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $orders = $this->orders->listForSelection($tenantId);

        return view('dashboard.customer.sales_distribution.shipments.edit', compact('shipment', 'orders'));
    }

    public function update(SalesShipmentUpdateRequest $request, string $locale, SalesShipment $shipment): RedirectResponse
    {
        $this->authorizeTenant($shipment->tenant_id);
        $this->service->update($shipment, $request->validated());

        return redirect()->route('customer.sales-distribution.shipments.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.updated', ['entity' => __('sales_dist.entities.shipment')]));
    }

    public function destroy(string $locale, SalesShipment $shipment): RedirectResponse
    {
        $this->authorizeTenant($shipment->tenant_id);
        $this->service->delete($shipment);

        return redirect()->route('customer.sales-distribution.shipments.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.deleted', ['entity' => __('sales_dist.entities.shipment')]));
    }

    private function authorizeTenant(string $tenantId): void
    {
        if ((string) auth()->user()->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
