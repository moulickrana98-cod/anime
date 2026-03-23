<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * List all orders with filters.
     */
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(20)->withQueryString();
        $statuses = [
            Order::STATUS_PENDING,
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPED,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
            Order::STATUS_REFUNDED,
        ];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Show a single order's details.
     */
    public function show(Order $order)
    {
        $order->load('user', 'items.product');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $allowed = $order->getAllowedStatusTransitions();

        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', $allowed)],
        ]);

        $order->update(['status' => $request->status]);

        // TODO: Send email notification to customer about status change.

        return back()->with('success', "Order #{$order->order_number} status updated to " . ucfirst($request->status) . '.');
    }

    /**
     * Required by Route::resource — proxies to updateStatus.
     */
    public function update(Request $request, Order $order)
    {
        return $this->updateStatus($request, $order);
    }
}
