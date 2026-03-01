<?php

namespace App\Http\Requests\Customer\SalesDistribution;

use Illuminate\Foundation\Http\FormRequest;

class SalesInvoiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_no' => ['required', 'string', 'max:100'],
            'sales_order_id' => ['required', 'integer', 'exists:sales_orders,id'],
            'customer_id' => ['required', 'integer', 'exists:sales_customers,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:unpaid,partially_paid,paid,void'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('sales_dist.validation.messages.required'),
            'string' => __('sales_dist.validation.messages.string'),
            'max' => __('sales_dist.validation.messages.max'),
            'in' => __('sales_dist.validation.messages.in'),
            'exists' => __('sales_dist.validation.messages.exists'),
            'date' => __('sales_dist.validation.messages.date'),
            'after_or_equal' => __('sales_dist.validation.messages.after_or_equal'),
            'numeric' => __('sales_dist.validation.messages.numeric'),
            'min' => __('sales_dist.validation.messages.min'),
            'integer' => __('sales_dist.validation.messages.integer'),
        ];
    }

    public function attributes(): array
    {
        return [
            'invoice_no' => __('sales_dist.validation.attributes.invoice_no'),
            'sales_order_id' => __('sales_dist.validation.attributes.sales_order_id'),
            'customer_id' => __('sales_dist.validation.attributes.customer_id'),
            'invoice_date' => __('sales_dist.validation.attributes.invoice_date'),
            'due_date' => __('sales_dist.validation.attributes.due_date'),
            'subtotal' => __('sales_dist.validation.attributes.subtotal'),
            'tax' => __('sales_dist.validation.attributes.tax'),
            'status' => __('sales_dist.validation.attributes.status'),
            'notes' => __('sales_dist.validation.attributes.notes'),
        ];
    }
}
