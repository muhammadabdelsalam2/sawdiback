<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\DTOs\Farmer\FarmerDTO;
use App\Http\Requests\Farmer\StoreFarmerRequest;
use App\Services\Farmer\FarmerService;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    //
    protected $farmerService;
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
        $dto = $request->toDTO();

        $this->farmerService->create($dto);
        // Check if created successfully and redirect with success message

        return redirect()
            ->route('customer.farmers.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', '__(farmer.created_successfully)');

    }
}
