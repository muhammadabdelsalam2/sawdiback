<?php

namespace App\Http\Controllers\Livestock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Livestock\AnimalSpeciesStoreRequest;
use App\Http\Requests\Livestock\AnimalSpeciesUpdateRequest;
use App\Models\AnimalSpecies;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class AnimalSpeciesController extends Controller
{
    public function index(string $locale): View
    {
        $rows = AnimalSpecies::query()
            ->orderBy('name_translations->' . $this->localeKey())
            ->orderBy('name')
            ->paginate(15);
        return view('dashboard.livestock.master.species.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.livestock.master.species.create');
    }

    public function store(AnimalSpeciesStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        $data['name_translations'] = [$this->localeKey() => $data['name']];
        
        AnimalSpecies::query()->create($data);

        return redirect()
            ->route('customer.livestock.species.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('livestock.messages.success.species_created'));
    }

    public function edit(string $locale, AnimalSpecies $species): View
    {
        return view('dashboard.livestock.master.species.edit', compact('species'));
    }

    public function update(AnimalSpeciesUpdateRequest $request, string $locale, AnimalSpecies $species): RedirectResponse
    {
        $data = $request->validated();
        $translations = $species->name_translations ?? [];
        $translations[$this->localeKey()] = $data['name'];
        $data['name_translations'] = $translations;

        $species->update($data);

        return redirect()
            ->route('customer.livestock.species.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('livestock.messages.success.species_updated'));
    }

    public function destroy(string $locale, AnimalSpecies $species): RedirectResponse
    {
        try {
            $species->delete();
            return redirect()->back()->with('success', __('livestock.messages.success.species_deleted'));
        } catch (Throwable $e) {
            return redirect()->back()->with('error', __('livestock.messages.error.species_in_use'));
        }
    }

    private function localeKey(): string
    {
        return substr(app()->getLocale(), 0, 2);
    }
}
