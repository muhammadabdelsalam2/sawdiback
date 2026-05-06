<?php

namespace App\Http\Resources;

use App\Support\LocaleResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = LocaleResolver::resolve($request);

        return [
            'id' => $this->id,
            'title' => $this->title[$locale] ?? $this->title['en'] ?? $this->title['ar'] ?? '',
            'description' => $this->description[$locale] ?? $this->description['en'] ?? $this->description['ar'] ?? '',
            'video' => $this->video ? asset('storage/' . $this->video) : null,
            'video_url' => $this->video_url,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
