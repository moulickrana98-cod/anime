@extends('layouts.admin')
@section('title', 'Products')
@section('page-title', 'Products')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-sm text-gray-500">{{ $products->total() }} total products</p>
    </div>
    <a href="{{ route('admin.products.create') }}"
       class="bg-primary hover:bg-primary-dark text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition flex items-center gap-2">
        <i class="fas fa-plus"></i> Add Product
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="text-xs font-semibold text-gray-500 block mb-1">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or SKU..."
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
    </div>
    <div class="min-w-40">
        <label class="text-xs font-semibold text-gray-500 block mb-1">Category</label>
        <select name="category" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary bg-white">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="min-w-36">
        <label class="text-xs font-semibold text-gray-500 block mb-1">Status</label>
        <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary bg-white">
            <option value="">All</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <button type="submit" class="bg-primary text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-primary-dark transition">
        Filter
    </button>
    @if(request()->hasAny(['search','category','status']))
        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-400 hover:text-primary py-2">Clear</a>
    @endif
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Product</th>
                    <th class="px-5 py-3 text-left">Category</th>
                    <th class="px-5 py-3 text-right">Price</th>
                    <th class="px-5 py-3 text-right">Stock</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Featured</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $product->thumbnail }}"
                                 class="w-10 h-10 rounded-lg object-cover border border-gray-100"
                                 onerror="this.src='https://placehold.co/40x40/e0e7ff/6366f1?text=A'">
                            <div>
                                <p class="font-semibold text-gray-800 max-w-xs truncate">{{ $product->name }}</p>
                                @if($product->sku)
                                    <p class="text-xs text-gray-400 font-mono">{{ $product->sku }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $product->category->name }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-900">{{ $product->formatted_price }}</td>
                    <td class="px-5 py-3 text-right">
                        <span class="font-semibold {{ $product->stock === 0 ? 'text-red-500' : ($product->stock < 10 ? 'text-orange-500' : 'text-green-600') }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($product->is_active)
                            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Active</span>
                        @else
                            <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-2 py-1 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($product->is_featured)
                            <span class="text-amber-500 text-sm">⭐</span>
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.products.show', $product) }}"
                               class="text-gray-400 hover:text-primary transition" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="text-gray-400 hover:text-blue-500 transition" title="Edit">
                                <i class="fas fa-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                        No products found.
                        <a href="{{ route('admin.products.create') }}" class="text-primary font-semibold ml-1">Add one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
