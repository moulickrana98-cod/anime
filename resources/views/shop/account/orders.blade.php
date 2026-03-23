@extends('layouts.shop')
@section('title', 'My Orders — AniStore')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8">📦 My Orders</h1>

    @if($orders->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm text-center py-20">
            <div class="text-6xl mb-4">📦</div>
            <p class="text-xl font-bold text-gray-800 mb-2">No orders yet</p>
            <p class="text-gray-500 mb-8">Your order history will appear here.</p>
            <a href="{{ route('shop.index') }}" class="bg-primary text-white font-bold px-8 py-3 rounded-xl hover:bg-primary-dark transition">Start Shopping</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div>
                        <p class="font-mono text-sm font-bold text-primary">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @php $badge = $order->status_badge; @endphp
                        <span class="text-xs font-bold px-3 py-1 rounded-full {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        <span class="font-bold text-gray-900">{{ $order->formatted_total }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 mb-4">
                    @foreach($order->items->take(3) as $item)
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                        <span class="text-xs text-gray-600 font-medium">{{ $item->product_name }}</span>
                        <span class="text-xs text-gray-400">×{{ $item->quantity }}</span>
                    </div>
                    @endforeach
                    @if($order->items->count() > 3)
                        <div class="bg-gray-50 rounded-lg px-3 py-2 text-xs text-gray-400">+{{ $order->items->count() - 3 }} more</div>
                    @endif
                </div>
                <a href="{{ route('account.orders.show', $order) }}" class="text-sm text-primary font-semibold hover:underline">View details →</a>
            </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
