<?php

namespace App\Http\Requests\Customer\Livestock;

use Illuminate\Foundation\Http\FormRequest;

class TransferAnimalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = (string) auth()->user()->tenant_id;

        return [
            'to_pen_id' => [
                'required',
                'exists:farm_pens,id,tenant_id,' . $tenantId,
            ],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}