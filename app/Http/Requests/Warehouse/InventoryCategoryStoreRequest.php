<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Validation\Rule;

class InventoryCategoryStoreRequest extends BaseWarehouseRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('categories', 'code')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
