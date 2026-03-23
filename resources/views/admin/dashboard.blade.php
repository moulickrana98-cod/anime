@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ── Stat Cards ──────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    @php
    $cards = [
        ['label' => 'Total Revenue',   'value' => '₹'.number_format($stats['total_revenue'],2), 'icon' => 'fa-indian-rupee-sign', 'color' => 'bg-indigo-500',  'bg' => 'bg-indigo-50'],
        ['label' => 'Total Orders',    'value' => $stats['total_orders'],    'icon' => 'fa-shopping-bag',  'color' => 'bg-purple-500',  'bg' => 'bg-purple-50'],
        ['label' => 'Total Products',  'value' => $stats['total_products'],  'icon' => 'fa-box-open',       'color' => 'bg-blue-500',    'bg' => 'bg-blue-50'],
        ['label' => 'Total Customers', 'value' => $stats['total_users'],     'icon' => 'fa-users',          'color' => 'bg-green-500',   'bg' => 'bg-green-50'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 {{ $card['bg'] }} rounded-xl flex items-center justify-center">
            <i class="fas {{ $card['icon'] }} {{ str_replace('bg-', 'text-', $card['color']) }} text-lg"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ $card['label'] }}</p>
            <p class="text-2xl font-extrabold text-gray-900">{{ $card['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Alert Badges ─────────────────────────────────────────────────────────── --}}
<div class="flex flex-wrap gap-3 mb-8">
    @if($stats['pending_orders'])
    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
       class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm font-semibold px-4 py-2 rounded-xl hover:bg-yellow-100 transition">
        <i class="fas fa-clock text-yellow-500"></i>
        {{ $stats['pending_orders'] }} Pending Orders
    </a>
    @endif
    @if($stats['low_stock'])
    <a href="{{ route('admin.products.index', ['status' => 'active']) }}"
       class="flex items-center gap-2 bg-orange-50 border border-orange-200 text-orange-800 text-sm font-semibold px-4 py-2 rounded-xl hover:bg-orange-100 transition">
        <i class="fas fa-exclamation-triangle text-orange-500"></i>
        {{ $stats['low_stock'] }} Low Stock Items
    </a>
    @endif
    @if($stats['out_of_stock'])
    <a href="{{ route('admin.products.index') }}"
       class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-800 text-sm font-semibold px-4 py-2 rounded-xl hover:bg-red-100 transition">
        <i class="fas fa-ban text-red-500"></i>
        {{ $stats['out_of_stock'] }} Out of Stock
    </a>
    @endif
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── Recent Orders ────────────────────────────────────────────────────── --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800">Recent Orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-primary font-semibold hover:underline">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">Order</th>
                        <th class="px-6 py-3 text-left">Customer</th>
                        <th class="px-6 py-3 text-left">Total</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-xs text-primary font-semibold hover:underline">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td class="px-6 py-3 text-gray-700">{{ $order->user->name }}</td>
                        <td class="px-6 py-3 font-semibold text-gray-900">{{ $order->formatted_total }}</td>
                        <td class="px-6 py-3">
                            @php $badge = $order->status_badge; @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs">{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Top Products ──────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800">Top Products</h2>
            <a href="{{ route('admin.products.index') }}" class="text-xs text-primary font-semibold hover:underline">View all →</a>
        </div>
        <ul class="divide-y divide-gray-50">
            @forelse($topProducts as $i => $product)
            <li class="px-6 py-4 flex items-center gap-3">
                <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 text-xs font-bold flex items-center justify-center flex-shrink-0">{{ $i+1 }}</span>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('admin.products.show', $product) }}" class="text-sm font-semibold text-gray-800 hover:text-primary truncate block">
                        {{ $product->name }}
                    </a>
                    <p class="text-xs text-gray-400">{{ $product->sold_count ?? 0 }} sold</p>
                </div>
                <span class="text-sm font-bold text-gray-900">{{ $product->formatted_price }}</span>
            </li>
            @empty
            <li class="px-6 py-8 text-center text-gray-400 text-sm">No data yet.</li>
            @endforelse
        </ul>
    </div>

</div>
@endsection
