@extends('layouts.shop')

@section('title', 'Checkout — AniStore')

@push('head')
<script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-3xl font-extrabold text-gray-900 mb-8">💳 Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">

        {{-- ── Checkout Form ────────────────────────────────────────────────── --}}
        <div class="lg:col-span-3">
            <form id="checkoutForm" class="space-y-6">
                @csrf

                {{-- Shipping Info --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-gray-900 mb-5 text-lg">📦 Shipping Information</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @php $fields = [
                            ['shipping_name',        'Full Name',    'text',  'sm:col-span-2'],
                            ['shipping_email',       'Email',        'email', ''],
                            ['shipping_phone',       'Phone',        'tel',   ''],
                            ['shipping_address',     'Address',      'text',  'sm:col-span-2'],
                            ['shipping_city',        'City',         'text',  ''],
                            ['shipping_state',       'State',        'text',  ''],
                            ['shipping_postal_code', 'Postal Code',  'text',  ''],
                            ['shipping_country',     'Country',      'text',  ''],
                        ]; @endphp

                        @foreach($fields as [$name, $label, $type, $span])
                        <div class="{{ $span }}">
                            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">{{ $label }} *</label>
                            <input type="{{ $type }}" name="{{ $name }}" required
                                   value="{{ old($name, auth()->user()->{str_replace('shipping_', '', $name)} ?? ($name === 'shipping_country' ? 'India' : '')) }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary transition">
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Payment --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-gray-900 mb-5 text-lg">
                        🔒 Secure Payment
                        <span class="ml-2 text-xs font-normal text-gray-400">Powered by Stripe</span>
                    </h2>

                    {{-- Stripe Card Element --}}
                    <div id="payment-element" class="border border-gray-200 rounded-xl p-4 min-h-[100px] bg-gray-50">
                        {{-- Stripe will inject its UI here --}}
                    </div>
                    <div id="payment-errors" class="text-red-500 text-sm mt-2 hidden"></div>
                </div>

                <button type="submit" id="submitBtn"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 rounded-xl transition text-lg shadow-lg shadow-primary/20 flex items-center justify-center gap-3 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span id="btnText"><i class="fas fa-lock mr-2"></i>Pay ₹{{ number_format($total, 2) }}</span>
                    <span id="btnSpinner" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </form>
        </div>

        {{-- ── Order Summary ─────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-20">
                <h2 class="font-bold text-gray-900 mb-5">Order Summary</h2>

                <div class="space-y-3 mb-5">
                    @foreach($cart as $item)
                    <div class="flex items-center gap-3">
                        <img src="{{ $item['image'] }}" class="w-12 h-12 rounded-lg object-cover border border-gray-100"
                             onerror="this.src='https://placehold.co/48x48/e0e7ff/6366f1?text=Ani'">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-400">Qty: {{ $item['quantity'] }}</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 flex-shrink-0">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                    </div>
                    @endforeach
                </div>

                <hr class="border-gray-100 mb-4">

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₹{{ number_format($subtotal, 2) }}</span></div>
                    <div class="flex justify-between text-gray-600"><span>GST (18%)</span><span>₹{{ number_format($tax, 2) }}</span></div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span class="{{ $shipping == 0 ? 'text-green-600 font-semibold' : '' }}">{{ $shipping == 0 ? 'FREE' : '₹'.$shipping }}</span>
                    </div>
                    <hr class="border-gray-100">
                    <div class="flex justify-between font-bold text-gray-900 text-base">
                        <span>Total</span>
                        <span>₹{{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
const stripe = Stripe('{{ config("services.stripe.key") }}');
let elements, paymentElement;

// Initialize Stripe Elements asynchronously
async function initStripe() {
    elements = stripe.elements({ mode: 'payment', amount: {{ (int)($total * 100) }}, currency: 'inr' });
    paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');
}

initStripe();

document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const btn      = document.getElementById('submitBtn');
    const btnText  = document.getElementById('btnText');
    const spinner  = document.getElementById('btnSpinner');
    const errDiv   = document.getElementById('payment-errors');

    btn.disabled = true;
    btnText.classList.add('hidden');
    spinner.classList.remove('hidden');
    errDiv.classList.add('hidden');

    // Collect shipping data and get clientSecret from backend
    const form    = e.target;
    const data    = Object.fromEntries(new FormData(form));

    try {
        const res = await fetch('{{ route("checkout.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(data),
        });

        const json = await res.json();
        if (!res.ok) throw new Error(json.error || 'Payment initialization failed.');

        // Confirm the PaymentIntent with Stripe
        const { error } = await stripe.confirmPayment({
            elements,
            clientSecret: json.clientSecret,
            confirmParams: {
                return_url: '{{ route("checkout.success") }}',
                payment_method_data: { billing_details: { name: data.shipping_name, email: data.shipping_email } },
            },
        });

        if (error) throw new Error(error.message);

    } catch (err) {
        errDiv.textContent = err.message;
        errDiv.classList.remove('hidden');
        btn.disabled = false;
        btnText.classList.remove('hidden');
        spinner.classList.add('hidden');
    }
});
</script>
@endpush
@endsection
