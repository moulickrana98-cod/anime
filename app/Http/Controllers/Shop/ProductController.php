<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Shop listing with search, filter, and sort.
     */
    public function index(Request $request)
    {
        $query = Product::active()->inStock()->with('category');

        // Search by name
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        match ($request->sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest'     => $query->latest(),
            default      => $query->orderByDesc('is_featured')->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::active()->withCount('products')->get();

        return view('shop.products.index', compact('products', 'categories'));
    }

    /**
     * Filter by category.
     */
    public function byCategory(Category $category)
    {
        $products = Product::active()
            ->inStock()
            ->where('category_id', $category->id)
            ->with('category')
            ->paginate(12);

        $categories = Category::active()->get();

        return view('shop.products.index', compact('products', 'categories', 'category'));
    }

    /**
     * Product detail page.
     */
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $related = Product::active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('category')
            ->take(4)
            ->get();

        return view('shop.products.show', compact('product', 'related'));
    }
}
