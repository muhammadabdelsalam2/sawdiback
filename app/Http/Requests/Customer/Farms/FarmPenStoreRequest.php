<?php

namespace App\Http\Requests\Customer\Farms;

use App\Models\FarmPen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FarmPenStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

public function rules(): array
    {
        return [
            'farm_id' => [
                'required',
                Rule::exists('farms', 'id')->where(function ($query) {
                    $query->where('tenant_id', auth()->user()->tenant_id);
                }),
            ],
            'pen_number'    => ['required', 'string', 'max:50'],
            'type'          => ['required', 'string', Rule::in(FarmPen::TYPES)],
            'capacity'      => ['nullable', 'integer', 'min:0'],
            'current_count' => ['nullable', 'integer', 'min:0'],
            'notes'         => ['nullable', 'string'],
        ];
    }}