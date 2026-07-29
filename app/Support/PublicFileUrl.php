<?php

namespace App\Support;

class PublicFileUrl
{
    public static function url(?string $path): ?string
    {
        $path = self::normalize($path);

        if (! $path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, 'assets/') && file_exists(public_path($path))) {
            return asset($path);
        }

        if (self::path($path)) {
            return route('public.files.show', ['path' => $path]);
        }

        return null;
    }

    public static function path(?string $path): ?string
    {
        $path = self::normalize($path);

        if (! $path || filter_var($path, FILTER_VALIDATE_URL) || str_starts_with($path, 'assets/')) {
            return null;
        }

        $storagePath = storage_path('app/public/' . $path);

        return file_exists($storagePath) ? $storagePath : null;
    }

    public static function normalize(?string $path): ?string
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
