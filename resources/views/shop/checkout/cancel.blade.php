@extends('layouts.shop')
@section('title', 'Payment Cancelled — AniStore')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-20 text-center">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-10">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-times-circle text-4xl text-red-400"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 mb-3">Payment Not Completed</h1>
        <p class="text-gray-500 mb-8">
            Your payment was cancelled or failed. Your cart is still saved — you can try again at any time.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('checkout.index') }}" class="bg-primary text-white font-bold px-8 py-3 rounded-xl hover:bg-primary-dark transition">
                Try Again
            </a>
            <a href="{{ route('cart.index') }}" class="border border-gray-200 text-gray-700 font-semibold px-8 py-3 rounded-xl hover:bg-gray-50 transition">
                Back to Cart
            </a>
        </div>
    </div>
</div>
@endsection
