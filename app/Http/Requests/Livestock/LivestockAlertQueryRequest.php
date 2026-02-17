<?php

namespace App\Http\Requests\Livestock;

class LivestockAlertQueryRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        return [
            'days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'month' => ['nullable', 'date_format:Y-m'],
        ];
    }
}
