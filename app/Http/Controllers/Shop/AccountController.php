<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;

class AccountController extends Controller
{
    /**
     * Show the authenticated user's order history.
     */
    public function orders()
    {
        $orders = auth()->user()
            ->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('shop.account.orders', compact('orders'));
    }

    /**
     * Show a single order — only if it belongs to the current user.
     */
    public function showOrder(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items.product');
        return view('shop.account.order-detail', compact('order'));
    }
}
