@extends('layouts.admin')
@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('page-title', isset($product) ? 'Edit: '.$product->name : 'Add New Product')

@section('content')
@php $isEdit = isset($product); @endphp

<form action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- ── Main fields ───────────────────────────────────────────────── --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Basic Info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-5">Basic Information</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Category *</label>
                        <select name="category_id" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-primary @error('category_id') border-red-400 @enderror">
                            <option value="">Select category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Description *</label>
                        <textarea name="description" rows="5" required
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary @error('description') border-red-400 @enderror">{{ old('description', $product->description ?? '') }}</textarea>
                        @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-5">Pricing & Inventory</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Price (₹) *</label>
                        <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}"
                               step="0.01" min="0" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary @error('price') border-red-400 @enderror">
                        @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Compare Price (₹)</label>
                        <input type="number" name="compare_price" value="{{ old('compare_price', $product->compare_price ?? '') }}"
                               step="0.01" min="0"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
                        <p class="text-xs text-gray-400 mt-1">Original price for showing discount</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Stock Qty *</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}"
                               min="0" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary @error('stock') border-red-400 @enderror">
                        @error('stock')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary font-mono @error('sku') border-red-400 @enderror">
                        @error('sku')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Weight (g)</label>
                        <input type="number" name="weight" value="{{ old('weight', $product->weight ?? '') }}"
                               step="0.01" min="0"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tags</label>
                        <input type="text" name="tags"
                               value="{{ old('tags', isset($product) ? implode(', ', $product->tags ?? []) : '') }}"
                               placeholder="naruto, figure, PVC"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
                        <p class="text-xs text-gray-400 mt-1">Comma separated</p>
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-5">Product Images</h3>

                @if($isEdit && $product->images && count($product->images))
                <div class="grid grid-cols-4 gap-3 mb-4">
                    @foreach($product->images as $i => $img)
                    <div class="relative group">
                        <img src="{{ asset('storage/'.$img) }}" class="w-full aspect-square object-cover rounded-xl border border-gray-200">
                        <label class="absolute top-1 right-1 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center cursor-pointer opacity-0 group-hover:opacity-100 transition">
                            <input type="checkbox" name="remove_images[]" value="{{ $i }}" class="sr-only">
                            <i class="fas fa-times text-white text-xs"></i>
                        </label>
                        <p class="text-xs text-center text-gray-400 mt-1">Check to remove</p>
                    </div>
                    @endforeach
                </div>
                @endif

                <label class="block border-2 border-dashed border-gray-200 rounded-xl p-8 text-center cursor-pointer hover:border-primary transition">
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2 block"></i>
                    <p class="text-sm text-gray-500 font-medium">Click to upload images</p>
                    <p class="text-xs text-gray-400 mt-1">JPEG, PNG, WebP — max 2MB each</p>
                    <input type="file" name="{{ $isEdit ? 'new_images' : 'images' }}[]" multiple accept="image/*" class="sr-only"
                           onchange="previewImages(this)">
                </label>
                <div id="imagePreview" class="grid grid-cols-4 gap-3 mt-4"></div>
            </div>

        </div>

        {{-- ── Sidebar options ───────────────────────────────────────────────── --}}
        <div class="space-y-6">

            {{-- Status & Publish --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Visibility</h3>
                <div class="space-y-3">
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-sm font-medium text-gray-700">Active (visible in store)</span>
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                               class="w-5 h-5 rounded accent-indigo-500">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-sm font-medium text-gray-700">Featured ⭐</span>
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1"
                               {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 rounded accent-amber-500">
                    </label>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
                <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2.5 rounded-xl transition text-sm">
                    {{ $isEdit ? '💾 Update Product' : '✅ Create Product' }}
                </button>
                <a href="{{ route('admin.products.index') }}"
                   class="block text-center text-sm text-gray-500 hover:text-primary py-1">Cancel</a>
            </div>

        </div>
    </div>
</form>

@push('scripts')
<script>
function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    for (const file of input.files) {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.innerHTML = `<img src="${e.target.result}" class="w-full aspect-square object-cover rounded-xl border border-gray-200">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection
