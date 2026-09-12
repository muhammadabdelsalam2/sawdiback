<?php

namespace App\Http\Requests\Customer\Livestock;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedingLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = (string) auth()->user()->tenant_id;

        return [
            'animal_id' => [
                'required',
                'exists:livestock_animals,id,tenant_id,' . $tenantId,
            ],
            'feed_type_id' => [
                'required',
                'exists:feed_types,id,tenant_id,' . $tenantId,
            ],
            'feeding_date' => ['required', 'date'],
            'quantity'     => ['required', 'numeric', 'min:0.01'],
            'unit_cost'    => ['nullable', 'numeric', 'min:0'],
        ];
    }
}