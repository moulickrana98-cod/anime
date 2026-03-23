<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get the current cart from session.
     */
    private function getCart(): array
    {
        return session('cart', []);
    }

    /**
     * Save cart back to session.
     */
    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    /**
     * Display the cart page.
     */
    public function index()
    {
        $cart  = $this->getCart();
        $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        return view('shop.cart.index', compact('cart', 'total'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:20',
        ]);

        if (!$product->isInStock()) {
            return back()->with('error', 'Sorry, this product is out of stock.');
        }

        $qty = (int) $request->quantity;

        if ($qty > $product->stock) {
            return back()->with('error', "Only {$product->stock} units available.");
        }

        $cart = $this->getCart();
        $key  = (string) $product->id;

        if (isset($cart[$key])) {
            $newQty = $cart[$key]['quantity'] + $qty;
            if ($newQty > $product->stock) {
                $newQty = $product->stock;
            }
            $cart[$key]['quantity'] = $newQty;
        } else {
            $cart[$key] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'slug'     => $product->slug,
                'price'    => (float) $product->price,
                'image'    => $product->thumbnail,
                'quantity' => $qty,
                'stock'    => $product->stock,
            ];
        }

        $this->saveCart($cart);

        return back()->with('success', "\"{$product->name}\" added to cart.");
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(Request $request, string $rowId)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:20']);

        $cart = $this->getCart();

        if (!isset($cart[$rowId])) {
            return back()->with('error', 'Cart item not found.');
        }

        $product = Product::find($rowId);
        $qty     = (int) $request->quantity;

        if ($product && $qty > $product->stock) {
            $qty = $product->stock;
        }

        $cart[$rowId]['quantity'] = $qty;
        $this->saveCart($cart);

        return back()->with('success', 'Cart updated.');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(string $rowId)
    {
        $cart = $this->getCart();
        unset($cart[$rowId]);
        $this->saveCart($cart);

        return back()->with('success', 'Item removed from cart.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Cart cleared.');
    }

    /**
     * Helper: get item count for navbar badge.
     */
    public static function cartCount(): int
    {
        return collect(session('cart', []))->sum('quantity');
    }
}
