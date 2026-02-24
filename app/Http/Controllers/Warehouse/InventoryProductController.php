<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\InventoryProductStoreRequest;
use App\Http\Requests\Warehouse\InventoryProductUpdateRequest;
use App\Models\InventoryProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class InventoryProductController extends Controller
{
    public function index(string $locale): View
    {
        $rows = InventoryProduct::query()->orderBy('name')->paginate(15);

        return view('dashboard.warehouse.products.index', compact('rows'));
    }

    public function create(string $locale): View
    {
        return view('dashboard.warehouse.products.create');
    }

    public function store(InventoryProductStoreRequest $request, string $locale): RedirectResponse
    {
        InventoryProduct::query()->create($request->validated());

        return redirect()
            ->route('customer.inventory.products.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Product created successfully.');
    }

    public function edit(string $locale, InventoryProduct $product): View
    {
        return view('dashboard.warehouse.products.edit', compact('product'));
    }

    public function update(InventoryProductUpdateRequest $request, string $locale, InventoryProduct $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()
            ->route('customer.inventory.products.index', ['locale' => session('locale_full', 'en-SA')])
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(string $locale, InventoryProduct $product): RedirectResponse
    {
        try {
            $product->delete();

            return redirect()
                ->route('customer.inventory.products.index', ['locale' => session('locale_full', 'en-SA')])
                ->with('success', 'Product deleted successfully.');
        } catch (Throwable) {
            return redirect()->back()->with('error', 'Product cannot be deleted because it is in use.');
        }
    }
}

