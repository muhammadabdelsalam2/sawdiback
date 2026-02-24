<?php

namespace App\Http\Requests\Warehouse;

class WarehouseAlertQueryRequest extends BaseWarehouseRequest
{
    public function rules(): array
    {
        return [
            'days' => ['nullable', 'integer', 'min:1', 'max:180'],
        ];
    }
}

