<?php

namespace App\Http\Requests\Dashboard\Content;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'description_ar' => ['required', 'string'],
            'description_en' => ['required', 'string'],
            'video' => ['nullable', 'file', 'mimes:mp4,avi,mov,wmv', 'max:51200'], // 50MB max
            'video_url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'title_ar.required' => __('validation.required', ['attribute' => __('dashboard.content.title_ar')]),
            'title_en.required' => __('validation.required', ['attribute' => __('dashboard.content.title_en')]),
            'description_ar.required' => __('validation.required', ['attribute' => __('dashboard.content.description_ar')]),
            'description_en.required' => __('validation.required', ['attribute' => __('dashboard.content.description_en')]),
            'video.mimes' => __('validation.mimes', ['attribute' => __('dashboard.content.video'), 'values' => 'mp4, avi, mov, wmv']),
            'video.max' => __('validation.max.file', ['attribute' => __('dashboard.content.video'), 'max' => '50MB']),
            'video_url.url' => __('validation.url', ['attribute' => __('dashboard.content.video_url')]),
        ];
    }
}
