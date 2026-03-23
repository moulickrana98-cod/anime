@extends('layouts.admin')
@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')

{{-- Filters --}}
<form method="GET" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="text-xs font-semibold text-gray-500 block mb-1">Search (order # or customer)</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ANI-XXXXXXXX or name"
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
    </div>
    <div class="min-w-40">
        <label class="text-xs font-semibold text-gray-500 block mb-1">Status</label>
        <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-primary">
            <option value="">All Statuses</option>
            @foreach($statuses as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-xs font-semibold text-gray-500 block mb-1">From</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
    </div>
    <div>
        <label class="text-xs font-semibold text-gray-500 block mb-1">To</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
    </div>
    <button type="submit" class="bg-primary text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-primary-dark transition">Filter</button>
    @if(request()->hasAny(['search','status','date_from','date_to']))
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-400 hover:text-primary py-2">Clear</a>
    @endif
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Order #</th>
                    <th class="px-5 py-3 text-left">Customer</th>
                    <th class="px-5 py-3 text-right">Total</th>
                    <th class="px-5 py-3 text-center">Payment</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-left">Date</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="font-mono text-xs text-primary font-bold hover:underline">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $order->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $order->shipping_email }}</p>
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-gray-900">{{ $order->formatted_total }}</td>
                    <td class="px-5 py-3 text-center">
                        @php $paid = $order->payment_status === 'paid'; @endphp
                        <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $paid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @php $badge = $order->status_badge; @endphp
                        <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs whitespace-nowrap">
                        {{ $order->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="text-gray-400 hover:text-primary transition" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">No orders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
