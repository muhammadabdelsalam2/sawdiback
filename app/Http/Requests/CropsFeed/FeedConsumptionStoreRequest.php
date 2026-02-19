<?php

namespace App\Http\Requests\CropsFeed;

use Illuminate\Validation\Rule;

class FeedConsumptionStoreRequest extends BaseCropsFeedRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'feed_type_id' => ['required', 'integer', Rule::exists('feed_types', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'animal_id' => ['nullable', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'group_name' => ['nullable', 'string', 'max:255', 'required_without:animal_id'],
            'consumption_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
