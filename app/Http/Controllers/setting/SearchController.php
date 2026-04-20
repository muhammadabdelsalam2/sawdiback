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
        $q = $request->q;

        if (!$q) {
            return response()->json([]);
        }

        $results = [];

        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */


        // AI Search Example (pseudo-code, replace with actual AI search logic)
        // Start Search Automatic In Google Style
        // if (strlen($q) < 3) {
        //     return [];
        // }
        // $key = "ai_search_" . md5($q);

        // $result = Cache::remember($key, 3600, function () use ($q) {
        //     return OpenAI::chat()->create([
        //         'model' => 'gpt-4o-mini',
        //         'messages' => [
        //             ['role' => 'user', 'content' => $q],
        //         ],
        //     ]);
        // });


        /*
              |--------------------------------------------------------------------------
              | Animals
              |--------------------------------------------------------------------------
        */
        $animals = \App\Models\LivestockAnimal::where('tag_number', 'like', "%{$q}%")
            ->limit(5)
            ->get();
        if ($q == 'animals') {
            $animals = \App\Models\LivestockAnimal::latest()->limit(5)->get();
        } elseif ($q == 'animals:today') {
            $animals = \App\Models\LivestockAnimal::whereDate('created_at', now()->toDateString())->get();
        }
        foreach ($animals as $animal) {
            $results[] = [
                'type' => 'Animal',
                'name' => $animal->tag_number,
                'url' => route('customer.livestock.animals.edit', [
                    'locale' => $locale,
                    'animal' => $animal->id
                ]),
            ];
        }



        /*
        |--------------------------------------------------------------------------
        | ORDERS
        |--------------------------------------------------------------------------
        */
        $orders = \App\Models\Order::where('order_no', 'like', "%{$q}%")
            ->limit(5)
            ->get();
        if ($q == 'orders') {
            $orders = \App\Models\Order::latest()->limit(5)->get();
        }
        foreach ($orders as $order) {
            $results[] = [
                'type' => 'Order',
                'name' => $order->order_no,
                'url' => route('customer.ecommerce.orders.show', [
                    'locale' => $locale,
                    'order' => $order->id
                ]),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | PRODUCTS (example)
        |--------------------------------------------------------------------------
        */
        $products = \App\Models\InventoryProduct::where('name', 'like', "%{$q}%")
            ->limit(5)
            ->get();
        if ($q == 'products') {
            $products = \App\Models\InventoryProduct::latest()->limit(5)->get();
        }
        foreach ($products as $product) {
            $results[] = [
                'type' => 'Product',
                'name' => $product->name,
                'url' => route('customer.inventory.products.edit', [
                    'locale' => $locale,
                    'product' => $product->id
                ]),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json($results);
    }
}
