@extends('layouts.admin')
@section('title', isset($user) ? 'Edit User' : 'Add User')
@section('page-title', isset($user) ? 'Edit: '.$user->name : 'Add User')

@section('content')
@php $isEdit = isset($user); @endphp

<div class="max-w-xl">
<form action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}"
      method="POST">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Name *</label>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary @error('name') border-red-400 @enderror">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email *</label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary @error('email') border-red-400 @enderror">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                Password {{ $isEdit ? '(leave blank to keep current)' : '*' }}
            </label>
            <input type="password" name="password" {{ $isEdit ? '' : 'required' }}
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary @error('password') border-red-400 @enderror">
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary">
        </div>
        <label class="flex items-center justify-between cursor-pointer">
            <span class="text-sm font-medium text-gray-700">Admin privileges</span>
            <input type="hidden" name="is_admin" value="0">
            <input type="checkbox" name="is_admin" value="1"
                   {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}
                   class="w-5 h-5 rounded accent-indigo-500">
        </label>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold px-8 py-2.5 rounded-xl transition text-sm">
                {{ $isEdit ? '💾 Update' : '✅ Create' }}
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="border border-gray-200 text-gray-600 hover:bg-gray-50 font-semibold px-8 py-2.5 rounded-xl transition text-sm">Cancel</a>
        </div>
    </div>
</form>
</div>
@endsection
