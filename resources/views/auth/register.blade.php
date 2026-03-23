@extends('layouts.shop')
@section('title', 'Register — AniStore')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 to-indigo-900 flex items-center justify-center px-4 py-16">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8">

        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold bg-gradient-to-r from-primary to-anime bg-clip-text text-transparent">🎌 AniStore</a>
            <p class="text-gray-500 mt-2 text-sm">Create your free account and start shopping!</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary @error('password') border-red-400 @enderror">
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary">
            </div>

            <button type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 rounded-xl transition shadow-lg shadow-primary/20 mt-2">
                Create Account
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Sign in</a>
        </p>
    </div>
</div>
@endsection
