<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreCityRequest;
use App\Http\Requests\Settings\UpdateCityRequest;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends Controller
{
    private function tenantId(): int
    {
        return 1; // TODO: replace with tenant resolver later
    }

    private function activeLocale(): string
    {
        return session('locale_full', 'en-SA');
    }

    private function countriesList()
    {
        return Country::query()
            ->where('tenant_id', $this->tenantId())
            ->orderBy('name')
            ->get();
    }

    private function findCityOrFail(int $id): City
    {
        return City::query()
            ->where('tenant_id', $this->tenantId())
            ->findOrFail($id);
    }

    public function index(Request $request): View
    {
        $activeLocale = $this->activeLocale();
        $countries = $this->countriesList();

        $query = City::query()
            ->with('country')
            ->where('tenant_id', $this->tenantId())
            ->latest();

        $selectedCountryId = $request->query('country_id');
        if (!empty($selectedCountryId)) {
            $query->where('country_id', (int) $selectedCountryId);
        }

        $cities = $query->paginate(10)->withQueryString();

        return view('settings.cities.index', compact(
            'cities',
            'countries',
            'selectedCountryId',
            'activeLocale'
        ));
    }

    public function create(): View
    {
        $activeLocale = $this->activeLocale();
        $countries = $this->countriesList();

        return view('settings.cities.create', compact('countries', 'activeLocale'));
    }

    public function store(StoreCityRequest $request): RedirectResponse
    {
        try {
            City::create([
                'tenant_id' => $this->tenantId(), // TODO: replace with tenant resolver later
                'country_id' => $request->country_id,
                'name' => $request->name,
                'is_active' => (bool) $request->is_active,
            ]);

            return redirect()
                ->route('settings.cities.index', ['locale' => $request->route('locale')])
                ->with('success', 'City created successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create city.');
        }
    }

    // ✅ Fix: No Model Binding here
    public function edit(string $locale, int $city): View
    {
        $activeLocale = $this->activeLocale();
        $cityModel = $this->findCityOrFail($city);
        $countries = $this->countriesList();

        return view('settings.cities.edit', [
            'city' => $cityModel,
            'countries' => $countries,
            'activeLocale' => $activeLocale,
        ]);
    }

    // ✅ Fix: No Model Binding here
    public function update(UpdateCityRequest $request, string $locale, int $city): RedirectResponse
    {
        $cityModel = $this->findCityOrFail($city);

        try {
            $cityModel->update([
                'country_id' => $request->country_id,
                'name' => $request->name,
                'is_active' => (bool) $request->is_active,
            ]);

            return redirect()
                ->route('settings.cities.index', ['locale' => $request->route('locale')])
                ->with('success', 'City updated successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update city.');
        }
    }

    // ✅ Fix: No Model Binding here
    public function destroy(string $locale, int $city): RedirectResponse
    {
        $cityModel = $this->findCityOrFail($city);

        try {
            $cityModel->delete();

            return redirect()
                ->route('settings.cities.index', ['locale' => $locale])
                ->with('success', 'City deleted successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete city.');
        }
    }
}
