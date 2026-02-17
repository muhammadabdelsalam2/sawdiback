<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class AnimalFeedingStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'animal_id' => ['required_without:animal_ids', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'animal_ids' => ['required_without:animal_id', 'array', 'min:1'],
            'animal_ids.*' => ['integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'feed_type_id' => ['required', 'integer', Rule::exists('feed_types', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'feeding_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
