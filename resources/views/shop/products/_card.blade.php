{{-- Reusable product card. Pass $product (Product model instance). --}}
<div class="group bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg hover:border-primary/30 transition duration-200 flex flex-col">

    {{-- Image --}}
    <a href="{{ route('shop.show', $product) }}" class="block relative overflow-hidden bg-gray-100 aspect-square">
        <img src="{{ $product->thumbnail }}"
             alt="{{ $product->name }}"
             class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
             onerror="this.src='https://placehold.co/400x400/e0e7ff/6366f1?text=Anime'">

        {{-- Badges --}}
        <div class="absolute top-2 left-2 flex flex-col gap-1">
            @if($product->is_featured)
                <span class="bg-amber-400 text-white text-xs font-bold px-2 py-0.5 rounded-full">⭐ Featured</span>
            @endif
            @if($product->on_sale)
                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $product->discount_percent }}% OFF</span>
            @endif
            @if(!$product->isInStock())
                <span class="bg-gray-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">Out of Stock</span>
            @endif
        </div>
    </a>

    {{-- Info --}}
    <div class="p-4 flex flex-col flex-1">
        <a href="{{ route('shop.category', $product->category) }}" class="text-xs text-primary font-semibold uppercase tracking-wide mb-1">
            {{ $product->category->name }}
        </a>
        <a href="{{ route('shop.show', $product) }}" class="font-semibold text-gray-800 hover:text-primary transition leading-snug line-clamp-2 mb-2 flex-1">
            {{ $product->name }}
        </a>

        {{-- Price --}}
        <div class="flex items-center gap-2 mb-3">
            <span class="text-lg font-bold text-gray-900">{{ $product->formatted_price }}</span>
            @if($product->on_sale)
                <span class="text-sm text-gray-400 line-through">₹{{ number_format($product->compare_price, 2) }}</span>
            @endif
        </div>

        {{-- Add to Cart --}}
        @if($product->isInStock())
        <form action="{{ route('cart.add', $product) }}" method="POST">
            @csrf
            <input type="hidden" name="quantity" value="1">
            <button type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white font-semibold text-sm py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
        </form>
        @else
        <button disabled class="w-full bg-gray-200 text-gray-400 font-semibold text-sm py-2.5 rounded-xl cursor-not-allowed">
            Out of Stock
        </button>
        @endif
    </div>
</div>
