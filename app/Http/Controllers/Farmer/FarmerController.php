<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\StoreFarmerRequest;
use App\Models\Farmer;
use App\Services\Farmer\FarmerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FarmerController extends Controller
{
    //
    protected FarmerService $farmerService;
    public function __construct(FarmerService $farmerService)
    {
        $this->farmerService = $farmerService;
    }


    public function index()
    {
        // Get all farmers and pass to the view
        $farmers = $this->farmerService->getAllFarmers();
        return view('dashboard.customer.farmer.index', compact('farmers'));
    }


    public function show($id)
    {
        // Placeholder for showing farmer details
        // You can implement this method to retrieve and display details of a specific farmer
        return view('dashboard.customer.farmer.show', compact('id'));
    }


    // Create a new farmer
    public function create()
    {
        // Placeholder for creating a new farmer
        // You can implement this method to show a form for creating a new farmer
        return view('dashboard.customer.farmer.partial.create');
    }

    // Store a new farmer
    public function store(StoreFarmerRequest $request)
    {

        // transaction to ensure data integrity
        try {
            $dto = $request->toDTO();

            $newFarmer = $this->farmerService->create($dto);
            // Check if created successfully and redirect with success message
            // Store Image if exists
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('farmers', 'public');
                $newFarmer->image = $imagePath;
                $newFarmer->save();
            }
            // return Response Json for AJAX request
            if ($newFarmer) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.farmer.created_successfully'),
                    'data' => $newFarmer
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.farmer.creation_failed'),
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.farmer.creation_failed'),
            ], 500);
        }


    }


    // Delete Farmer 

    public function force($locale, Farmer $farmer)
    {
        try {
            DB::beginTransaction();


            $farmer->forceDelete();

            DB::commit();

            return redirect()
                ->route('customer.farmers.index', ['locale' => $locale])
                ->with('success', __('farmer.messages.deleted_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Force delete farmer failed: ' . $e->getMessage());

            return redirect()
                ->route('customer.farmers.index', ['locale' => $locale])
                ->with('error', __('farmer.messages.deletion_failed'));
        }
    }
}
