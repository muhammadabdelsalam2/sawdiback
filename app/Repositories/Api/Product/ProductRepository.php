<?php

namespace App\Repositories\Api\Product;

use App\Models\Category;
use App\Models\InventoryProduct;
use App\Repositories\Contracts\Api\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    private function favoritesEagerLoad(): array
{
    return ['favoriteProducts' => function ($query) {
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        }
    }];
}


    public function all(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = InventoryProduct::query()->with($this->favoritesEagerLoad()); ;
        // Apply Filters By Favorite, Search, Category, Price Range

        $this->applyFavoriteFilter($query, $filters);


        $this->applyStockCalculations($query);

        $this->applyLastPrice($query);

        $this->applyBaseFilters($query, $filters);

        // 4️⃣ Paginate & calculate available quantity & price
        return $query->paginate($perPage)
            ->through(function ($product) {
                $product->available_quantity =
                    ($product->movements->where('movement_type', 'in')->sum('quantity') ?? 0)
                    -
                    ($product->movements->where('movement_type', 'out')->sum('quantity') ?? 0);
                return $product;
            });
    }

    /**
     * Apply "favorites" filter if requested
     */

    private function applyFavoriteFilter(Builder $query, array $filters): void
    {
        if (!empty($filters['favorites']) && auth()->check()) {
            $userId = auth()->id();
            $query->whereHas('favoriteProducts', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
    }
    /**
     * Base filters (tenant + active + search + category + price)
     */
    private function applyBaseFilters(Builder $query, array $filters): void
    {
        $query->where('is_active', true);

        // Search
        $query->when($filters['q'] ?? null, function ($q) use ($filters) {
            $q->where(function ($sub) use ($filters) {
                $sub->where('name', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('title->en', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('title->ar', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('code', 'like', '%' . $filters['q'] . '%');
            });
        });

        // Category
        $query->when($filters['category'] ?? null, function ($q) use ($filters) {
            $q->where('category', $filters['category']);
        });

        // Sorting logic only (NO filtering)

        // if max_price exists → order DESC (high → low)
        if (isset($filters['max_price'])) {
            $query->orderBy('last_price', 'desc');
        }

        // if min_price exists → order ASC (low → high)
        if (isset($filters['min_price'])) {
            $query->orderBy('last_price', 'asc');
        }
    }
    /**
     * Calculate total_in & total_out
     */
    private function applyStockCalculations(Builder $query): void
    {
        $query->withSum([
            'movements as total_in' => fn($q) => $q->where('movement_type', 'in')
        ], 'quantity');

        $query->withSum([
            'movements as total_out' => fn($q) => $q->where('movement_type', 'out')
        ], 'quantity');
    }

    /**
     * Get last price
     */
    private function applyLastPrice(Builder $query): void
    {
        $query->addSelect([
            'last_price' => function ($subQuery) {
                $subQuery->select('unit_cost')
                    ->from('inventory_movements')
                    ->whereColumn('inventory_product_id', 'inventory_products.id')
                    ->whereNotNull('unit_cost')
                    ->latest('movement_date')
                    ->limit(1);
            }
        ]);

    }
    /**
     * Base filters (tenant + active + search + category)
     */
    /**
     * Base filters (tenant + active + search + category + price)
     */



    public function byCategory(Category $category, int $perPage = 15): LengthAwarePaginator
    {
        return InventoryProduct::where('is_active', true)
        ->with($this->favoritesEagerLoad())
            ->where('category', 'vet_medicine')
            ->withSum([
                'movements as total_in' => function ($q) {
                    $q->where('movement_type', 'in');
                }
            ], 'quantity')
            ->withSum([
                'movements as total_out' => function ($q) {
                    $q->where('movement_type', 'out');
                }
            ], 'quantity')
            ->addSelect([
                'last_price' => function ($query) {
                    $query->select('unit_cost')
                        ->from('inventory_movements')
                        ->whereColumn('inventory_product_id', 'inventory_products.id')
                        ->whereNotNull('unit_cost')
                        ->latest('movement_date')
                        ->limit(1);
                }
            ])
            ->paginate($perPage);
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return InventoryProduct::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%")
                    ->orWhere('title->en', 'LIKE', "%$query%")
                    ->orWhere('title->ar', 'LIKE', "%$query%")
                    ->orWhere('code', 'LIKE', "%$query%");
            })
            ->with(array_merge($this->favoritesEagerLoad(), ['movements']))
            ->paginate($perPage)
            ->through(function ($product) {
                $totalIn = $product->movements->where('movement_type', 'IN')->sum('quantity');
                $totalOut = $product->movements->where('movement_type', 'OUT')->sum('quantity');
                $product->available_quantity = $totalIn - $totalOut;
                return $product;
            });
    }

    public function findWithDetails(int $id): ?InventoryProduct
    {
        $query = InventoryProduct::query()->with($this->favoritesEagerLoad());

        // Stock calculations
        $this->applyStockCalculations($query);

        // Last price
        $this->applyLastPrice($query);

        return $query
            ->where('id', $id)
            ->where('is_active', true)
            ->first();
    }



}
