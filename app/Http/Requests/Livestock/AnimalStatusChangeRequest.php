<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class AnimalStatusChangeRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['active', 'sold', 'dead', 'slaughtered'])],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
