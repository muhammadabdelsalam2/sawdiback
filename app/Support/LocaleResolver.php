<?php

namespace App\Support;

use Illuminate\Http\Request;

class LocaleResolver
{
    private const SUPPORTED_LOCALES = ['ar', 'en'];

    public static function resolve(?Request $request = null, ?string $fallback = 'en'): string
    {
        $request ??= request();

        $candidates = [
            $request?->route('locale'),
            $request?->header('X-Locale'),
            $request?->header('Accept-Language'),
            app()->getLocale(),
            $fallback,
        ];

        foreach ($candidates as $candidate) {
            $locale = self::normalize($candidate);

            if ($locale !== null) {
                return $locale;
            }
        }

        return $fallback ?? 'en';
    }

    public static function apply(?string $locale): string
    {
        $resolved = self::normalize($locale) ?? 'en';

        app()->setLocale($resolved);

        return $resolved;
    }

    private static function normalize(null|string|array $value): ?string
    {
        if (is_array($value)) {
            $value = $value[0] ?? null;
        }

        if (!is_string($value) || $value === '') {
            return null;
        }

        $locale = strtolower(substr(trim(explode(',', $value)[0]), 0, 2));

        return in_array($locale, self::SUPPORTED_LOCALES, true) ? $locale : null;
    }
}
