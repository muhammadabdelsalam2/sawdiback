<?php

namespace App\Http\Requests\Subscriptions;

use App\Models\Feature;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeatureUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Feature $feature */
        $feature = $this->route('feature');

        return [
            'key' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('features', 'key')->ignore($feature->id)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:boolean,number,string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
