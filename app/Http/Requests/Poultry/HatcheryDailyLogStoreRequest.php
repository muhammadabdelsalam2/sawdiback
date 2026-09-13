<?php

namespace App\Http\Requests\Poultry;

use Illuminate\Foundation\Http\FormRequest;

class HatcheryDailyLogStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hatchery_batch_id' => ['required', 'exists:poultry_hatchery_batches,id'],
            'log_date'          => ['required', 'date'],
            'temperature'       => ['required', 'numeric', 'between:20,50'],
            'humidity'          => ['required', 'numeric', 'between:0,100'],
            'has_incident'      => ['nullable', 'boolean'],
            'incident_reason'   => ['nullable', 'required_if:has_incident,true', 'string'],
            'notes'             => ['nullable', 'string'],
        ];
    }
}