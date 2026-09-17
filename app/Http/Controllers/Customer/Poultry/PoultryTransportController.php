<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Poultry\TransportVehicleStoreRequest;
use App\Http\Requests\Poultry\VehicleRentalStoreRequest;
use App\Models\Poultry\PoultryTransportVehicle;
use App\Models\Poultry\PoultryVehicleRental;
use App\Services\Poultry\PoultryFinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PoultryTransportController extends Controller
{
    public function __construct(
        protected PoultryFinancialService $financialService
    ) {}

    public function index(Request $request): JsonResponse|View
    {
        $vehicles = PoultryTransportVehicle::query()
            ->with(['farm:id,name'])
            ->withCount(['rentals' => fn($query) => $query->where('payment_status', 'pending')])
            ->when($request->filled('farm_id'), fn($q) => $q->where('farm_id', $request->integer('farm_id')))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn($sub) => $sub->where('plate_number', 'like', "%{$term}%")
                    ->orWhere('driver_name', 'like', "%{$term}%"));
            })
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $vehicles,
            ]);
        }

        $currentLocale = $request->route('locale') ?? app()->getLocale();

        return view('dashboard.customer.poultry.vehicles.index', compact('vehicles', 'currentLocale'));
    }

    public function store(TransportVehicleStoreRequest $request): JsonResponse|RedirectResponse
    {
        $vehicle = PoultryTransportVehicle::create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle created successfully',
                'data'    => $vehicle->load('farm:id,name'),
            ], Response::HTTP_CREATED);
        }

        $locale = $request->route('locale') ?? app()->getLocale();

        return redirect()
            ->route('customer.poultry.vehicles.show', ['locale' => $locale, 'vehicle' => $vehicle->id])
            ->with('success', __('poultry.messages.success.vehicle_created') ?? 'تم إنشاء سيارة النقل بنجاح.');
    }

    public function show(Request $request, PoultryTransportVehicle $transport_vehicle): JsonResponse|View
    {
        $transport_vehicle->load([
            'farm:id,name',
            'rentals' => fn($q) => $q->latest('started_at'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $transport_vehicle,
            ]);
        }

        $vehicle = $transport_vehicle;
        $currentLocale = $request->route('locale') ?? app()->getLocale();

        return view('dashboard.customer.poultry.vehicles.show', compact('vehicle', 'currentLocale'));
    }

    public function update(TransportVehicleStoreRequest $request, PoultryTransportVehicle $transport_vehicle): JsonResponse|RedirectResponse
    {
        $transport_vehicle->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle updated successfully',
                'data'    => $transport_vehicle->load('farm:id,name'),
            ]);
        }

        $locale = $request->route('locale') ?? app()->getLocale();

        return redirect()
            ->route('customer.poultry.vehicles.show', ['locale' => $locale, 'vehicle' => $transport_vehicle->id])
            ->with('success', __('poultry.messages.success.vehicle_updated') ?? 'تم تحديث بيانات السيارة بنجاح.');
    }

    public function destroy(Request $request, PoultryTransportVehicle $transport_vehicle): JsonResponse|RedirectResponse
    {
        $hasActiveRentals = $transport_vehicle->rentals()->where('payment_status', 'pending')->exists();

        if ($hasActiveRentals) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete vehicle with active rental trips.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return back()->with('error', 'لا يمكن حذف السيارة لوجود رحلات أو مستحقات نشطة مرتبطة بها.');
        }

        $transport_vehicle->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle deleted successfully',
            ]);
        }

        $locale = $request->route('locale') ?? app()->getLocale();

        return redirect()
            ->route('customer.poultry.vehicles.index', ['locale' => $locale])
            ->with('success', __('poultry.messages.success.vehicle_deleted') ?? 'تم حذف السيارة بنجاح.');
    }

    public function indexRentals(Request $request): JsonResponse|View
    {
        $rentals = PoultryVehicleRental::query()
            ->with(['vehicle:id,plate_number,driver_name'])
            ->when($request->filled('vehicle_id'), fn($q) => $q->where('vehicle_id', $request->integer('vehicle_id')))
            ->when($request->filled('status'), fn($q) => $q->where('payment_status', $request->string('status')))
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('started_at', '>=', $request->date('start_date')))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('started_at', '<=', $request->date('end_date')))
            ->latest('started_at')
            ->paginate($request->integer('per_page', 15));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $rentals,
            ]);
        }

        $currentLocale = $request->route('locale') ?? app()->getLocale();

        return view('dashboard.customer.poultry.vehicle_rentals.index', compact('rentals', 'currentLocale'));
    }

    public function createRental(Request $request, string $locale): View
    {
        $vehicles = PoultryTransportVehicle::query()
            ->orderBy('plate_number')
            ->get();

        $currentLocale = $locale;

        return view('dashboard.customer.poultry.vehicle_rentals.create', compact('vehicles', 'currentLocale'));
    }

    public function storeRental(VehicleRentalStoreRequest $request, string $locale): JsonResponse|RedirectResponse
    {
        $rental = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['tenant_id'] = auth()->user()->tenant_id ?? null;

            $rental = PoultryVehicleRental::create($data);

            $userId = auth()->id() ?? 1;
            $this->financialService->recordRentalJournalEntry($rental, $userId);

            return $rental;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle rental recorded and financial entry posted successfully',
                'data'    => $rental->load('vehicle:id,plate_number,driver_name'),
            ], Response::HTTP_CREATED);
        }

        return redirect()
            ->route('customer.poultry.vehicles.show', ['locale' => $locale, 'vehicle' => $rental->vehicle_id])
            ->with('success', __('poultry.messages.success.sale_recorded') ?? 'تم تسجيل عملية النقل وترحيل القيود بنجاح.');
    }

    public function updateRental(VehicleRentalStoreRequest $request, string $locale, PoultryVehicleRental $rental): JsonResponse|RedirectResponse
    {
        DB::transaction(function () use ($request, $rental) {
            $rental->update($request->validated());

            if (method_exists($this->financialService, 'syncRentalJournalEntry')) {
                $this->financialService->syncRentalJournalEntry($rental, auth()->id() ?? 1);
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle rental updated successfully',
                'data'    => $rental->load('vehicle:id,plate_number,driver_name'),
            ]);
        }

        return redirect()
            ->route('customer.poultry.vehicles.show', ['locale' => $locale, 'vehicle' => $rental->vehicle_id])
            ->with('success', __('poultry.messages.success.vehicle_updated') ?? 'تم تحديث بيانات التأجير بنجاح.');
    }

    public function destroyRental(Request $request, string $locale, PoultryVehicleRental $rental): JsonResponse|RedirectResponse
    {
        $vehicleId = $rental->vehicle_id;

        DB::transaction(function () use ($rental) {
            if (method_exists($this->financialService, 'reverseRentalJournalEntry')) {
                $this->financialService->reverseRentalJournalEntry($rental, auth()->id() ?? 1);
            }

            $rental->delete();
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle rental deleted and financial entry reversed successfully',
            ]);
        }

        return redirect()
            ->route('customer.poultry.vehicles.show', ['locale' => $locale, 'vehicle' => $vehicleId])
            ->with('success', __('poultry.messages.success.vehicle_deleted') ?? 'تم حذف الرحلة بنجاح.');
    }

    public function financialSummary(Request $request, string $locale): JsonResponse|View
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
            'vehicle_id' => ['nullable', 'integer', 'exists:poultry_transport_vehicles,id'],
        ]);

        $summary = $this->financialService->calculateRentalProfitLoss(
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('vehicle_id')
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $summary,
            ]);
        }

        $rentals = PoultryVehicleRental::query()->with('vehicle')->get();
        $totalRevenue = $rentals->sum('rental_fee');
        $totalExpenses = $rentals->sum(fn($r) => (float)$r->fuel_cost + (float)$r->driver_commission + (float)$r->other_expenses);
        $netProfit = $totalRevenue - $totalExpenses;
        $currentLocale = $locale;

        return view('dashboard.customer.poultry.vehicle_rentals.financial_summary', compact('rentals', 'totalRevenue', 'totalExpenses', 'netProfit', 'currentLocale'));
    }
}