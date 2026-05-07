<?php

namespace App\Models\Concerns;

use App\Support\LocaleResolver;

trait HasTranslations
{
    /**
     * Resolve a translation for a given attribute.
     *
     * @param string $attribute
     * @param string|null $fallback
     * @return string|null
     */
    protected function resolveTranslation(string $attribute, ?string $fallback = null): ?string
    {
        $translations = $this->getAttribute($attribute);
        $locale = LocaleResolver::resolve();

        if (is_array($translations)) {
            return $translations[$locale]
                ?? $translations['en']
                ?? $translations['ar']
                ?? $fallback;
        }

        if (is_string($translations) && $translations !== '') {
            // Check if it's a JSON string that wasn't cast
            $decoded = json_decode($translations, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded[$locale]
                    ?? $decoded['en']
                    ?? $decoded['ar']
                    ?? $fallback;
            }
            return $translations;
        }

        return $fallback;
    }

    /**
     * Get a localized attribute value.
     *
     * @param string $attribute
     * @param string|null $fallbackAttribute
     * @return string|null
     */
    public function getLocalized(string $attribute, ?string $fallbackAttribute = null): ?string
    {
        $fallback = $fallbackAttribute ? $this->getAttribute($fallbackAttribute) : null;
        return $this->resolveTranslation($attribute, $fallback);
    }
}
