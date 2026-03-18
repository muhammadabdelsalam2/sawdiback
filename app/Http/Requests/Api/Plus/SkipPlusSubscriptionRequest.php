<?php

namespace App\Http\Requests\Api\Plus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SkipPlusSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'string', Rule::in(['skip_once', 'pause'])],
            'resume_at' => ['sometimes', 'nullable', 'date', 'after:today'],
            'reason' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('action') === 'pause' && !$this->filled('resume_at')) {
                $validator->errors()->add('resume_at', 'The resume_at field is required when action is pause.');
            }
        });
    }
}
