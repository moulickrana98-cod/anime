@extends('layouts.admin')
@section('title', 'Instagram Settings')
@section('page-title', 'Instagram Integration')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-600 via-pink-600 to-orange-400 flex items-center justify-center">
                <i class="fab fa-instagram text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 text-lg">Instagram Profile Link</h2>
                <p class="text-sm text-gray-500">Display your Instagram page across the store to grow your following.</p>
            </div>
        </div>

        <form action="{{ route('admin.settings.instagram.update') }}" method="POST">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Instagram URL *</label>
                    <input type="url" name="instagram_url"
                           value="{{ old('instagram_url', $instagramUrl) }}"
                           placeholder="https://instagram.com/yourstorehandle"
                           required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary @error('instagram_url') border-red-400 @enderror">
                    @error('instagram_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Handle (e.g. @anistore)</label>
                    <input type="text" name="instagram_handle"
                           value="{{ old('instagram_handle', $instagramHandle) }}"
                           placeholder="@anistore"
                           required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary @error('instagram_handle') border-red-400 @enderror">
                    @error('instagram_handle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">Shown in the footer and homepage CTA banner.</p>
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white font-bold py-2.5 rounded-xl transition text-sm">
                    <i class="fab fa-instagram mr-2"></i> Save Instagram Settings
                </button>
            </div>
        </form>

        @if($instagramUrl)
        <div class="mt-6 pt-6 border-t border-gray-100">
            <p class="text-xs text-gray-500 mb-3 font-semibold uppercase tracking-wide">Preview</p>
            <a href="{{ $instagramUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white font-semibold px-5 py-2.5 rounded-xl hover:opacity-90 transition text-sm">
                <i class="fab fa-instagram"></i>
                {{ $instagramHandle ?: $instagramUrl }}
                <i class="fas fa-external-link-alt text-xs opacity-70"></i>
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
