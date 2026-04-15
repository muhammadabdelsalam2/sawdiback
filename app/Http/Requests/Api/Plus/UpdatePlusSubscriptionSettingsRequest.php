<?php

namespace App\Http\Requests\Api\Plus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlusSubscriptionSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'auto_renew' => ['sometimes', 'boolean'],
            'payment_method_id' => [
                'sometimes',
                'integer',
                Rule::exists('user_payment_methods', 'id')->where(
                    fn ($query) => $query->where('user_id', $userId)
                ),
            ],
            'vacation_mode' => ['sometimes', 'boolean'],
            'resume_at' => ['sometimes', 'nullable', 'date', 'after:today'],
            'cancel_subscription' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasUpdatableSetting =
                $this->has('auto_renew') ||
                $this->has('payment_method_id') ||
                $this->has('vacation_mode') ||
                $this->has('cancel_subscription');

            if (!$hasUpdatableSetting) {
                $validator->errors()->add(
                    'settings',
                    'At least one setting must be provided for update.'
                );
            }

            if ($this->boolean('vacation_mode') && !$this->filled('resume_at')) {
                $validator->errors()->add(
                    'resume_at',
                    'The resume_at field is required when vacation_mode is true.'
                );
            }

            if ($this->filled('resume_at') && !$this->boolean('vacation_mode')) {
                $validator->errors()->add(
                    'resume_at',
                    'The resume_at field may only be sent when vacation_mode is true.'
                );
            }
        });
    }
}
