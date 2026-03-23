@extends('layouts.shop')

@section('title', 'AniStore — Your Anime Universe')

@section('content')

{{-- ── Hero Banner ────────────────────────────────────────────────────────── --}}
<section class="relative overflow-hidden bg-gradient-to-br from-gray-900 via-indigo-900 to-purple-900 text-white">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-20 text-9xl">🎌</div>
        <div class="absolute bottom-10 right-20 text-8xl">⚔️</div>
        <div class="absolute top-1/2 left-1/2 text-7xl">🌸</div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-36">
        <div class="max-w-2xl">
            <span class="inline-block bg-anime/20 text-anime font-semibold text-sm px-3 py-1 rounded-full mb-4">✨ New Arrivals Weekly</span>
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-6">
                Your Anime<br>
                <span class="bg-gradient-to-r from-indigo-400 to-pink-400 bg-clip-text text-transparent">Universe Awaits</span>
            </h1>
            <p class="text-lg text-gray-300 mb-8">
                Shop premium figures, apparel, posters and accessories from your favourite anime series. Free shipping on orders over ₹999.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('shop.index') }}"
                   class="bg-primary hover:bg-primary-dark text-white font-bold px-8 py-3 rounded-xl transition shadow-lg shadow-primary/30">
                    Shop Now <i class="fas fa-arrow-right ml-2"></i>
                </a>
                @if($instagramUrl)
                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener"
                   class="border border-white/30 text-white font-semibold px-8 py-3 rounded-xl hover:bg-white/10 transition flex items-center gap-2">
                    <i class="fab fa-instagram text-pink-400"></i> Follow {{ $instagramHandle }}
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ── Category Grid ──────────────────────────────────────────────────────── --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Shop by Category</h2>
        <a href="{{ route('shop.index') }}" class="text-primary font-semibold text-sm hover:underline">View all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $cat)
        <a href="{{ route('shop.category', $cat) }}"
           class="group flex flex-col items-center gap-2 bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-primary/40 hover:shadow-md transition text-center">
            <div class="w-14 h-14 bg-gradient-to-br from-primary/10 to-anime/10 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition">
                🎭
            </div>
            <span class="text-sm font-semibold text-gray-800 group-hover:text-primary transition leading-tight">{{ $cat->name }}</span>
            <span class="text-xs text-gray-400">{{ $cat->products_count }} items</span>
        </a>
        @endforeach
    </div>
</section>

{{-- ── Featured Products ───────────────────────────────────────────────────── --}}
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900">⭐ Featured Products</h2>
            <a href="{{ route('shop.index') }}" class="text-primary font-semibold text-sm hover:underline">See all →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                @include('shop.products._card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>

{{-- ── Instagram CTA ───────────────────────────────────────────────────────── --}}
@if($instagramUrl)
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-orange-500 rounded-3xl p-10 text-white text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 text-9xl flex items-center justify-center pointer-events-none">📸</div>
            <div class="relative">
                <i class="fab fa-instagram text-5xl mb-4 block"></i>
                <h2 class="text-3xl font-extrabold mb-3">Follow Us on Instagram</h2>
                <p class="text-white/80 mb-6 text-lg">
                    Get the latest drops, unboxings, and fan content. Join our anime community!
                </p>
                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 bg-white text-pink-600 font-bold px-8 py-3 rounded-xl hover:bg-gray-50 transition shadow-lg">
                    <i class="fab fa-instagram"></i>
                    {{ $instagramHandle ?? 'Follow Now' }}
                </a>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ── New Arrivals ────────────────────────────────────────────────────────── --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-900">🆕 New Arrivals</h2>
        <a href="{{ route('shop.index', ['sort' => 'newest']) }}" class="text-primary font-semibold text-sm hover:underline">View all →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($newArrivals as $product)
            @include('shop.products._card', ['product' => $product])
        @endforeach
    </div>
</section>

{{-- ── USP Banner ──────────────────────────────────────────────────────────── --}}
<section class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        @foreach([
            ['🚚', 'Free Shipping', 'On orders over ₹999'],
            ['🔒', 'Secure Payments', 'Powered by Stripe'],
            ['📦', 'Easy Returns', '7-day return policy'],
            ['⭐', 'Authentic Merch', '100% licensed products'],
        ] as [$icon, $title, $sub])
        <div>
            <div class="text-3xl mb-2">{{ $icon }}</div>
            <div class="font-bold text-sm">{{ $title }}</div>
            <div class="text-gray-400 text-xs mt-1">{{ $sub }}</div>
        </div>
        @endforeach
    </div>
</section>

@endsection
