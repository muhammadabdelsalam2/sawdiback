<?php

namespace App\Http\Controllers\Livestock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Livestock\TransferAnimalRequest;
use App\Http\Requests\Livestock\AnimalStatusChangeRequest;
use App\Http\Requests\Livestock\LivestockAnimalStoreRequest;
use App\Http\Requests\Livestock\LivestockAnimalUpdateRequest;
use App\Models\AnimalBreed;
use App\Models\AnimalSpecies;
use App\Models\Farm;
use App\Models\FarmPen;
use App\Models\FeedType;
use App\Models\LivestockAnimal;
use App\Models\Vaccine;
use App\Repositories\LivestockAnimalRepository;
use App\Services\Livestock\AnimalMovementService;
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
        $tenantId = (string) auth()->user()->tenant_id;

        $items = $this->animals->paginateWithRelations(
            (int) $request->integer('per_page', 15),
            $request->only(['species_id', 'farm_id', 'pen_id', 'status', 'search'])
        );

        $farms = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']);
        $pens  = FarmPen::query()->where('tenant_id', $tenantId)->with('farm')->forSelect()->get();
        $currentLocale = $locale;

        return view('dashboard.livestock.animals.index', compact('items', 'farms', 'pens', 'currentLocale'));
    }

    public function create(string $locale): View
    {
        $tenantId = (string) auth()->user()->tenant_id;

        $farms   = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']);
        $species = AnimalSpecies::query()->where('tenant_id', $tenantId)->orderBy('name')->get();
        $breeds  = AnimalBreed::query()->where('tenant_id', $tenantId)->orderBy('name')->get();
        $animals = LivestockAnimal::query()->where('tenant_id', $tenantId)->orderBy('tag_number')->get();
        $pens    = FarmPen::query()->where('tenant_id', $tenantId)->with('farm')->forSelect()->get();
        $currentLocale = $locale;

        return view('dashboard.livestock.animals.create', compact('farms', 'species', 'breeds', 'animals', 'pens', 'currentLocale'));
    }

    public function store(LivestockAnimalStoreRequest $request, string $locale): RedirectResponse
    {
        $data = $request->validated();

        // إسناد المزرعة من الحظيرة المختارة إن وجدت أو الاعتماد على المزرعة المحددة
        if ($request->filled('pen_id') && empty($data['farm_id'])) {
            $pen = FarmPen::find($request->pen_id);
            if ($pen) {
                $data['farm_id'] = $pen->farm_id;
            }
        }

        $animal = $this->registerAnimalService->execute($data);

        return redirect()
            ->route('customer.livestock.animals.show', ['locale' => $locale, 'animal' => $animal->id])
            ->with('success', __('livestock.messages.success.animal_registered'));
    }

    public function transfer(
        TransferAnimalRequest $request,
        string $locale,
        LivestockAnimal $animal,
        AnimalMovementService $movementService
    ): RedirectResponse {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $animal->tenant_id !== $tenantId) {
            abort(403);
        }

        $toPen = FarmPen::where('tenant_id', $tenantId)->findOrFail($request->validated('to_pen_id'));

        $movementService->transfer($animal, $toPen);

        return back()->with('success', __('livestock.messages.animal_transferred') ?? 'تم نقل الحيوان وتحديث طاقة الحظائر بنجاح');
    }

    public function show(string $locale, LivestockAnimal $animal): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $animal->tenant_id !== $tenantId) {
            abort(403);
        }

        $animal->load([
            'species',
            'breed',
            'mother',
            'father',
            'pen.farm',
            'healthRecords',
            'vaccinations.vaccine',
            'reproductionCyclesAsFemale',
            'milkProductionLogs',
            'feedingLogs' => fn($q) => $q->with('feedType')->latest('feeding_date')->limit(10),
            'weightLogs',
            'statusHistory' => fn($q) => $q->latest('changed_at'),
        ]);

        $feedTypes = FeedType::query()->where('tenant_id', $tenantId)->orderBy('name')->get();
        $vaccines  = Vaccine::query()->where('tenant_id', $tenantId)->orderBy('name')->get();
        $currentLocale = $locale;

        return view('dashboard.livestock.animals.show', compact('animal', 'feedTypes', 'vaccines', 'currentLocale'));
    }

    public function edit(string $locale, LivestockAnimal $animal): View
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $animal->tenant_id !== $tenantId) {
            abort(403);
        }

        $farms   = Farm::query()->where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']);
        $species = AnimalSpecies::query()->where('tenant_id', $tenantId)->orderBy('name')->get();
        $breeds  = AnimalBreed::query()->where('tenant_id', $tenantId)->orderBy('name')->get();
        $animals = LivestockAnimal::query()->where('tenant_id', $tenantId)->whereKeyNot($animal->id)->orderBy('tag_number')->get();
        $pens    = FarmPen::query()->where('tenant_id', $tenantId)->with('farm')->forSelect()->get();
        $currentLocale = $locale;

        return view('dashboard.livestock.animals.edit', compact('animal', 'farms', 'species', 'breeds', 'animals', 'pens', 'currentLocale'));
    }

    public function update(LivestockAnimalUpdateRequest $request, string $locale, LivestockAnimal $animal): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $animal->tenant_id !== $tenantId) {
            abort(403);
        }

        $data = $request->validated();

        if ($request->filled('pen_id') && empty($data['farm_id'])) {
            $pen = FarmPen::find($request->pen_id);
            if ($pen) {
                $data['farm_id'] = $pen->farm_id;
            }
        }

        $this->animals->update($animal, $data);

        return redirect()
            ->route('customer.livestock.animals.show', ['locale' => $locale, 'animal' => $animal->id])
            ->with('success', __('livestock.messages.success.animal_updated'));
    }

    public function changeStatus(AnimalStatusChangeRequest $request, string $locale, LivestockAnimal $animal): RedirectResponse
    {
        $tenantId = (string) auth()->user()->tenant_id;
        if ((string) $animal->tenant_id !== $tenantId) {
            abort(403);
        }

        $this->changeAnimalStatusService->execute($animal, $request->validated());

        return redirect()
            ->back()
            ->with('success', __('livestock.messages.success.animal_status_updated'));
    }
}
