<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\API\CategoryService;
use Illuminate\Http\Request;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
class CategoriesController extends Controller
{
    // Function Get All Categories
    public function __construct(
        protected CategoryService $categoryService
    ) {
    }

 public function index()
{
    $categories = $this->categoryService->all(); // LengthAwarePaginator

    // Return via your ApiResponse helper
    return ApiResponse::success(
        data: $categories['data'],
        message: $categories['message'],
        code: $categories['code']
    );
}

    public function slag()
    {

    }


}
