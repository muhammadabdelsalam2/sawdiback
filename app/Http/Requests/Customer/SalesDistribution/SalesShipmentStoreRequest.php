<?php

namespace App\Http\Requests\Customer\SalesDistribution;

use Illuminate\Foundation\Http\FormRequest;

class SalesShipmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sales_order_id' => ['required', 'integer', 'exists:sales_orders,id'],
            'shipment_no' => ['required', 'string', 'max:100'],
            'shipping_company' => ['required', 'string', 'max:190'],
            'tracking_no' => ['nullable', 'string', 'max:190'],
            'status' => ['required', 'in:pending,packed,shipped,delivered,returned'],
            'shipped_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date', 'after_or_equal:shipped_at'],
            'warehouse_id' => ['nullable', 'integer', 'min:1'],
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
            'sales_order_id' => __('sales_dist.validation.attributes.sales_order_id'),
            'shipment_no' => __('sales_dist.validation.attributes.shipment_no'),
            'shipping_company' => __('sales_dist.validation.attributes.shipping_company'),
            'tracking_no' => __('sales_dist.validation.attributes.tracking_no'),
            'status' => __('sales_dist.validation.attributes.status'),
            'shipped_at' => __('sales_dist.validation.attributes.shipped_at'),
            'delivered_at' => __('sales_dist.validation.attributes.delivered_at'),
            'warehouse_id' => __('sales_dist.validation.attributes.warehouse_id'),
            'notes' => __('sales_dist.validation.attributes.notes'),
        ];
    }
}
