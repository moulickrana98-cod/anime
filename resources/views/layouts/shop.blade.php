<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AniStore — Your Anime Universe')</title>
    <meta name="description" content="@yield('meta_description', 'Shop premium anime merchandise — figures, apparel, posters and more.')">

    {{-- Tailwind CSS via CDN (replace with compiled asset in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#6366f1', dark: '#4f46e5', light: '#a5b4fc' },
                        anime:   { DEFAULT: '#f97316', dark: '#ea580c' },
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @stack('head')
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

{{-- ── Navigation ──────────────────────────────────────────────────────── --}}
<nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-2xl font-extrabold bg-gradient-to-r from-primary to-anime bg-clip-text text-transparent">
                    🎌 AniStore
                </span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}"      class="text-sm font-medium text-gray-600 hover:text-primary transition">Home</a>
                <a href="{{ route('shop.index') }}" class="text-sm font-medium text-gray-600 hover:text-primary transition">Shop</a>
                @foreach(\App\Models\Category::active()->take(4)->get() as $cat)
                    <a href="{{ route('shop.category', $cat) }}" class="text-sm font-medium text-gray-600 hover:text-primary transition">{{ $cat->name }}</a>
                @endforeach
            </div>

            {{-- Right Icons --}}
            <div class="flex items-center gap-4">
                {{-- Search --}}
                <a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-primary">
                    <i class="fas fa-search"></i>
                </a>

                {{-- Instagram --}}
                @if(env('INSTAGRAM_URL'))
                <a href="{{ env('INSTAGRAM_URL') }}" target="_blank" rel="noopener"
                   class="text-gray-500 hover:text-pink-600 transition" title="Follow us on Instagram">
                    <i class="fab fa-instagram text-lg"></i>
                </a>
                @endif

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-primary transition">
                    <i class="fas fa-shopping-cart text-lg"></i>
                    @php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
                    @if($cartCount > 0)
                        <span class="absolute -top-2 -right-2 bg-anime text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                {{-- Auth --}}
                @auth
                    <div class="relative group">
                        <button class="flex items-center gap-2 text-sm text-gray-600 hover:text-primary font-medium">
                            <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover" alt="{{ auth()->user()->name }}">
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                        </button>
                        <div class="absolute right-0 mt-1 w-48 bg-white shadow-lg rounded-xl border border-gray-100 hidden group-hover:block z-50">
                            <a href="{{ route('account.orders') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">My Orders</a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm text-primary font-semibold hover:bg-primary/5">Admin Panel</a>
                            @endif
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-primary">Login</a>
                    <a href="{{ route('register') }}" class="bg-primary text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-primary-dark transition">Sign Up</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ── Flash Messages ────────────────────────────────────────────────────── --}}
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="bg-green-50 border-l-4 border-green-500 px-6 py-3 flex items-center justify-between max-w-7xl mx-auto mt-4 rounded-lg">
        <span class="text-green-800 text-sm font-medium">✅ {{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 text-lg leading-none">&times;</button>
    </div>
@endif
@if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 px-6 py-3 flex items-center justify-between max-w-7xl mx-auto mt-4 rounded-lg">
        <span class="text-red-800 text-sm font-medium">❌ {{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 text-lg leading-none">&times;</button>
    </div>
@endif

{{-- ── Main Content ──────────────────────────────────────────────────────── --}}
<main>
    @yield('content')
</main>

{{-- ── Footer ───────────────────────────────────────────────────────────── --}}
<footer class="bg-gray-900 text-gray-300 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 md:grid-cols-4 gap-10">

        {{-- Brand --}}
        <div class="md:col-span-2">
            <p class="text-2xl font-extrabold text-white mb-3">🎌 AniStore</p>
            <p class="text-sm text-gray-400 leading-relaxed max-w-sm">
                Your one-stop shop for premium anime merchandise. From legendary figures to everyday gear — we bring your favourite anime to life.
            </p>
            {{-- Social Icons --}}
            <div class="flex gap-4 mt-5">
                @if(env('INSTAGRAM_URL'))
                <a href="{{ env('INSTAGRAM_URL') }}" target="_blank" rel="noopener"
                   class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-600 via-pink-600 to-orange-400 flex items-center justify-center text-white hover:scale-110 transition" title="Instagram: {{ env('INSTAGRAM_HANDLE') }}">
                    <i class="fab fa-instagram"></i>
                </a>
                @endif
                <a href="#" class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white hover:scale-110 transition">
                    <i class="fab fa-facebook-f text-sm"></i>
                </a>
                <a href="#" class="w-9 h-9 rounded-full bg-sky-500 flex items-center justify-center text-white hover:scale-110 transition">
                    <i class="fab fa-twitter text-sm"></i>
                </a>
            </div>
            @if(env('INSTAGRAM_HANDLE'))
            <p class="mt-3 text-sm text-pink-400 font-medium">
                <i class="fab fa-instagram mr-1"></i> {{ env('INSTAGRAM_HANDLE') }}
            </p>
            @endif
        </div>

        {{-- Quick Links --}}
        <div>
            <p class="text-white font-semibold mb-4">Shop</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('shop.index') }}" class="hover:text-white transition">All Products</a></li>
                @foreach(\App\Models\Category::active()->take(5)->get() as $cat)
                    <li><a href="{{ route('shop.category', $cat) }}" class="hover:text-white transition">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </div>

        {{-- Account --}}
        <div>
            <p class="text-white font-semibold mb-4">Account</p>
            <ul class="space-y-2 text-sm">
                @auth
                    <li><a href="{{ route('account.orders') }}" class="hover:text-white transition">My Orders</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="hover:text-white transition">Logout</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:text-white transition">Login</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition">Register</a></li>
                @endauth
                <li><a href="{{ route('cart.index') }}" class="hover:text-white transition">My Cart</a></li>
            </ul>
        </div>

    </div>
    <div class="border-t border-gray-800 py-5 text-center text-xs text-gray-500">
        © {{ date('Y') }} AniStore. All rights reserved. Powered by Laravel &amp; Stripe.
    </div>
</footer>

@stack('scripts')
</body>
</html>
