@extends('layouts.shop')
@section('title', 'Order Confirmed — AniStore')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-20 text-center">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-10">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check-circle text-4xl text-green-500"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 mb-3">Order Confirmed! 🎉</h1>
        <p class="text-gray-500 mb-2">Thank you for your purchase!</p>
        <p class="text-sm text-gray-400 mb-8">
            Order #<span class="font-mono font-bold text-gray-700">{{ $order->order_number }}</span>
        </p>

        {{-- Order quick summary --}}
        <div class="bg-gray-50 rounded-xl p-5 text-left mb-8 space-y-2">
            @foreach($order->items as $item)
            <div class="flex justify-between text-sm">
                <span class="text-gray-700">{{ $item->product_name }} × {{ $item->quantity }}</span>
                <span class="font-semibold">₹{{ number_format($item->total_price, 2) }}</span>
            </div>
            @endforeach
            <hr class="border-gray-200 my-2">
            <div class="flex justify-between font-bold text-gray-900">
                <span>Total Paid</span>
                <span>{{ $order->formatted_total }}</span>
            </div>
        </div>

        <p class="text-sm text-gray-500 mb-8">A confirmation will be sent to <strong>{{ $order->shipping_email }}</strong>.</p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('account.orders') }}" class="bg-primary text-white font-bold px-8 py-3 rounded-xl hover:bg-primary-dark transition">
                View My Orders
            </a>
            <a href="{{ route('shop.index') }}" class="border border-gray-200 text-gray-700 font-semibold px-8 py-3 rounded-xl hover:bg-gray-50 transition">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
