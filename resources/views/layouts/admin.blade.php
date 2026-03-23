<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — AniStore Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: { DEFAULT: '#6366f1', dark: '#4f46e5' } } } } }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- ── Sidebar ────────────────────────────────────────────────────────── --}}
    <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col flex-shrink-0">
        {{-- Logo --}}
        <div class="h-16 flex items-center px-6 border-b border-gray-800">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-extrabold text-white">🎌 AniStore</a>
            <span class="ml-2 text-xs bg-primary/20 text-primary-light px-2 py-0.5 rounded-full font-semibold">Admin</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">

            @php
                $navItems = [
                    ['route' => 'admin.dashboard',        'icon' => 'fa-chart-pie',     'label' => 'Dashboard'],
                    ['route' => 'admin.products.index',   'icon' => 'fa-box-open',      'label' => 'Products'],
                    ['route' => 'admin.categories.index', 'icon' => 'fa-tags',          'label' => 'Categories'],
                    ['route' => 'admin.orders.index',     'icon' => 'fa-shopping-bag',  'label' => 'Orders'],
                    ['route' => 'admin.users.index',      'icon' => 'fa-users',         'label' => 'Users'],
                    ['route' => 'admin.settings.instagram','icon'=> 'fa-instagram fab', 'label' => 'Instagram'],
                ];
            @endphp

            @foreach($navItems as $item)
                @php $active = request()->routeIs(rtrim($item['route'], '.index') . '*'); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                          {{ $active ? 'bg-primary text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fa-solid {{ $item['icon'] }} w-4 text-center"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach

        </nav>

        {{-- Bottom: view store + logout --}}
        <div class="px-4 pb-6 border-t border-gray-800 pt-4 space-y-1">
            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
                <i class="fas fa-external-link-alt w-4 text-center"></i> View Store
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-400 hover:bg-red-900/40 hover:text-red-400 transition">
                    <i class="fas fa-sign-out-alt w-4 text-center"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main Area ──────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 flex-shrink-0">
            <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center gap-4">
                {{-- Pending orders badge --}}
                @php $pendingCount = \App\Models\Order::where('status', 'pending')->count(); @endphp
                @if($pendingCount)
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="flex items-center gap-2 bg-yellow-50 text-yellow-700 text-sm font-medium px-3 py-1.5 rounded-lg border border-yellow-200 hover:bg-yellow-100 transition">
                    <i class="fas fa-bell text-yellow-500"></i>
                    {{ $pendingCount }} pending
                </a>
                @endif
                {{-- Admin avatar --}}
                <div class="flex items-center gap-2">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover" alt="">
                    <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="m-4 bg-green-50 border-l-4 border-green-500 px-5 py-3 flex items-center justify-between rounded-lg">
                <span class="text-green-800 text-sm font-medium">✅ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-400 hover:text-green-600 text-xl leading-none">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="m-4 bg-red-50 border-l-4 border-red-500 px-5 py-3 flex items-center justify-between rounded-lg">
                <span class="text-red-800 text-sm font-medium">❌ {{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 text-xl leading-none">&times;</button>
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>

    </div>
</div>

@stack('scripts')
</body>
</html>
