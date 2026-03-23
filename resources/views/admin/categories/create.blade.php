@extends('layouts.admin')
@section('title', isset($category) ? 'Edit Category' : 'Add Category')
@section('page-title', isset($category) ? 'Edit: '.$category->name : 'Add Category')

@section('content')
@php $isEdit = isset($category); @endphp

<div class="max-w-2xl">
<form action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        <h3 class="font-bold text-gray-800">Category Details</h3>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Name *</label>
            <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary @error('name') border-red-400 @enderror">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary">{{ old('description', $category->description ?? '') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0"
                   class="w-32 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
            <p class="text-xs text-gray-400 mt-1">Lower numbers appear first.</p>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Category Image</label>
            @if($isEdit && $category->image)
                <div class="mb-3">
                    <img src="{{ asset('storage/'.$category->image) }}" class="w-24 h-24 rounded-xl object-cover border border-gray-200">
                    <p class="text-xs text-gray-400 mt-1">Upload a new image to replace it.</p>
                </div>
            @endif
            <input type="file" name="image" accept="image/*"
                   class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        </div>

        <label class="flex items-center justify-between cursor-pointer">
            <span class="text-sm font-medium text-gray-700">Active (show in store)</span>
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}
                   class="w-5 h-5 rounded accent-indigo-500">
        </label>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold px-8 py-2.5 rounded-xl transition text-sm">
                {{ $isEdit ? '💾 Update' : '✅ Create' }}
            </button>
            <a href="{{ route('admin.categories.index') }}"
               class="border border-gray-200 text-gray-600 hover:bg-gray-50 font-semibold px-8 py-2.5 rounded-xl transition text-sm">
                Cancel
            </a>
        </div>
    </div>
</form>
</div>
@endsection
