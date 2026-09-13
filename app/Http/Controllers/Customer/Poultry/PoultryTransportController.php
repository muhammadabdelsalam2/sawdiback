<?php

namespace App\Http\Controllers\Customer\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Poultry\TransportVehicleStoreRequest;
use App\Http\Requests\Poultry\VehicleRentalStoreRequest;
use App\Models\Poultry\PoultryTransportVehicle;
use App\Models\Poultry\PoultryVehicleRental;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Poultry\PoultryFinancialService;

class PoultryTransportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vehicles = PoultryTransportVehicle::with(['farm', 'rentals'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $vehicles,
        ]);
    }

    public function store(TransportVehicleStoreRequest $request): JsonResponse
    {
        $vehicle = PoultryTransportVehicle::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Vehicle created successfully',
            'data'    => $vehicle->load('farm'),
        ], 201);
    }

    public function show(PoultryTransportVehicle $transport_vehicle): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $transport_vehicle->load(['farm', 'rentals']),
        ]);
    }

    public function update(TransportVehicleStoreRequest $request, PoultryTransportVehicle $transport_vehicle): JsonResponse
    {
        $transport_vehicle->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Vehicle updated successfully',
            'data'    => $transport_vehicle->load('farm'),
        ]);
    }

    public function destroy(PoultryTransportVehicle $transport_vehicle): JsonResponse
    {
        $transport_vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle deleted successfully',
        ]);
    }

    public function indexRentals(Request $request): JsonResponse
    {
        $rentals = PoultryVehicleRental::with('vehicle')
            ->latest('started_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $rentals,
        ]);
    }

   public function storeRental(VehicleRentalStoreRequest $request, PoultryFinancialService $financialService): JsonResponse
    {
        $rental = PoultryVehicleRental::create($request->validated());

        // ترحيل القيد اليومي المالي للرحلة تلقائياً
        $financialService->recordRentalJournalEntry($rental, auth()->id() ?? 1);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle rental recorded and financial entry posted successfully',
            'data'    => $rental->load('vehicle'),
        ], 201);
    }
    public function updateRental(VehicleRentalStoreRequest $request, PoultryVehicleRental $rental): JsonResponse
    {
        $rental->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Vehicle rental updated successfully',
            'data'    => $rental->load('vehicle'),
        ]);
    }

    public function destroyRental(PoultryVehicleRental $rental): JsonResponse
    {
        $rental->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle rental deleted successfully',
        ]);
    }
    public function financialSummary(Request $request, \App\Services\Poultry\PoultryFinancialService $financialService): JsonResponse
    {
        $summary = $financialService->calculateRentalProfitLoss(
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('vehicle_id')
        );

        return response()->json([
            'success' => true,
            'data'    => $summary,
        ]);
    }
}