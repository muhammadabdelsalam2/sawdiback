<?php

namespace App\Http\Requests\Customer\Poultry;

use Illuminate\Validation\Rule;

class ChickenBreedUpdateRequest extends BasePoultryRequest
{
    public function rules(): array
    {
        $breed = $this->route('chicken_breed');

        return [
            'pen_id' => ['nullable', 'integer', Rule::exists('farm_pens', 'id')->where(fn ($q) => $q->where('tenant_id', $this->tenantId()))],
            'code' => ['required', 'string', 'max:100', Rule::unique('poultry_chicken_breeds', 'code')->ignore($breed?->id)->where(fn ($q) => $q->where('tenant_id', $this->tenantId()))],
            'breed_type' => ['required', Rule::in(['local', 'improved', 'broiler', 'layer'])],
            'purchase_amount' => ['required', 'numeric', 'min:0'],
            'female_count' => ['required', 'integer', 'min:0'],
            'male_count' => ['required', 'integer', 'min:0'],
            'started_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
