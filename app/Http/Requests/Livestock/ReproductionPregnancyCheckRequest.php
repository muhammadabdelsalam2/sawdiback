<?php

namespace App\Http\Requests\Livestock;

class ReproductionPregnancyCheckRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        return [
            'pregnancy_confirmed' => ['required', 'boolean'],
            'pregnancy_check_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:pregnancy_check_date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
