<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;
use UnexpectedValueException;

class CheckoutController extends Controller
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Show checkout page — validates cart is not empty.
     */
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $tax      = round($subtotal * 0.18, 2); // 18% GST
        $shipping = $subtotal >= 999 ? 0 : 99;   // Free shipping over ₹999
        $total    = $subtotal + $tax + $shipping;

        return view('shop.checkout.index', compact('cart', 'subtotal', 'tax', 'shipping', 'total'));
    }

    /**
     * Create a Stripe PaymentIntent and return client_secret to the frontend.
     */
    public function process(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty.'], 422);
        }

        $validated = $request->validate([
            'shipping_name'        => 'required|string|max:255',
            'shipping_email'       => 'required|email',
            'shipping_phone'       => 'required|string|max:20',
            'shipping_address'     => 'required|string|max:500',
            'shipping_city'        => 'required|string|max:100',
            'shipping_state'       => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_country'     => 'required|string|max:100',
        ]);

        // Re-verify stock and compute totals server-side (never trust client)
        $subtotal = 0;
        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            if (!$product || !$product->isInStock() || $product->stock < $item['quantity']) {
                return response()->json(['error' => "Stock issue with \"{$item['name']}\". Please review your cart."], 422);
            }
            $subtotal += $product->price * $item['quantity'];
        }

        $tax      = round($subtotal * 0.18, 2);
        $shipping = $subtotal >= 999 ? 0 : 99;
        $total    = $subtotal + $tax + $shipping;

        // Save checkout data to session for after Stripe confirms
        session(['checkout_data' => $validated]);

        try {
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount'   => (int) ($total * 100), // Stripe uses paise for INR
                'currency' => 'inr',
                'metadata' => [
                    'user_id' => auth()->id(),
                    'email'   => $validated['shipping_email'],
                ],
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            // Store the PI id temporarily to link to order after success
            session(['stripe_pi_id' => $paymentIntent->id]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe PaymentIntent creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Payment initialization failed. Please try again.'], 500);
        }
    }

    /**
     * Order success page — create DB order after payment confirmed.
     */
    public function success(Request $request)
    {
        $cart         = session('cart', []);
        $checkoutData = session('checkout_data', []);
        $piId         = session('stripe_pi_id');

        if (empty($cart) || empty($checkoutData)) {
            return redirect()->route('home');
        }

        // Retrieve and verify payment status from Stripe
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($piId);

            if ($paymentIntent->status !== 'succeeded') {
                return redirect()->route('checkout.cancel')
                    ->with('error', 'Payment was not completed successfully.');
            }
        } catch (\Exception $e) {
            Log::error('Stripe PI retrieval failed: ' . $e->getMessage());
            return redirect()->route('checkout.cancel')->with('error', 'Could not verify payment.');
        }

        $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['quantity']);
        $tax      = round($subtotal * 0.18, 2);
        $shipping = $subtotal >= 999 ? 0 : 99;
        $total    = $subtotal + $tax + $shipping;

        DB::beginTransaction();
        try {
            // Create the order
            $order = Order::create([
                ...$checkoutData,
                'user_id'                  => auth()->id(),
                'order_number'             => Order::generateOrderNumber(),
                'status'                   => Order::STATUS_PROCESSING,
                'subtotal'                 => $subtotal,
                'tax'                      => $tax,
                'shipping'                 => $shipping,
                'total'                    => $total,
                'currency'                 => 'INR',
                'payment_method'           => 'stripe',
                'payment_status'           => 'paid',
                'stripe_payment_intent_id' => $piId,
            ]);

            // Create order items and reduce stock
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_image' => $product->images[0] ?? null,
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $product->price,
                    'total_price'   => $product->price * $item['quantity'],
                ]);

                // Decrement stock
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            return redirect()->route('checkout.cancel')->with('error', 'Order creation failed. Please contact support with your payment reference: ' . $piId);
        }

        // Clear session data
        session()->forget(['cart', 'checkout_data', 'stripe_pi_id']);

        return view('shop.checkout.success', compact('order'));
    }

    /**
     * Payment cancelled/failed page.
     */
    public function cancel()
    {
        return view('shop.checkout.cancel');
    }

    /**
     * Handle Stripe webhook events.
     * Verify the signature before processing any event.
     */
    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (UnexpectedValueException $e) {
            Log::warning('Stripe webhook: invalid payload.');
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook: invalid signature.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle relevant event types
        match ($event->type) {
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
            'charge.dispute.created'        => $this->handleDispute($event->data->object),
            default                         => null,
        };

        return response()->json(['status' => 'ok']);
    }

    private function handlePaymentFailed($paymentIntent): void
    {
        Log::info('Payment failed for PI: ' . $paymentIntent->id);
        Order::where('stripe_payment_intent_id', $paymentIntent->id)
            ->update(['payment_status' => 'failed', 'status' => Order::STATUS_CANCELLED]);
    }

    private function handleDispute($charge): void
    {
        Log::warning('Dispute created for charge: ' . $charge->id);
        // Notify admin via email (extend as needed)
    }
}
