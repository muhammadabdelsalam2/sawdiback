<?php

namespace App\Http\Requests\Api\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class ReviewSubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'review' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'reasons' => ['sometimes', 'array'],
            'reasons.*' => ['string', 'max:255'],
            'images' => ['sometimes', 'array', 'max:' . (int) config('ecommerce.review.max_images', 5)],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
