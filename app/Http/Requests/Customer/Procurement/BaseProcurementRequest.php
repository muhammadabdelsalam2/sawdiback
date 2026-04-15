<?php

namespace App\Http\Requests\Customer\Procurement;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseProcurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
