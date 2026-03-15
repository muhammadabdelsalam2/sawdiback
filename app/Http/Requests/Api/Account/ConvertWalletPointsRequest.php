<?php

namespace App\Http\Requests\Api\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ConvertWalletPointsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check() || auth()->check();
    }

    public function rules(): array
    {
        return [
            'points' => [
                'required',
                'integer',
                'min:' . config('wallet.points.minimum_redeemable_points', 100),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $points = (int) $this->input('points', 0);
            $multiple = (int) config('wallet.points.must_be_multiple_of', 100);

            if ($points > 0 && $points % $multiple !== 0) {
                $validator->errors()->add(
                    'points',
                    "Points must be a multiple of {$multiple}."
                );
            }
        });
    }
}
