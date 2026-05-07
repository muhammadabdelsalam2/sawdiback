<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $nameAttribute = app()->getLocale() === 'en' ? 'name_en' : 'name_ar';

        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            $nameAttribute => $this->name,
            'notes' => $this->notes,
            'image' => $this->image_url,
            'is_active' => $this->is_active,
            'parent_id' => $this->parent_id,
        ];
    }


}
