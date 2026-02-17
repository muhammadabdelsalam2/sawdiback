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
        $rows = AnimalBreed::query()->with('species')->orderBy('name')->paginate(15);
        return view('dashboard.livestock.master.breeds.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        $species = AnimalSpecies::query()->orderBy('name')->get();
        return view('dashboard.livestock.master.breeds.create', compact('species'));
    }

    public function store(AnimalBreedStoreRequest $request, string $locale): RedirectResponse
    {
        AnimalBreed::query()->create($request->validated());

        return redirect()
            ->route('superadmin.livestock.breeds.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Breed created successfully.');
    }

    public function edit(string $locale, AnimalBreed $breed): View
    {
        $species = AnimalSpecies::query()->orderBy('name')->get();
        return view('dashboard.livestock.master.breeds.edit', compact('breed', 'species'));
    }

    public function update(AnimalBreedUpdateRequest $request, string $locale, AnimalBreed $breed): RedirectResponse
    {
        $breed->update($request->validated());

        return redirect()
            ->route('superadmin.livestock.breeds.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Breed updated successfully.');
    }

    public function destroy(string $locale, AnimalBreed $breed): RedirectResponse
    {
        try {
            $breed->delete();
            return redirect()->back()->with('success', 'Breed deleted successfully.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Breed cannot be deleted because it is in use.');
        }
    }
}
