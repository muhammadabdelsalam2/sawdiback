<?php

namespace App\Http\Requests\Poultry;

use App\Models\Poultry\PoultryHatcheryBatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HatcheryBatchStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hatchery_machine_id' => ['required', 'exists:poultry_hatchery_machines,id'],
            'batch_number'        => ['required', 'string', 'max:100'],
            'purchase_amount'     => ['nullable', 'numeric', 'min:0'],
            'breed_type'          => ['required', 'string', Rule::in(array_keys(PoultryHatcheryBatch::BREEDS))],
            'source_type'         => ['required', 'in:farm,external'],
            'seller_phone'        => ['nullable', 'required_if:source_type,external', 'string', 'max:50'],
            'seller_location'     => ['nullable', 'string', 'max:255'],
            'pen_id'              => ['nullable', 'required_if:source_type,farm', 'exists:farm_pens,id'],
            'loaded_at'           => ['required', 'date'],
            'expected_hatch_at'   => ['required', 'date', 'after_or_equal:loaded_at'],
            'eggs_loaded'         => ['required', 'integer', 'min:1'],
            'notes'               => ['nullable', 'string'],
        ];
    }
}