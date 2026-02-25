<?php

namespace App\Http\Controllers\Customer\SalesDistribution;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\SalesDistribution\SalesPaymentStoreRequest;
use App\Http\Requests\Customer\SalesDistribution\SalesPaymentUpdateRequest;
use App\Models\SalesDistribution\SalesInvoice;
use App\Models\SalesDistribution\SalesPayment;
use App\Services\SalesDistribution\SalesPaymentService;
use Illuminate\Http\RedirectResponse;

class SalesPaymentController extends Controller
{
    public function __construct(
        private readonly SalesPaymentService $service
    ) {}

    public function store(SalesPaymentStoreRequest $request, string $locale, SalesInvoice $invoice): RedirectResponse
    {
        $this->authorizeTenant($invoice->tenant_id);
        $this->service->create($invoice, $request->validated());

        return redirect()->route('customer.sales-distribution.invoices.show', [
            'locale' => $locale,
            'invoice' => $invoice->id,
        ])->with('success', __('sales_dist.messages.payment_added'));
    }

    public function update(SalesPaymentUpdateRequest $request, string $locale, SalesInvoice $invoice, SalesPayment $payment): RedirectResponse
    {
        $this->authorizeTenant($invoice->tenant_id);
        if ($payment->invoice_id !== $invoice->id) {
            abort(404);
        }

        $this->service->update($payment, $request->validated());

        return redirect()->route('customer.sales-distribution.invoices.show', [
            'locale' => $locale,
            'invoice' => $invoice->id,
        ])->with('success', __('sales_dist.messages.payment_updated'));
    }

    public function destroy(string $locale, SalesInvoice $invoice, SalesPayment $payment): RedirectResponse
    {
        $this->authorizeTenant($invoice->tenant_id);
        if ($payment->invoice_id !== $invoice->id) {
            abort(404);
        }

        $this->service->delete($payment);

        return redirect()->route('customer.sales-distribution.invoices.show', [
            'locale' => $locale,
            'invoice' => $invoice->id,
        ])->with('success', __('sales_dist.messages.payment_deleted'));
    }

    private function authorizeTenant(string $tenantId): void
    {
        if ((string) auth()->user()->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}
