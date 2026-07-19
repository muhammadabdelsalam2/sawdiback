<?php

namespace App\Http\Requests\Customer\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseAssetUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:equipment,water_pipes,iron,other'],
            'storage_location' => ['nullable', 'string', 'max:255'],
            'quantity_or_status' => ['nullable', 'string', 'max:255'],
            'farm_id' => ['nullable', 'exists:farms,id'],
            'notes' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:2048'],
        ];
    }
}
