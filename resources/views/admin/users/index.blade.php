@extends('layouts.admin')
@section('title', 'Users')
@section('page-title', 'Users')

@section('content')

<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $users->total() }} total users</p>
    <a href="{{ route('admin.users.create') }}"
       class="bg-primary hover:bg-primary-dark text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition flex items-center gap-2">
        <i class="fas fa-plus"></i> Add User
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="text-xs font-semibold text-gray-500 block mb-1">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..."
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
    </div>
    <div class="min-w-40">
        <label class="text-xs font-semibold text-gray-500 block mb-1">Role</label>
        <select name="role" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-primary">
            <option value="">All Roles</option>
            <option value="admin"    {{ request('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
            <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
        </select>
    </div>
    <button type="submit" class="bg-primary text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-primary-dark transition">Filter</button>
    @if(request()->hasAny(['search','role']))
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-primary py-2">Clear</a>
    @endif
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">User</th>
                    <th class="px-5 py-3 text-left">Role</th>
                    <th class="px-5 py-3 text-right">Orders</th>
                    <th class="px-5 py-3 text-left">Joined</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->avatar_url }}" class="w-9 h-9 rounded-full object-cover">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        @if($user->is_admin)
                            <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded-full">Admin</span>
                        @else
                            <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-2 py-1 rounded-full">Customer</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-700">{{ $user->orders_count }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="text-gray-400 hover:text-primary transition"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="text-gray-400 hover:text-blue-500 transition"><i class="fas fa-pencil"></i></a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                  onsubmit="return confirm('Delete user {{ addslashes($user->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>
@endsection
