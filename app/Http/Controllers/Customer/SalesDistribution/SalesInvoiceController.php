<?php

namespace App\Http\Controllers\Customer\SalesDistribution;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SalesDistribution\SalesInvoiceStoreRequest;
use App\Http\Requests\Customer\SalesDistribution\SalesInvoiceUpdateRequest;
use App\Models\SalesDistribution\SalesInvoice;
use App\Services\SalesDistribution\SalesCustomerService;
use App\Services\SalesDistribution\SalesDistributionContextService;
use App\Services\SalesDistribution\SalesInvoiceService;
use App\Services\SalesDistribution\SalesOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesInvoiceController extends Controller
{
    public function __construct(
        private readonly SalesInvoiceService $service,
        private readonly SalesCustomerService $customers,
        private readonly SalesOrderService $orders,
        private readonly SalesDistributionContextService $context
    ) {}

    public function index(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $invoices = $this->service->paginate($tenantId, request()->only(['status', 'date_from', 'date_to']));

        return view('dashboard.customer.sales_distribution.invoices.index', compact('invoices'));
    }

    public function create(string $locale): View
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $customers = $this->customers->listActive($tenantId);
        $orders = $this->orders->listForSelection($tenantId);

        return view('dashboard.customer.sales_distribution.invoices.create', compact('customers', 'orders'));
    }

    public function store(SalesInvoiceStoreRequest $request, string $locale): RedirectResponse
    {
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $this->service->create($tenantId, $request->validated());

        return redirect()->route('customer.sales-distribution.invoices.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.created', ['entity' => __('sales_dist.entities.invoice')]));
    }

    public function show(string $locale, SalesInvoice $invoice): View
    {
        $this->authorizeTenant($invoice->tenant_id);
        $invoice->load(['order', 'customer', 'payments']);
        $invoicePaymentsExportTitle = __('sales_dist.export.titles.invoice_payments_show', ['invoice_no' => $invoice->invoice_no]);

        return view('dashboard.customer.sales_distribution.invoices.show', compact('invoice', 'invoicePaymentsExportTitle'));
    }

    public function edit(string $locale, SalesInvoice $invoice): View
    {
        $this->authorizeTenant($invoice->tenant_id);
        $tenantId = $this->context->tenantIdOrFail(auth()->user());
        $customers = $this->customers->listActive($tenantId);
        $orders = $this->orders->listForSelection($tenantId);

        return view('dashboard.customer.sales_distribution.invoices.edit', compact('invoice', 'customers', 'orders'));
    }

    public function update(SalesInvoiceUpdateRequest $request, string $locale, SalesInvoice $invoice): RedirectResponse
    {
        $this->authorizeTenant($invoice->tenant_id);
        $this->service->update($invoice, $request->validated());

        return redirect()->route('customer.sales-distribution.invoices.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.updated', ['entity' => __('sales_dist.entities.invoice')]));
    }

    public function destroy(string $locale, SalesInvoice $invoice): RedirectResponse
    {
        $this->authorizeTenant($invoice->tenant_id);
        $this->service->delete($invoice);

        return redirect()->route('customer.sales-distribution.invoices.index', ['locale' => $locale])
            ->with('success', __('sales_dist.messages.deleted', ['entity' => __('sales_dist.entities.invoice')]));
    }

    private function authorizeTenant(string $tenantId): void
    {
        if ((string) auth()->user()->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
