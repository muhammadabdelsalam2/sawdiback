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
        $rows = AnimalSpecies::query()->orderBy('name')->paginate(15);
        return view('dashboard.livestock.master.species.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.livestock.master.species.create');
    }

    public function store(AnimalSpeciesStoreRequest $request, string $locale): RedirectResponse
    {
        AnimalSpecies::query()->create($request->validated());

        return redirect()
            ->route('customer.livestock.species.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Species created successfully.');
    }

    public function edit(string $locale, AnimalSpecies $species): View
    {
        return view('dashboard.livestock.master.species.edit', compact('species'));
    }

    public function update(AnimalSpeciesUpdateRequest $request, string $locale, AnimalSpecies $species): RedirectResponse
    {
        $species->update($request->validated());

        return redirect()
            ->route('customer.livestock.species.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Species updated successfully.');
    }

    public function destroy(string $locale, AnimalSpecies $species): RedirectResponse
    {
        try {
            $species->delete();
            return redirect()->back()->with('success', 'Species deleted successfully.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Species cannot be deleted because it is in use.');
        }
    }
}
