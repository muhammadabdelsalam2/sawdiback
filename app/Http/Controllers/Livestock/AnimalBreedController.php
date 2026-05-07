<?php

namespace App\Http\Controllers\Livestock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Livestock\AnimalBreedStoreRequest;
use App\Http\Requests\Livestock\AnimalBreedUpdateRequest;
use App\Models\AnimalBreed;
use App\Models\AnimalSpecies;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class AnimalBreedController extends Controller
{
    public function index(string $locale): View
    {
        $rows = AnimalBreed::query()
            ->with('species')
            ->orderBy('name_translations->' . $this->localeKey())
            ->orderBy('name')
            ->paginate(15);
        return view('dashboard.livestock.master.breeds.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        $species = AnimalSpecies::query()
            ->orderBy('name_translations->' . $this->localeKey())
            ->orderBy('name')
            ->get();
        return view('dashboard.livestock.master.breeds.create', compact('species'));
    }

    public function store(AnimalBreedStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();
        $data['name_translations'] = [$this->localeKey() => $data['name']];
        
        AnimalBreed::query()->create($data);

        return redirect()
            ->route('customer.livestock.breeds.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('livestock.messages.success.breed_created'));
    }

    public function edit(string $locale, AnimalBreed $breed): View
    {
        $species = AnimalSpecies::query()
            ->orderBy('name_translations->' . $this->localeKey())
            ->orderBy('name')
            ->get();
        return view('dashboard.livestock.master.breeds.edit', compact('breed', 'species'));
    }

    public function update(AnimalBreedUpdateRequest $request, string $locale, AnimalBreed $breed): RedirectResponse
    {
        $data = $request->validated();
        $translations = $breed->name_translations ?? [];
        $translations[$this->localeKey()] = $data['name'];
        $data['name_translations'] = $translations;

        $breed->update($data);

        return redirect()
            ->route('customer.livestock.breeds.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', __('livestock.messages.success.breed_updated'));
    }

    public function destroy(string $locale, AnimalBreed $breed): RedirectResponse
    {
        try {
            $breed->delete();
            return redirect()->back()->with('success', __('livestock.messages.success.breed_deleted'));
        } catch (Throwable $e) {
            return redirect()->back()->with('error', __('livestock.messages.error.breed_in_use'));
        }
    }

    private function localeKey(): string
    {
        return substr(app()->getLocale(), 0, 2);
    }
}
