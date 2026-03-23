@extends('layouts.shop')

@section('title', isset($category) ? $category->name . ' — AniStore' : 'Shop All — AniStore')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-primary">Home</a>
        <span class="mx-2">/</span>
        @if(isset($category))
            <a href="{{ route('shop.index') }}" class="hover:text-primary">Shop</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 font-medium">{{ $category->name }}</span>
        @else
            <span class="text-gray-800 font-medium">Shop All</span>
        @endif
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ── Sidebar Filters ─────────────────────────────────────────────── --}}
        <aside class="lg:w-60 flex-shrink-0">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 sticky top-20">
                <h3 class="font-bold text-gray-800 mb-4">Filters</h3>
                <form method="GET" action="{{ route('shop.index') }}" id="filterForm">

                    {{-- Search --}}
                    <div class="mb-5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Search</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Product name..."
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                    </div>

                    {{-- Category --}}
                    <div class="mb-5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Category</label>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} class="text-primary" onchange="this.form.submit()">
                                All Categories
                            </label>
                            @foreach($categories as $cat)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="category" value="{{ $cat->slug }}"
                                       {{ request('category') === $cat->slug ? 'checked' : '' }}
                                       class="text-primary" onchange="this.form.submit()">
                                <span class="flex-1">{{ $cat->name }}</span>
                                <span class="text-gray-400 text-xs">({{ $cat->products_count }})</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Price Range --}}
                    <div class="mb-5">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Price Range</label>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min ₹"
                                   class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:border-primary">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max ₹"
                                   class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:border-primary">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-primary text-white text-sm font-semibold py-2 rounded-lg hover:bg-primary-dark transition">
                        Apply Filters
                    </button>
                    @if(request()->hasAny(['q','category','min_price','max_price']))
                        <a href="{{ route('shop.index') }}" class="block text-center text-xs text-gray-400 hover:text-primary mt-2">Clear filters</a>
                    @endif
                </form>
            </div>
        </aside>

        {{-- ── Product Grid ─────────────────────────────────────────────────── --}}
        <div class="flex-1">
            {{-- Sort + count bar --}}
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-gray-500">
                    Showing <span class="font-semibold text-gray-800">{{ $products->total() }}</span> products
                </p>
                <form method="GET" action="{{ route('shop.index') }}">
                    @foreach(request()->except('sort') as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <select name="sort" onchange="this.form.submit()"
                            class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:border-primary bg-white">
                        <option value="">Featured</option>
                        <option value="newest"     {{ request('sort') === 'newest'     ? 'selected' : '' }}>Newest</option>
                        <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Price: Low → High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                    </select>
                </form>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
                    <div class="text-6xl mb-4">🔍</div>
                    <p class="text-gray-600 font-semibold text-lg mb-2">No products found</p>
                    <p class="text-gray-400 text-sm mb-6">Try adjusting your filters or search term.</p>
                    <a href="{{ route('shop.index') }}" class="bg-primary text-white px-6 py-2 rounded-lg font-semibold text-sm hover:bg-primary-dark transition">Clear Filters</a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        @include('shop.products._card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
