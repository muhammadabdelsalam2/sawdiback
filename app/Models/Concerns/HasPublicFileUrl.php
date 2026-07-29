<?php

namespace App\Models\Concerns;

use App\Support\PublicFileUrl;

trait HasPublicFileUrl
{
    protected function publicFileUrl(?string $path): ?string
    {
        return PublicFileUrl::url($path);
    }

    protected function publicFilePath(?string $path): ?string
    {
        return PublicFileUrl::path($path);
    }

    protected function normalizePublicFilePath(?string $path): ?string
    {
        return PublicFileUrl::normalize($path);
    }
}
