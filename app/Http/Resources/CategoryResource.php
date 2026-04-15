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
            'code' => $this->code,
            'name' => $this->name, // uses your localized accessor
            'notes' => $this->notes,
            'image' => $this->image 
                ? asset('storage/' . $this->image) 
                : 'https://placehold.co/400x400/cccccc/000000?text='.$this->name,
            'is_active' => $this->is_active,
            'parent_id' => $this->parent_id,
        ];
    }
}
