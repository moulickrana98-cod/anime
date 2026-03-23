@extends('layouts.shop')
@section('title', 'Login — AniStore')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 to-indigo-900 flex items-center justify-center px-4 py-16">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8">

        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold bg-gradient-to-r from-primary to-anime bg-clip-text text-transparent">🎌 AniStore</a>
            <p class="text-gray-500 mt-2 text-sm">Welcome back! Sign in to continue.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary @error('password') border-red-400 @enderror">
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded accent-indigo-500"> Remember me
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 rounded-xl transition shadow-lg shadow-primary/20">
                Sign In
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Sign up free</a>
        </p>
    </div>
</div>
@endsection
