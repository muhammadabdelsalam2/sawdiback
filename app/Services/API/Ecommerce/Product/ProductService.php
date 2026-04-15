<?php

namespace App\Services\API\Ecommerce\Product;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\InventoryProduct;
use App\Repositories\Api\Product\ProductRepository;
use App\Repositories\Contracts\Api\ProductRepositoryInterface;
use App\Support\ServiceResult;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ProductService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get all active products
     */
    public function getAllProducts(array $filters = [], int $perPage = 15)
    {

        $products = $this->productRepository->all($filters, $perPage);

        $productsResource = ProductResource::collection($products);

        return ServiceResult::success(
            data: $productsResource,
            message: __('ecommerce.product.success'),
            code: 200
        );
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory(Category $category, int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->byCategory($category, $perPage);
    }

    /**
     * Search products
     */
    public function searchProducts(?string $query, int $perPage = 15): LengthAwarePaginator
    {
        if (empty($query)) {
            return $this->getAllProducts($perPage);
        }

        return $this->productRepository->search($query, $perPage);
    }


    public function filterProducts(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $filters['q'] ?? null;
        $category = $filters['category'] ?? null;

        if ($query) {
            return $this->productRepository->search($query, $perPage);
        }

        if ($category instanceof Category) {
            return $this->productRepository->byCategory($category, $perPage);
        }

        return $this->productRepository->all($perPage);
    }

    public function getProductDetails($product): array
    {

        $product = $this->productRepository->findWithDetails($product->id);

        if (!$product) {
            return ServiceResult::error(
                message: 'Product not found',
                errors: [],
                code: 404
            );
        }



        return ServiceResult::success(
            data: new ProductResource($product),
            message: 'Success',
            code: 200
        );
    }



    // Feat: Service For Add Favorite Product
    public function addToFavorites($user, $productId): array
    {
        // get Product By Id
        $product = $this->productRepository->findWithDetails($productId);


        if (!$product) {
            return ServiceResult::error(
                message: __('ecommerce.product.not_found'),
                errors: [],
                code: 404
            );
        }

        // Check if already in favorites
        $existingFavorite = $user->favoriteProducts()->where('inventory_product_id', $productId)->first();
        if ($existingFavorite) {

            return ServiceResult::error(
                message: __('ecommerce.product.already_favorite'),
                errors: [],
                code: 400
            );
        } else {
            // Add to favorites
            $user->favoriteProducts()->attach($productId);
            return ServiceResult::success(
                data: new ProductResource($product),
                message: __('ecommerce.product.favorite_added'),
                code: 200
            );
        }

    }

    public function removeFromFavorites($user, $productId): array
    {
        $favorite = $user->favoriteProducts()->where('inventory_product_id', $productId)->first();

        if (!$favorite) {
            return ServiceResult::error(
                message: __('ecommerce.product.not_found'),
                errors: [],
                code: 404
            );
        }

        $user->favoriteProducts()->detach($productId);

        return ServiceResult::success(
            message: __('ecommerce.product.favorite_removed'),
            code: 200
        );
    }



}