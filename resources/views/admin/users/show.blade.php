@extends('layouts.admin')
@section('title', $user->name)
@section('page-title', $user->name)

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Profile Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
        <img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-4">
        <h2 class="font-bold text-gray-900 text-lg">{{ $user->name }}</h2>
        <p class="text-gray-500 text-sm">{{ $user->email }}</p>
        <div class="mt-3">
            @if($user->is_admin)
                <span class="bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full">Admin</span>
            @else
                <span class="bg-gray-100 text-gray-500 text-xs font-semibold px-3 py-1 rounded-full">Customer</span>
            @endif
        </div>
        <div class="mt-4 text-xs text-gray-400">
            <p>Joined {{ $user->created_at->format('d M Y') }}</p>
            <p>{{ $user->orders->count() }} orders placed</p>
        </div>
        <a href="{{ route('admin.users.edit', $user) }}"
           class="mt-5 block bg-primary text-white text-sm font-bold py-2 rounded-xl hover:bg-primary-dark transition">
            Edit User
        </a>
    </div>

    {{-- Order History --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">Order History ({{ $user->orders->count() }})</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-5 py-3 text-left">Order #</th>
                    <th class="px-5 py-3 text-right">Total</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-left">Date</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($user->orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-mono text-xs text-primary font-bold">{{ $order->order_number }}</td>
                    <td class="px-5 py-3 text-right font-semibold">{{ $order->formatted_total }}</td>
                    <td class="px-5 py-3 text-center">
                        @php $b = $order->status_badge; @endphp
                        <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $b['class'] }}">{{ $b['label'] }}</span>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:underline text-xs font-semibold">View →</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
