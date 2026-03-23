<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Render the store homepage.
     */
    public function index()
    {
        $featuredProducts = Product::active()
            ->featured()
            ->inStock()
            ->with('category')
            ->take(8)
            ->get();

        $categories = Category::active()
            ->withCount(['products' => fn ($q) => $q->active()])
            ->take(6)
            ->get();

        $newArrivals = Product::active()
            ->inStock()
            ->with('category')
            ->latest()
            ->take(4)
            ->get();

        $instagramUrl    = env('INSTAGRAM_URL', '#');
        $instagramHandle = env('INSTAGRAM_HANDLE', '@anistore');

        return view('shop.home', compact(
            'featuredProducts',
            'categories',
            'newArrivals',
            'instagramUrl',
            'instagramHandle'
        ));
    }
}
