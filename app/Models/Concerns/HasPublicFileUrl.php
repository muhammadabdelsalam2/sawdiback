<?php

namespace App\Models\Concerns;

trait HasPublicFileUrl
{
    protected function publicFileUrl(?string $path): ?string
    {
        $path = $this->normalizePublicFilePath($path);

        if (! $path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, 'assets/') && file_exists(public_path($path))) {
            return asset($path);
        }

        if ($this->publicFilePath($path)) {
            return route('public.files.show', ['path' => $path]);
        }

        return null;
    }

    protected function publicFilePath(?string $path): ?string
    {
        $path = $this->normalizePublicFilePath($path);

        if (! $path || filter_var($path, FILTER_VALIDATE_URL) || str_starts_with($path, 'assets/')) {
            return null;
        }

        $storagePath = storage_path('app/public/' . $path);

        if (file_exists($storagePath)) {
            return $storagePath;
        }

        return null;
    }

    protected function normalizePublicFilePath(?string $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = ltrim(trim($path), '/\\');
        $path = str_replace('\\', '/', $path);

        foreach (['public/', 'storage/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
            }
        }

        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        return $path;
    }
}
