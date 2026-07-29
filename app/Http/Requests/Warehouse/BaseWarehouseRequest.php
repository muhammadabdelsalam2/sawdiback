<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function tenantId(): ?string
    {
        return session('tenant_id') ?? auth()->user()?->tenant_id;
    }

    protected function productImageRules(): array
    {
        return [
            'nullable',
            'file',
            'max:2048',
            'extensions:jpg,jpeg,png,webp',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! $value || ! method_exists($value, 'getRealPath')) {
                    return;
                }

                if (@getimagesize($value->getRealPath()) === false) {
                    $fail(__('validation.image', ['attribute' => $attribute]));
                }
            },
        ];
    }
}
