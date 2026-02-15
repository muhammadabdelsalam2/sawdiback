<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreCountryRequest;
use App\Http\Requests\Settings\UpdateCountryRequest;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CountryController extends Controller
{
    public function index(string $locale): View
    {
        $activeLocale = session('locale_full', $locale);

        $countries = Country::query()
            ->where('tenant_id', 1) // TODO: replace with tenant resolver later
            ->latest()
            ->paginate(10);

        return view('settings.countries.index', compact('countries', 'activeLocale'));
    }

    public function create(string $locale): View
    {
        $activeLocale = session('locale_full', $locale);
        return view('settings.countries.create', compact('activeLocale'));
    }

    public function store(string $locale, StoreCountryRequest $request): RedirectResponse
    {
        try {
            Country::create([
                'tenant_id' => 1, // TODO: replace with tenant resolver later
                'name' => $request->name,
                'iso2' => $request->iso2,
                'iso3' => $request->iso3,
                'phone_code' => $request->phone_code,
                'is_active' => (bool) $request->is_active,
            ]);

            return redirect()
                ->route('settings.countries.index', ['locale' => $locale])
                ->with('success', 'Country created successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create country.');
        }
    }

    public function edit(string $locale, Country $country): View
    {
        $activeLocale = session('locale_full', $locale);

        // Minimal safety (tenant placeholder)
        if ((int) $country->tenant_id !== 1) {
            abort(404);
        }

        return view('settings.countries.edit', compact('country', 'activeLocale'));
    }

    public function update(string $locale, UpdateCountryRequest $request, Country $country): RedirectResponse
    {
        if ((int) $country->tenant_id !== 1) {
            abort(404);
        }

        try {
            $country->update([
                'name' => $request->name,
                'iso2' => $request->iso2,
                'iso3' => $request->iso3,
                'phone_code' => $request->phone_code,
                'is_active' => (bool) $request->is_active,
            ]);

            return redirect()
                ->route('settings.countries.index', ['locale' => $locale])
                ->with('success', 'Country updated successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update country.');
        }
    }

    public function destroy(string $locale, Country $country): RedirectResponse
    {
        if ((int) $country->tenant_id !== 1) {
            abort(404);
        }

        try {
            $country->delete();

            return redirect()
                ->route('settings.countries.index', ['locale' => $locale])
                ->with('success', 'Country deleted successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete country.');
        }
    }
}
