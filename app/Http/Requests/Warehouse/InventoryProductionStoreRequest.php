<?php

namespace App\Http\Requests\Warehouse;

use App\Models\Category;
use Illuminate\Validation\Rule;

class InventoryProductionStoreRequest extends BaseWarehouseRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();
        $animalProductCategoryId = Category::query()
            ->where('tenant_id', $tenantId)
            ->where('code', 'animal_product')
            ->value('id');

        return [
            'inventory_product_id' => [
                'required',
                'integer',
                Rule::exists('inventory_products', 'id')->where(function ($q) use ($tenantId, $animalProductCategoryId) {
                    $q->where('tenant_id', $tenantId);

                    if ($animalProductCategoryId) {
                        $q->where('category_id', $animalProductCategoryId);
                    } else {
                        $q->where('category', 'animal_product');
                    }
                }),
            ],
            'livestock_animal_id' => ['nullable', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'batch_number' => ['required', 'string', 'max:100'],
            'production_date' => ['required', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:production_date'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
