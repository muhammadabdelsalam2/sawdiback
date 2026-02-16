<?php

namespace App\Http\Requests\Livestock;

use App\Models\FeedType;
use Illuminate\Validation\Rule;

class FeedTypeUpdateRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();
        /** @var FeedType|null $feedType */
        $feedType = $this->route('feed_type');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('feed_types', 'name')
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId))
                    ->ignore($feedType?->id),
            ],
            'category' => ['required', Rule::in(['concentrate', 'roughage', 'supplement'])],
            'unit' => ['required', 'string', 'max:100'],
            'cost_per_unit' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
