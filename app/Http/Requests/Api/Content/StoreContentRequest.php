<?php

namespace App\Http\Requests\Api\Content;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'array'],
            'title.ar' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.ar' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'video' => ['required', 'string', 'max:2048'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $title = $this->input('title', []);
            $description = $this->input('description', []);

            $titleAr = $this->input('title_ar', $title['ar'] ?? null);
            $titleEn = $this->input('title_en', $title['en'] ?? null);
            $descriptionAr = $this->input('description_ar', $description['ar'] ?? null);
            $descriptionEn = $this->input('description_en', $description['en'] ?? null);

            if (blank($titleAr) && blank($titleEn)) {
                $validator->errors()->add('title', 'The title field is required in at least one language.');
            }

            if (blank($descriptionAr) && blank($descriptionEn)) {
                $validator->errors()->add('description', 'The description field is required in at least one language.');
            }
        });
    }
}
