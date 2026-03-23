@extends('layouts.admin')
@section('title', $product->name)
@section('page-title', 'Product Detail')

@section('content')

<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.products.index') }}"
       class="text-sm text-gray-500 hover:text-primary flex items-center gap-1">
        <i class="fas fa-arrow-left text-xs"></i> Back to Products
    </a>
    <div class="flex gap-3">
        <a href="{{ route('admin.products.edit', $product) }}"
           class="bg-primary hover:bg-primary-dark text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fas fa-pencil"></i> Edit Product
        </a>
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
              onsubmit="return confirm('Permanently delete this product?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="bg-red-50 hover:bg-red-100 text-red-600 font-semibold text-sm px-5 py-2.5 rounded-xl transition flex items-center gap-2 border border-red-200">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── Images & Basic Info ──────────────────────────────────────────── --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- Images --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4">Product Images</h2>
            @php $images = $product->image_urls; @endphp
            @if(count($images))
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($images as $img)
                    <div class="aspect-square rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                        <img src="{{ $img }}" alt="{{ $product->name }}"
                             class="w-full h-full object-cover">
                    </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-center aspect-video max-h-48 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border-2 border-dashed border-gray-200">
                    <div class="text-center">
                        <div class="text-5xl mb-2">🖼️</div>
                        <p class="text-sm text-gray-400">No images uploaded</p>
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="text-xs text-primary font-semibold hover:underline mt-1 block">Add images →</a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Description --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-3">Description</h2>
            <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
        </div>

        {{-- Order history for this product --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">
                    Sales History
                    <span class="ml-2 text-sm font-normal text-gray-400">(last 10 orders)</span>
                </h2>
            </div>
            @if($product->orderItems->isNotEmpty())
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">Order #</th>
                        <th class="px-5 py-3 text-right">Qty</th>
                        <th class="px-5 py-3 text-right">Revenue</th>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($product->orderItems->take(10) as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.orders.show', $item->order) }}"
                               class="font-mono text-xs text-primary font-bold hover:underline">
                                {{ $item->order->order_number }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-right font-semibold">{{ $item->quantity }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-900">
                            ₹{{ number_format($item->total_price, 2) }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-400">
                            {{ $item->order->created_at->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            @php $badge = $item->order->status_badge; @endphp
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="px-6 py-10 text-center text-gray-400">
                <div class="text-4xl mb-2">📦</div>
                <p class="text-sm">No orders for this product yet.</p>
            </div>
            @endif
        </div>

    </div>

    {{-- ── Sidebar: Meta & Stats ────────────────────────────────────────── --}}
    <div class="space-y-6">

        {{-- Status badges --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex gap-3 flex-wrap">
            <span class="text-xs font-bold px-3 py-1.5 rounded-full
                {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $product->is_active ? '● Active' : '○ Inactive' }}
            </span>
            @if($product->is_featured)
            <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-amber-100 text-amber-700">
                ⭐ Featured
            </span>
            @endif
            @if(!$product->isInStock())
            <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-red-100 text-red-600">
                ✕ Out of Stock
            </span>
            @endif
        </div>

        {{-- Pricing --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-800 mb-4">Pricing</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Sale Price</span>
                    <span class="text-xl font-extrabold text-gray-900">{{ $product->formatted_price }}</span>
                </div>
                @if($product->compare_price)
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Original Price</span>
                    <span class="font-semibold text-gray-400 line-through">
                        ₹{{ number_format($product->compare_price, 2) }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Discount</span>
                    <span class="font-bold text-red-500">{{ $product->discount_percent }}% OFF</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Inventory --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-800 mb-4">Inventory</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Stock</span>
                    <span class="font-bold
                        {{ $product->stock === 0 ? 'text-red-500' : ($product->stock < 10 ? 'text-orange-500' : 'text-green-600') }}">
                        {{ $product->stock }} units
                    </span>
                </div>
                @if($product->sku)
                <div class="flex justify-between">
                    <span class="text-gray-500">SKU</span>
                    <span class="font-mono text-xs text-gray-700 bg-gray-100 px-2 py-1 rounded">
                        {{ $product->sku }}
                    </span>
                </div>
                @endif
                @if($product->weight)
                <div class="flex justify-between">
                    <span class="text-gray-500">Weight</span>
                    <span class="font-semibold text-gray-700">{{ $product->weight }}g</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Category & Tags --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-800 mb-4">Organisation</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-gray-500 block mb-1">Category</span>
                    <a href="{{ route('admin.categories.edit', $product->category) }}"
                       class="inline-flex items-center gap-1.5 bg-primary/10 text-primary font-semibold text-xs px-3 py-1.5 rounded-full hover:bg-primary/20 transition">
                        <i class="fas fa-tag text-xs"></i>
                        {{ $product->category->name }}
                    </a>
                </div>
                @if($product->tags && count($product->tags))
                <div>
                    <span class="text-gray-500 block mb-2">Tags</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($product->tags as $tag)
                        <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full font-medium">
                            #{{ $tag }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-800 mb-4">Quick Stats</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Sold</span>
                    <span class="font-bold text-gray-900">
                        {{ $product->orderItems->sum('quantity') }} units
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Revenue</span>
                    <span class="font-bold text-gray-900">
                        ₹{{ number_format($product->orderItems->sum('total_price'), 2) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Created</span>
                    <span class="text-gray-600">{{ $product->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Last Updated</span>
                    <span class="text-gray-600">{{ $product->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- View on store --}}
        @if($product->is_active)
        <a href="{{ route('shop.show', $product) }}" target="_blank"
           class="flex items-center justify-center gap-2 w-full border-2 border-primary/30 text-primary font-semibold text-sm py-2.5 rounded-xl hover:bg-primary/5 transition">
            <i class="fas fa-external-link-alt text-xs"></i>
            View on Store
        </a>
        @endif

    </div>
</div>

@endsection
