@extends('layouts.admin')
@section('title', 'Order '.$order->order_number)
@section('page-title', 'Order #'.$order->order_number)

@section('content')

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── Order items & info ──────────────────────────────────────────── --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- Items table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">Order Items</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">Product</th>
                        <th class="px-5 py-3 text-right">Unit Price</th>
                        <th class="px-5 py-3 text-right">Qty</th>
                        <th class="px-5 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->image_url }}" class="w-10 h-10 rounded-lg object-cover border border-gray-100"
                                     onerror="this.src='https://placehold.co/40x40/e0e7ff/6366f1?text=A'">
                                <span class="font-medium text-gray-800">{{ $item->product_name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-right text-gray-600">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-5 py-3 text-right font-semibold">{{ $item->quantity }}</td>
                        <td class="px-5 py-3 text-right font-bold text-gray-900">₹{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 text-sm">
                    <tr><td colspan="3" class="px-5 py-2 text-right text-gray-500">Subtotal</td><td class="px-5 py-2 text-right font-semibold">₹{{ number_format($order->subtotal, 2) }}</td></tr>
                    <tr><td colspan="3" class="px-5 py-2 text-right text-gray-500">GST (18%)</td><td class="px-5 py-2 text-right font-semibold">₹{{ number_format($order->tax, 2) }}</td></tr>
                    <tr><td colspan="3" class="px-5 py-2 text-right text-gray-500">Shipping</td><td class="px-5 py-2 text-right font-semibold">{{ $order->shipping == 0 ? 'FREE' : '₹'.number_format($order->shipping, 2) }}</td></tr>
                    <tr class="text-base"><td colspan="3" class="px-5 py-3 text-right font-bold text-gray-800">Total</td><td class="px-5 py-3 text-right font-extrabold text-primary">{{ $order->formatted_total }}</td></tr>
                </tfoot>
            </table>
        </div>

        {{-- Shipping address --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4">Shipping Address</h2>
            <div class="text-sm text-gray-600 space-y-1">
                <p class="font-semibold text-gray-800">{{ $order->shipping_name }}</p>
                <p>{{ $order->shipping_address }}</p>
                <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                <p>{{ $order->shipping_country }}</p>
                <p class="mt-2"><i class="fas fa-envelope mr-1 text-gray-400"></i>{{ $order->shipping_email }}</p>
                <p><i class="fas fa-phone mr-1 text-gray-400"></i>{{ $order->shipping_phone }}</p>
            </div>
        </div>

    </div>

    {{-- ── Sidebar: Status & Payment ────────────────────────────────────── --}}
    <div class="space-y-6">

        {{-- Order status card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4">Order Status</h2>

            @php $badge = $order->status_badge; @endphp
            <div class="mb-4">
                <span class="px-3 py-1.5 rounded-xl text-sm font-bold {{ $badge['class'] }}">
                    {{ $badge['label'] }}
                </span>
            </div>

            @php $transitions = $order->getAllowedStatusTransitions(); @endphp
            @if(count($transitions))
            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block">Update Status</label>
                <select name="status" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white focus:outline-none focus:border-primary">
                    @foreach($transitions as $s)
                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-primary text-white text-sm font-bold py-2.5 rounded-xl hover:bg-primary-dark transition">
                    Update Status
                </button>
            </form>
            @else
                <p class="text-xs text-gray-400">No further status changes available.</p>
            @endif
        </div>

        {{-- Payment info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4">Payment</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Method</span>
                    <span class="font-semibold capitalize">{{ $order->payment_method ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    <span class="font-semibold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-red-500' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                @if($order->stripe_payment_intent_id)
                <div>
                    <span class="text-gray-500 block mb-1">Stripe PI</span>
                    <p class="font-mono text-xs bg-gray-50 rounded px-2 py-1 break-all">{{ $order->stripe_payment_intent_id }}</p>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-500">Order Date</span>
                    <span class="font-semibold">{{ $order->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Customer info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4">Customer</h2>
            <div class="flex items-center gap-3 mb-3">
                <img src="{{ $order->user->avatar_url }}" class="w-10 h-10 rounded-full object-cover">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $order->user->name }}</p>
                    <p class="text-xs text-gray-400">{{ $order->user->email }}</p>
                </div>
            </div>
            <a href="{{ route('admin.users.show', $order->user) }}"
               class="text-xs text-primary font-semibold hover:underline">View customer profile →</a>
        </div>

        <a href="{{ route('admin.orders.index') }}"
           class="block text-center text-sm text-gray-500 hover:text-primary py-2">← Back to Orders</a>
    </div>

</div>
@endsection
