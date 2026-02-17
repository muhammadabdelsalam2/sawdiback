<?php

namespace App\Http\Controllers\Livestock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Livestock\AnimalStatusChangeRequest;
use App\Http\Requests\Livestock\LivestockAnimalStoreRequest;
use App\Http\Requests\Livestock\LivestockAnimalUpdateRequest;
use App\Models\AnimalBreed;
use App\Models\AnimalSpecies;
use App\Models\FeedType;
use App\Models\LivestockAnimal;
use App\Models\Vaccine;
use App\Repositories\LivestockAnimalRepository;
use App\Services\Livestock\ChangeAnimalStatusService;
use App\Services\Livestock\RegisterAnimalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LivestockAnimalController extends Controller
{
    public function __construct(
        private readonly LivestockAnimalRepository $animals,
        private readonly RegisterAnimalService $registerAnimalService,
        private readonly ChangeAnimalStatusService $changeAnimalStatusService
    ) {
    }

    public function index(Request $request, string $locale): View
    {
        $items = $this->animals->paginateWithRelations((int) $request->integer('per_page', 15));

        return view('dashboard.livestock.animals.index', compact('items'));
    }

    public function create(string $locale): View
    {
        $species = AnimalSpecies::query()->orderBy('name')->get();
        $breeds = AnimalBreed::query()->orderBy('name')->get();
        $animals = LivestockAnimal::query()->orderBy('tag_number')->get();

        return view('dashboard.livestock.animals.create', compact('species', 'breeds', 'animals'));
    }

    public function store(LivestockAnimalStoreRequest $request, string $locale): RedirectResponse
    {
        $animal = $this->registerAnimalService->execute($request->validated());

        return redirect()
            ->route('superadmin.livestock.animals.show', ['locale' => session('locale_full', 'en-SA'), 'animal' => $animal->id])
            ->with('success', 'Animal registered successfully.');
    }

    public function show(string $locale, LivestockAnimal $animal): View
    {
        $animal->load([
            'species',
            'breed',
            'mother',
            'father',
            'healthRecords',
            'vaccinations.vaccine',
            'reproductionCyclesAsFemale',
            'milkProductionLogs',
            'feedingLogs.feedType',
            'weightLogs',
            'statusHistory',
        ]);

        $feedTypes = FeedType::query()->orderBy('name')->get();
        $vaccines = Vaccine::query()->orderBy('name')->get();

        return view('dashboard.livestock.animals.show', compact('animal', 'feedTypes', 'vaccines'));
    }

    public function edit(string $locale, LivestockAnimal $animal): View
    {
        $species = AnimalSpecies::query()->orderBy('name')->get();
        $breeds = AnimalBreed::query()->orderBy('name')->get();
        $animals = LivestockAnimal::query()->whereKeyNot($animal->id)->orderBy('tag_number')->get();

        return view('dashboard.livestock.animals.edit', compact('animal', 'species', 'breeds', 'animals'));
    }

    public function update(LivestockAnimalUpdateRequest $request, string $locale, LivestockAnimal $animal): RedirectResponse
    {
        $this->animals->update($animal, $request->validated());

        return redirect()
            ->route('superadmin.livestock.animals.show', ['locale' => session('locale_full', 'en-SA'), 'animal' => $animal->id])
            ->with('success', 'Animal updated successfully.');
    }

    public function changeStatus(AnimalStatusChangeRequest $request, string $locale, LivestockAnimal $animal): RedirectResponse
    {
        $this->changeAnimalStatusService->execute($animal, $request->validated());

        return redirect()
            ->back()
            ->with('success', 'Animal status updated successfully.');
    }
}
