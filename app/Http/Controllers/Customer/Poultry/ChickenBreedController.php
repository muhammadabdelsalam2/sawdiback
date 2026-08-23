<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Poultry\ChickenBreedEggLogStoreRequest;
use App\Http\Requests\Customer\Poultry\ChickenBreedStoreRequest;
use App\Http\Requests\Customer\Poultry\ChickenBreedUpdateRequest;
use App\Models\FarmPen;
use App\Models\Poultry\PoultryChickenBreed;
use App\Models\Poultry\PoultryChickenBreedEggLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChickenBreedController extends Controller
{
    public function index(): View
    {
        $breeds = PoultryChickenBreed::query()->withCount('eggLogs')->orderBy('code')->paginate(15);

        return view('dashboard.customer.poultry.chicken_breeds.index', compact('breeds'));
    }

    public function create(): View
    {
        $pens = FarmPen::query()->forSelect()->get();

        return view('dashboard.customer.poultry.chicken_breeds.create', compact('pens'));
    }

    public function store(ChickenBreedStoreRequest $request, string $locale): RedirectResponse
    {
        $breed = PoultryChickenBreed::query()->create($request->validated());

        return redirect()->route('customer.poultry.chicken-breeds.show', ['locale' => $locale, 'chicken_breed' => $breed->id])
            ->with('success', __('poultry.messages.success.chicken_breed_created'));
    }

    public function show(string $locale, PoultryChickenBreed $chicken_breed): View
    {
        $chicken_breed->load(['eggLogs' => fn ($q) => $q->orderByDesc('production_date')]);

        return view('dashboard.customer.poultry.chicken_breeds.show', ['breed' => $chicken_breed]);
    }

    public function edit(string $locale, PoultryChickenBreed $chicken_breed): View
    {
        $pens = FarmPen::query()->forSelect()->get();

        return view('dashboard.customer.poultry.chicken_breeds.edit', ['breed' => $chicken_breed, 'pens' => $pens]);
    }

    public function update(ChickenBreedUpdateRequest $request, string $locale, PoultryChickenBreed $chicken_breed): RedirectResponse
    {
        $chicken_breed->update($request->validated());

        return redirect()->route('customer.poultry.chicken-breeds.show', ['locale' => $locale, 'chicken_breed' => $chicken_breed->id])
            ->with('success', __('poultry.messages.success.chicken_breed_updated'));
    }

    public function destroy(string $locale, PoultryChickenBreed $chicken_breed): RedirectResponse
    {
        $chicken_breed->delete();

        return redirect()->route('customer.poultry.chicken-breeds.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.chicken_breed_deleted'));
    }

    public function storeEggLog(ChickenBreedEggLogStoreRequest $request, string $locale, PoultryChickenBreed $chicken_breed): RedirectResponse
    {
        PoultryChickenBreedEggLog::query()->create([
            'chicken_breed_id' => $chicken_breed->id,
            ...$request->validated(),
        ]);

        return redirect()->back()->with('success', __('poultry.messages.success.egg_log_recorded'));
    }
}
