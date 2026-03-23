@extends('layouts.admin')
@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $categories->total() }} total categories</p>
    <a href="{{ route('admin.categories.create') }}"
       class="bg-primary hover:bg-primary-dark text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition flex items-center gap-2">
        <i class="fas fa-plus"></i> Add Category
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Category</th>
                    <th class="px-5 py-3 text-left">Slug</th>
                    <th class="px-5 py-3 text-right">Products</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-right">Sort</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($categories as $cat)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if($cat->image)
                                <img src="{{ asset('storage/'.$cat->image) }}" class="w-10 h-10 rounded-lg object-cover border border-gray-100">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary/10 to-anime/10 flex items-center justify-center text-xl">🎭</div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800">{{ $cat->name }}</p>
                                @if($cat->description)
                                    <p class="text-xs text-gray-400 truncate max-w-xs">{{ $cat->description }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $cat->slug }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-700">{{ $cat->products_count }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $cat->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $cat->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right text-gray-500">{{ $cat->sort_order }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.categories.edit', $cat) }}"
                               class="text-gray-400 hover:text-blue-500 transition" title="Edit">
                                <i class="fas fa-pencil"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                                  onsubmit="return confirm('Delete category \'{{ addslashes($cat->name) }}\'?')">
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
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                        No categories found.
                        <a href="{{ route('admin.categories.create') }}" class="text-primary font-semibold ml-1">Add one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
