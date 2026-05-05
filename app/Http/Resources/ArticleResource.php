<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale(); // en أو ar حسب الـ URL

        return [
            'id'          => $this->id,
            'title'       => $this->title[$locale] ?? $this->title['en'] ?? '',
            'description' => $this->description[$locale] ?? $this->description['en'] ?? '',
            'category'    => $this->category,
            'image'       => $this->image_url,
            'created_at'  => $this->created_at?->toDateString(),
        ];
    }
}
