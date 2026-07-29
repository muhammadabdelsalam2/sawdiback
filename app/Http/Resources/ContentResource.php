<?php

namespace App\Http\Resources;

use App\Support\LocaleResolver;
use App\Support\PublicFileUrl;
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
            'video' => PublicFileUrl::url($this->video),
            'video_url' => $this->video_url,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
