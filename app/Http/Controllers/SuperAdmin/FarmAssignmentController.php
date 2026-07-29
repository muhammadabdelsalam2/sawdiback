<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Farm;
use App\Models\InventoryProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FarmAssignmentController extends Controller
{
    public function employees(): View
    {
        $employees = Employee::query()
            ->with(['farm'])
            ->whereNull('farm_id')
            ->orderByDesc('id')
            ->paginate(20);

        $farmsByTenant = $this->farmsByTenant($employees->pluck('tenant_id')->filter()->unique()->values()->all());

        return view('dashboard.superadmin.farm-assignments.employees', compact('employees', 'farmsByTenant'));
    }

    public function assignEmployee(Request $request, string $locale, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'farm_id' => [
                'required',
                'integer',
                Rule::exists('farms', 'id')->where(fn ($query) => $query->where('tenant_id', $employee->tenant_id)),
            ],
        ]);

        $employee->update(['farm_id' => $validated['farm_id']]);

        return redirect()->back()->with('success', __('superadmin.farm_assignments.employee_assigned'));
    }

    public function products(): View
    {
        $products = InventoryProduct::withoutGlobalScopes()
            ->with('farm')
            ->whereNull('farm_id')
            ->orderByDesc('id')
            ->paginate(20);

        $farmsByTenant = $this->farmsByTenant($products->pluck('tenant_id')->filter()->unique()->values()->all());

        return view('dashboard.superadmin.farm-assignments.products', compact('products', 'farmsByTenant'));
    }

    public function assignProduct(Request $request, string $locale, int $product): RedirectResponse
    {
        $product = InventoryProduct::withoutGlobalScopes()->findOrFail($product);

        $validated = $request->validate([
            'farm_id' => [
                'required',
                'integer',
                Rule::exists('farms', 'id')->where(fn ($query) => $query->where('tenant_id', $product->tenant_id)),
            ],
        ]);

        $product->update(['farm_id' => $validated['farm_id']]);

        return redirect()->back()->with('success', __('superadmin.farm_assignments.product_assigned'));
    }

    private function farmsByTenant(array $tenantIds)
    {
        return Farm::withoutGlobalScopes()
            ->whereIn('tenant_id', $tenantIds)
            ->orderBy('name')
            ->get()
            ->groupBy('tenant_id');
    }
}
