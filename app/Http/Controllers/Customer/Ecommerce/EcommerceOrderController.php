<?php

namespace App\Http\Controllers\Customer\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EcommerceOrderController extends Controller
{
    public function index(Request $request, string $locale): View
    {
        $tenantId = $request->user()->tenant_id;
        /*
         ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId)) :
         We Remove tenant_id filter to allow users
          see all their orders across tenants if they have multiple tenants.
           We will handle tenant scoping in the repository layer instead.
        */
        $query = Order::query()->with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest('id')->paginate(15);

        $statuses = [
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_PREPARING,
            Order::STATUS_SHIPPED,
            Order::STATUS_OUT_FOR_DELIVERY,
            Order::STATUS_DELIVERED,
            Order::STATUS_REJECTED,
            Order::STATUS_CANCELLED,
        ];

        return view('dashboard.customer.ecommerce.orders.index', compact('orders', 'statuses'));
    }

    public function show(Request $request, string $locale, Order $order): View
    {
        $this->authorizeOrder($request, $order);

        $order->load(['items', 'address', 'statusHistories']);

        $statuses = [
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_PREPARING,
            Order::STATUS_SHIPPED,
            Order::STATUS_OUT_FOR_DELIVERY,
            Order::STATUS_DELIVERED,
            Order::STATUS_REJECTED,
            Order::STATUS_CANCELLED,
        ];

        return view('dashboard.customer.ecommerce.orders.show', compact('order', 'statuses'));
    }

    public function updateStatus(Request $request, string $locale, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ]);

        $previous = $order->status;
        $order->update(['status' => $request->input('status')]);

        $order->statusHistories()->create([
            'from_status' => $previous,
            'to_status' => $order->status,
            'changed_at' => now(),
            'changed_by' => $request->user()->id,
            'notes' => $request->input('notes'),
        ]);

        return redirect()
            ->route('customer.ecommerce.orders.show', ['locale' => $locale, 'order' => $order->id])
            ->with('success', 'Order status updated successfully.');
    }

    protected function authorizeOrder(Request $request, Order $order): void
    {
        $tenantId = $request->user()->tenant_id;

        if ($tenantId && $order->tenant_id !== $tenantId) {
            abort(403);
        }
    }
}

