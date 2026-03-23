<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with key metrics.
     */
    public function index()
    {
        // Summary cards
        $stats = [
            'total_products'   => Product::count(),
            'total_orders'     => Order::count(),
            'total_users'      => User::where('is_admin', false)->count(),
            'total_revenue'    => Order::where('payment_status', 'paid')->sum('total'),
            'pending_orders'   => Order::where('status', Order::STATUS_PENDING)->count(),
            'low_stock'        => Product::where('stock', '<', 10)->where('stock', '>', 0)->count(),
            'out_of_stock'     => Product::where('stock', 0)->count(),
        ];

        // Recent orders (last 10)
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Monthly revenue for chart (last 6 months)
        $monthlyRevenue = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Top selling products
        $topProducts = Product::withCount(['orderItems as sold_count' => function ($q) {
            $q->select(DB::raw('SUM(quantity)'));
        }])
            ->orderByDesc('sold_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'monthlyRevenue', 'topProducts'));
    }
}
