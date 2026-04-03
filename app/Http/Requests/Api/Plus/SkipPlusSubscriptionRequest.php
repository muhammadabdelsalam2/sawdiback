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
            'delivery_id' => ['sometimes', 'nullable', 'string', 'max:191'],
            'order_id' => ['sometimes', 'nullable', 'string', 'max:191'],
            'resume_at' => ['sometimes', 'nullable', 'date', 'after:today'],
            'reason' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $action = $this->input('action');

            if ($action === 'pause' && !$this->filled('resume_at')) {
                $validator->errors()->add(
                    'resume_at',
                    'The resume_at field is required when action is pause.'
                );
            }

            if (
                $this->filled('delivery_id') &&
                $this->filled('order_id') &&
                $this->input('delivery_id') !== $this->input('order_id')
            ) {
                $validator->errors()->add(
                    'delivery_id',
                    'The delivery_id and order_id fields must match when both are provided.'
                );
            }
        });
    }
}
