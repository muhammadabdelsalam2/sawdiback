<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PublicFileController extends Controller
{
    public function show(string $path): Response
    {
        $path = $this->normalizePath($path);
        abort_if($path === null, 404);

        $basePath = realpath(storage_path('app/public'));
        abort_if($basePath === false, 404);

        $filePath = realpath($basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));
        abort_if($filePath === false || ! Str::startsWith($filePath, $basePath . DIRECTORY_SEPARATOR), 404);

        return response(file_get_contents($filePath), 200, [
            'Content-Type' => $this->contentType($filePath),
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    private function normalizePath(string $path): ?string
    {
        $path = ltrim(rawurldecode($path), '/\\');
        $path = str_replace('\\', '/', $path);

        foreach (['public/', 'storage/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
            }
        }

        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/')) {
            return null;
        }

        return $path;
    }

    private function contentType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }
}
