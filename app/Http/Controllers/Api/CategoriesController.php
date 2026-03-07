<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\API\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class CategoriesController extends Controller
{
    // Function Get All Categories
    public function __construct(
        protected CategoryService $categoryService
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $this->categoryService->all()
        ]);
    }


}
