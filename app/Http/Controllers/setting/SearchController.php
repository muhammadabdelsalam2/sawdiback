<?php

namespace App\Http\Controllers\setting;

use App\Http\Controllers\Controller;
use Cache;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class SearchController extends Controller
{
    //
    public function index($locale, Request $request)
    {
        $query = trim($request->q);

        // Return empty response if no query provided
        if (!$query) {
            return response()->json([]);
        }

        $results = [];

        /*
        |--------------------------------------------------------------------------
        | SEARCH ROUTER
        |--------------------------------------------------------------------------
        | Route query to different search handlers based on keywords
        */
        if ($this->isSpecialQuery($query)) {
            $results = array_merge($results, $this->handleSpecialQueries($query, $locale));
        } else {
            $results = array_merge($results, $this->handleNormalSearch($query, $locale));
        }

        return response()->json($results);
    }

    /*
    |--------------------------------------------------------------------------
    | DETECT SPECIAL QUERIES
    |--------------------------------------------------------------------------
    */
    private function isSpecialQuery(string $query): bool
    {
        return str_contains($query, ':')
            || in_array($query, ['animals', 'orders', 'products']);
    }

    /*
    |--------------------------------------------------------------------------
    | HANDLE SPECIAL QUERIES (COMMAND STYLE SEARCH)
    |--------------------------------------------------------------------------
    */
    private function handleSpecialQueries(string $query, string $locale): array
    {
        return array_merge(
            $this->searchAnimals($query, $locale),
            $this->searchOrders($query, $locale),
            $this->searchProducts($query, $locale),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HANDLE NORMAL SEARCH
    |--------------------------------------------------------------------------
    */
    private function handleNormalSearch(string $query, string $locale): array
    {
        return array_merge(
            $this->searchAnimals($query, $locale),
            $this->searchOrders($query, $locale),
            $this->searchProducts($query, $locale),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ANIMALS SEARCH
    |--------------------------------------------------------------------------
    */
    private function searchAnimals(string $query, string $locale): array
    {
        $builder = \App\Models\LivestockAnimal::query();

        if ($query === 'animals') {
            $builder->latest();
        } elseif ($query === 'animals:today') {
            $builder->whereDate('created_at', now()->toDateString());
        } else {
            $builder->where('tag_number', 'like', "%{$query}%");
        }

        return $builder->limit(5)->get()->map(function ($animal) use ($locale) {
            return [
                'type' => 'Animal',
                'name' => $animal->tag_number,
                'url' => route('customer.livestock.animals.edit', [
                    'locale' => $locale,
                    'animal' => $animal->id
                ]),
            ];
        })->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | ORDERS SEARCH
    |--------------------------------------------------------------------------
    */
    private function searchOrders(string $query, string $locale): array
    {
        $builder = \App\Models\Order::query();

        if ($query === 'orders') {
            $builder->latest();
        } elseif ($query === 'orders:today') {
            $builder->whereDate('created_at', now()->toDateString());
        } else {
            $builder->where('order_no', 'like', "%{$query}%");
        }

        return $builder->limit(5)->get()->map(function ($order) use ($locale) {
            return [
                'type' => 'Order',
                'name' => $order->order_no,
                'url' => route('customer.ecommerce.orders.show', [
                    'locale' => $locale,
                    'order' => $order->id
                ]),
            ];
        })->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS SEARCH
    |--------------------------------------------------------------------------
    */
    private function searchProducts(string $query, string $locale): array
    {
        $builder = \App\Models\InventoryProduct::query();

        if ($query === 'products') {
            $builder->latest();
        } elseif ($query === 'products:today') {
            $builder->whereDate('created_at', now()->toDateString());
        } else {
            $builder->where('name', 'like', "%{$query}%");
        }

        return $builder->limit(5)->get()->map(function ($product) use ($locale) {
            return [
                'type' => 'Product',
                'name' => $product->name,
                'url' => route('customer.inventory.products.edit', [
                    'locale' => $locale,
                    'product' => $product->id
                ]),
            ];
        })->toArray();
    }
}
