<?php

namespace App\Http\Controllers\Livestock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Livestock\AnimalFeedingStoreRequest;
use App\Http\Requests\Livestock\HealthRecordStoreRequest;
use App\Http\Requests\Livestock\LivestockAlertQueryRequest;
use App\Http\Requests\Livestock\MilkProductionStoreRequest;
use App\Http\Requests\Livestock\ReproductionBirthRequest;
use App\Http\Requests\Livestock\ReproductionCycleStoreRequest;
use App\Http\Requests\Livestock\ReproductionInseminationRequest;
use App\Http\Requests\Livestock\ReproductionPregnancyCheckRequest;
use App\Http\Requests\Livestock\VaccinationStoreRequest;
use App\Http\Requests\Livestock\WeightLogStoreRequest;
use App\Models\AnimalBirth;
use App\Models\AnimalVaccination;
use App\Models\LivestockAnimal;
use App\Models\ReproductionCycle;
use App\Repositories\ReproductionCycleRepository;
use App\Services\Livestock\LivestockAlertService;
use App\Services\Livestock\ManageReproductionCycleService;
use App\Services\Livestock\RecordFeedingService;
use App\Services\Livestock\RecordHealthService;
use App\Services\Livestock\RecordMilkProductionService;
use App\Services\Livestock\RecordVaccinationService;
use App\Services\Livestock\RecordWeightService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LivestockOperationsController extends Controller
{
    public function __construct(
        private readonly RecordFeedingService $recordFeedingService,
        private readonly RecordMilkProductionService $recordMilkProductionService,
        private readonly RecordHealthService $recordHealthService,
        private readonly RecordVaccinationService $recordVaccinationService,
        private readonly RecordWeightService $recordWeightService,
        private readonly ManageReproductionCycleService $reproductionService,
        private readonly ReproductionCycleRepository $cycles,
        private readonly LivestockAlertService $alerts
    ) {
    }

    public function recordFeeding(AnimalFeedingStoreRequest $request, string $locale): RedirectResponse
    {
        $this->recordFeedingService->execute($request->validated());
        return redirect()->back()->with('success', 'Feeding logs recorded successfully.');
    }

    public function recordMilkProduction(MilkProductionStoreRequest $request, string $locale): RedirectResponse
    {
        $this->recordMilkProductionService->execute($request->validated());

        return redirect()->back()->with('success', 'Milk production recorded successfully.');
    }

    public function recordHealth(HealthRecordStoreRequest $request, string $locale): RedirectResponse
    {
        $this->recordHealthService->execute($request->validated());

        return redirect()->back()->with('success', 'Health record saved successfully.');
    }

    public function recordVaccination(VaccinationStoreRequest $request, string $locale): RedirectResponse
    {
        $this->recordVaccinationService->execute($request->validated());

        return redirect()->back()->with('success', 'Vaccination recorded successfully.');
    }

    public function openReproductionCycle(ReproductionCycleStoreRequest $request, string $locale): RedirectResponse
    {
        $this->reproductionService->openCycle($request->validated());

        return redirect()->back()->with('success', 'Reproduction cycle opened successfully.');
    }

    public function recordWeight(WeightLogStoreRequest $request, string $locale): RedirectResponse
    {
        $this->recordWeightService->execute($request->validated());

        return redirect()->back()->with('success', 'Weight record saved successfully.');
    }

    public function inseminateCycle(ReproductionInseminationRequest $request, string $locale, ReproductionCycle $cycle): RedirectResponse
    {
        $this->reproductionService->recordInsemination($cycle, $request->validated());

        return redirect()->back()->with('success', 'Insemination data saved successfully.');
    }

    public function pregnancyCheckCycle(ReproductionPregnancyCheckRequest $request, string $locale, ReproductionCycle $cycle): RedirectResponse
    {
        $this->reproductionService->recordPregnancyCheck($cycle, $request->validated());

        return redirect()->back()->with('success', 'Pregnancy check saved successfully.');
    }

    public function recordBirth(ReproductionBirthRequest $request, string $locale, ReproductionCycle $cycle): RedirectResponse
    {
        $this->reproductionService->recordBirth($cycle, $request->validated());

        return redirect()->back()->with('success', 'Birth recorded and offspring created successfully.');
    }

    public function listCycles(string $locale): View
    {
        $rows = $this->cycles->paginateWithRelations(15);
        $femaleAnimals = LivestockAnimal::query()->with('species')->where('gender', 'female')->orderBy('tag_number')->get();
        $maleAnimals = LivestockAnimal::query()->where('gender', 'male')->orderBy('tag_number')->get();
        $recentBirths = AnimalBirth::query()->with('mother')->orderByDesc('id')->limit(20)->get();

        return view('dashboard.livestock.reproduction.index', compact('rows', 'femaleAnimals', 'maleAnimals', 'recentBirths'));
    }

    public function vaccinationDueAlerts(LivestockAlertQueryRequest $request, string $locale): View
    {
        $days = (int) $request->integer('days', 7);
        $rows = $this->alerts->vaccinationsDueInDays($days);

        return view('dashboard.livestock.alerts.vaccinations_due', compact('rows', 'days'));
    }

    public function vaccinationOverdueAlerts(string $locale): View
    {
        $rows = $this->alerts->vaccinationsOverdue();

        return view('dashboard.livestock.alerts.vaccinations_overdue', compact('rows'));
    }

    public function expectedDeliveries(LivestockAlertQueryRequest $request, string $locale): View
    {
        $month = $request->validated('month');
        $rows = $this->alerts->expectedDeliveriesInMonth($month);

        return view('dashboard.livestock.alerts.expected_deliveries', compact('rows', 'month'));
    }

    public function underTreatmentAnimals(string $locale): View
    {
        $rows = $this->alerts->animalsUnderTreatment();
        $vaccinationsDue = AnimalVaccination::query()
            ->whereNotNull('next_due_date')
            ->whereBetween('next_due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('next_due_date')
            ->limit(20)
            ->get();

        return view('dashboard.livestock.alerts.under_treatment', compact('rows', 'vaccinationsDue'));
    }
}
