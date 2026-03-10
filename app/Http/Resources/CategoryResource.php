<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $image = $this->image ?? null;

        if ($image && !filter_var($image, FILTER_VALIDATE_URL)) {
            $image = Storage::disk('public')->url($image);
        }

        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'slug' => $this->slug ?? null,
            'image' => $image,
            'description' => $this->description ?? null,
            'is_active' => isset($this->is_active) ? (bool) $this->is_active : true,
        ];
    }
}
