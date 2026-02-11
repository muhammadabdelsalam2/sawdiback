<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    //
    public function switch(string $locale, Request $request)
    {
        // Validate locale
        if (!preg_match('/^(en|ar)-(SA|EG)$/', $locale)) {
            $locale = 'en-SA';
        }

        [$lang, $country] = explode('-', $locale);

        // Store in session (single source of truth)
        session([
            'locale_full' => $locale,
            'locale' => $lang,
        ]);

        // Replace locale in current URL if exists
        $previousUrl = url()->previous();
        $parsed = parse_url($previousUrl);

        if (isset($parsed['path'])) {
            $segments = explode('/', trim($parsed['path'], '/'));

            // If first segment is locale → replace it
            if (preg_match('/^(en|ar)-(SA|EG)$/', $segments[0])) {
                $segments[0] = $locale;
            } else {
                array_unshift($segments, $locale);
            }

            $newPath = '/' . implode('/', $segments);

            return redirect($newPath);
        }

        // Fallback
        return redirect()->route('public.home', ['locale' => $locale]);
    }
}
