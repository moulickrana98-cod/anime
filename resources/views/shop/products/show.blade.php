@extends('layouts.shop')

@section('title', $product->name . ' — AniStore')
@section('meta_description', Str::limit(strip_tags($product->description), 160))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-primary">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-primary">Shop</a>
        <span class="mx-2">/</span>
        <a href="{{ route('shop.category', $product->category) }}" class="hover:text-primary">{{ $product->category->name }}</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

        {{-- ── Image Gallery ─────────────────────────────────────────────────── --}}
        <div>
            @php $images = $product->image_urls; @endphp
            <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm aspect-square flex items-center justify-center">
                <img id="mainImage"
                     src="{{ count($images) ? $images[0] : 'https://placehold.co/600x600/e0e7ff/6366f1?text='.urlencode($product->name) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-contain p-4">
            </div>
            @if(count($images) > 1)
            <div class="flex gap-3 mt-4">
                @foreach($images as $i => $img)
                <button onclick="document.getElementById('mainImage').src = '{{ $img }}'"
                        class="w-20 h-20 rounded-xl border-2 border-gray-200 hover:border-primary overflow-hidden transition flex-shrink-0">
                    <img src="{{ $img }}" alt="Image {{ $i+1 }}" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Product Info ───────────────────────────────────────────────────── --}}
        <div>
            <a href="{{ route('shop.category', $product->category) }}"
               class="inline-block bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-3">
                {{ $product->category->name }}
            </a>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-4 leading-tight">{{ $product->name }}</h1>

            {{-- Price --}}
            <div class="flex items-center gap-3 mb-6">
                <span class="text-3xl font-bold text-gray-900">{{ $product->formatted_price }}</span>
                @if($product->on_sale)
                    <span class="text-lg text-gray-400 line-through">₹{{ number_format($product->compare_price, 2) }}</span>
                    <span class="bg-red-100 text-red-700 text-sm font-bold px-2 py-0.5 rounded-full">{{ $product->discount_percent }}% OFF</span>
                @endif
            </div>

            {{-- Stock status --}}
            @if($product->isInStock())
                <p class="text-green-600 font-semibold text-sm mb-6">
                    <i class="fas fa-check-circle mr-1"></i>
                    In Stock — {{ $product->stock }} units available
                </p>
            @else
                <p class="text-red-500 font-semibold text-sm mb-6">
                    <i class="fas fa-times-circle mr-1"></i> Out of Stock
                </p>
            @endif

            {{-- Add to Cart form --}}
            @if($product->isInStock())
            <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-8">
                @csrf
                <div class="flex items-center gap-4">
                    {{-- Quantity Selector --}}
                    <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden">
                        <button type="button" onclick="changeQty(-1)" class="px-4 py-3 text-gray-500 hover:bg-gray-100 font-bold text-lg transition">−</button>
                        <input type="number" id="qtyInput" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                               class="w-16 text-center py-3 font-semibold text-gray-800 focus:outline-none border-none">
                        <button type="button" onclick="changeQty(1)" class="px-4 py-3 text-gray-500 hover:bg-gray-100 font-bold text-lg transition">+</button>
                    </div>
                    <button type="submit"
                            class="flex-1 bg-primary hover:bg-primary-dark text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </div>
            </form>
            @endif

            {{-- Description --}}
            <div class="bg-gray-50 rounded-xl p-5 mb-6">
                <h3 class="font-bold text-gray-800 mb-2">Product Description</h3>
                <p class="text-gray-600 text-sm leading-relaxed">{{ $product->description }}</p>
            </div>

            {{-- Tags --}}
            @if($product->tags && count($product->tags))
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($product->tags as $tag)
                    <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full font-medium">#{{ $tag }}</span>
                @endforeach
            </div>
            @endif

            {{-- SKU --}}
            @if($product->sku)
            <p class="text-xs text-gray-400">SKU: <span class="font-mono">{{ $product->sku }}</span></p>
            @endif

            {{-- Share Instagram --}}
            @if(env('INSTAGRAM_URL'))
            <div class="mt-6 flex items-center gap-3 text-sm text-gray-500">
                <span>Share the love:</span>
                <a href="{{ env('INSTAGRAM_URL') }}" target="_blank" rel="noopener"
                   class="flex items-center gap-1.5 text-pink-600 font-semibold hover:underline">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Related Products ────────────────────────────────────────────────── --}}
    @if($related->isNotEmpty())
    <section class="mt-20">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">You May Also Like</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($related as $rel)
                @include('shop.products._card', ['product' => $rel])
            @endforeach
        </div>
    </section>
    @endif

</div>

@push('scripts')
<script>
function changeQty(delta) {
    const input = document.getElementById('qtyInput');
    const max   = parseInt(input.max);
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value = val;
}
</script>
@endpush

@endsection
