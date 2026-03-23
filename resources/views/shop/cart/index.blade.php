@extends('layouts.shop')

@section('title', 'Shopping Cart — AniStore')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-3xl font-extrabold text-gray-900 mb-8">🛒 Your Cart</h1>

    @if(empty($cart))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm text-center py-20">
            <div class="text-6xl mb-4">🛒</div>
            <p class="text-xl font-bold text-gray-800 mb-2">Your cart is empty</p>
            <p class="text-gray-500 mb-8">Looks like you haven't added anything yet.</p>
            <a href="{{ route('shop.index') }}" class="bg-primary text-white font-bold px-8 py-3 rounded-xl hover:bg-primary-dark transition">
                Start Shopping
            </a>
        </div>
    @else

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── Cart Items ──────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">
            @foreach($cart as $rowId => $item)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex gap-4 items-start">
                <img src="{{ $item['image'] }}"
                     alt="{{ $item['name'] }}"
                     class="w-20 h-20 object-cover rounded-xl border border-gray-100 flex-shrink-0"
                     onerror="this.src='https://placehold.co/80x80/e0e7ff/6366f1?text=Ani'">

                <div class="flex-1">
                    <a href="{{ route('shop.show', $item['slug']) }}" class="font-semibold text-gray-800 hover:text-primary transition line-clamp-2">
                        {{ $item['name'] }}
                    </a>
                    <p class="text-primary font-bold mt-1">₹{{ number_format($item['price'], 2) }}</p>

                    <div class="flex items-center gap-4 mt-3">
                        {{-- Qty update --}}
                        <form action="{{ route('cart.update', $rowId) }}" method="POST" class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                            @csrf @method('PATCH')
                            <button type="button" onclick="changeCartQty(this, -1)" class="px-3 py-1.5 text-gray-500 hover:bg-gray-100 font-bold">−</button>
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}"
                                   class="w-12 text-center text-sm font-semibold border-none focus:outline-none"
                                   onchange="this.form.submit()">
                            <button type="button" onclick="changeCartQty(this, 1)" class="px-3 py-1.5 text-gray-500 hover:bg-gray-100 font-bold">+</button>
                        </form>

                        {{-- Remove --}}
                        <form action="{{ route('cart.remove', $rowId) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium transition">
                                <i class="fas fa-trash-alt mr-1"></i> Remove
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-right flex-shrink-0">
                    <p class="font-bold text-gray-900">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                </div>
            </div>
            @endforeach

            <div class="flex justify-between items-center pt-2">
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-gray-400 hover:text-red-500 transition font-medium">
                        <i class="fas fa-trash mr-1"></i> Clear Cart
                    </button>
                </form>
                <a href="{{ route('shop.index') }}" class="text-sm text-primary font-semibold hover:underline">
                    ← Continue Shopping
                </a>
            </div>
        </div>

        {{-- ── Order Summary ────────────────────────────────────────────────── --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-20">
                <h2 class="text-lg font-bold text-gray-900 mb-5">Order Summary</h2>

                @php
                    $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
                    $shipping = $subtotal >= 999 ? 0 : 99;
                    $tax      = round($subtotal * 0.18, 2);
                    $total    = $subtotal + $tax + $shipping;
                @endphp

                <div class="space-y-3 text-sm mb-5">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium">₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>GST (18%)</span>
                        <span class="font-medium">₹{{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span class="font-medium {{ $shipping == 0 ? 'text-green-600 font-semibold' : '' }}">
                            {{ $shipping == 0 ? 'FREE' : '₹' . number_format($shipping, 2) }}
                        </span>
                    </div>
                    @if($shipping > 0)
                    <p class="text-xs text-gray-400 bg-gray-50 px-3 py-2 rounded-lg">Add ₹{{ number_format(999 - $subtotal, 2) }} more for free shipping!</p>
                    @endif
                    <hr class="border-gray-100">
                    <div class="flex justify-between font-bold text-gray-900 text-base">
                        <span>Total</span>
                        <span>₹{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                @auth
                    <a href="{{ route('checkout.index') }}"
                       class="block w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 rounded-xl transition text-center shadow-lg shadow-primary/20">
                        Proceed to Checkout <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}?redirect={{ route('checkout.index') }}"
                       class="block w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 rounded-xl transition text-center">
                        Login to Checkout
                    </a>
                @endauth

                <p class="text-center text-xs text-gray-400 mt-3">
                    <i class="fas fa-lock mr-1"></i> Secured by Stripe
                </p>
            </div>
        </div>

    </div>
    @endif
</div>

@push('scripts')
<script>
function changeCartQty(btn, delta) {
    const form  = btn.closest('form');
    const input = form.querySelector('input[name="quantity"]');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > parseInt(input.max)) val = parseInt(input.max);
    input.value = val;
    form.submit();
}
</script>
@endpush

@endsection
