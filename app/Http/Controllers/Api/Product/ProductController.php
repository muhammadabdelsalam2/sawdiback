<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InventoryProduct;
use App\Services\API\Ecommerce\Product\ProductService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Get all products OR search
     */
    public function index(Request $request): JsonResponse
    {

        $request->validate([
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'price_from' => 'nullable|numeric|min:0',
            'price_to' => 'nullable|numeric|min:0',
            'perPage' => 'nullable|integer|min:1',
            'category' => 'nullable|string',
            'q' => 'nullable|string',
        ]);

        // تأكد من جلب قيمة per_page مع القيمة الافتراضية
        $perPage = $request->input('per_page', 15);

        $filters = $request->input('filters', []);
        $products = $this->productService->getAllProducts($filters, $perPage);

        return ApiResponse::success(
            data: $products['data'],   // Resource collection أو paginator
            message: $products['message'],
            code: $products['code']
        );
    }

    /**
     * Get products by category
     */
    public function byCategory(Category $category, Request $request): JsonResponse
    {
        $products = $this->productService->getProductsByCategory(
            $category,
            $request->input('per_page', 15)
        );

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show( $locale , InventoryProduct $product): JsonResponse
    {
        $data = $this->productService->getProductDetails($product);

        return ApiResponse::success(
            data: $data,
            message: __('ecommerce.product.message'),
            code: 200
        );
    }


}