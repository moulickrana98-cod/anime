<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * List all products with search and filter.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products   = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'required|string',
            'price'         => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'sku'           => 'nullable|string|max:100|unique:products',
            'weight'        => 'nullable|numeric|min:0',
            'tags'          => 'nullable|string',
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,webp|max:2048',
            'is_active'     => 'boolean',
            'is_featured'   => 'boolean',
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
        }

        $product = Product::create([
            ...$validated,
            'slug'    => Str::slug($validated['name']) . '-' . Str::random(6),
            'images'  => $imagePaths,
            'tags'    => $request->filled('tags') ? explode(',', $request->tags) : [],
            'is_active'   => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured', false),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Product \"{$product->name}\" created successfully.");
    }

    /**
     * Show form to edit an existing product.
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update an existing product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'required|string',
            'price'         => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'sku'           => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'weight'        => 'nullable|numeric|min:0',
            'tags'          => 'nullable|string',
            'new_images'    => 'nullable|array',
            'new_images.*'  => 'image|mimes:jpeg,png,webp|max:2048',
            'remove_images' => 'nullable|array', // indices to remove
            'is_active'     => 'boolean',
            'is_featured'   => 'boolean',
        ]);

        // Handle image removals
        $currentImages = $product->images ?? [];
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $idx) {
                if (isset($currentImages[$idx])) {
                    Storage::disk('public')->delete($currentImages[$idx]);
                    unset($currentImages[$idx]);
                }
            }
            $currentImages = array_values($currentImages);
        }

        // Handle new image uploads
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $currentImages[] = $image->store('products', 'public');
            }
        }

        $product->update([
            ...$validated,
            'images'      => $currentImages,
            'tags'        => $request->filled('tags') ? array_map('trim', explode(',', $request->tags)) : [],
            'is_active'   => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Product \"{$product->name}\" updated successfully.");
    }

    /**
     * Show product details (admin view).
     */
    public function show(Product $product)
    {
        $product->load('category', 'orderItems.order');
        return view('admin.products.show', compact('product'));
    }

    /**
     * Delete a product and its images.
     */
    public function destroy(Product $product)
    {
        // Clean up stored images
        foreach ($product->images ?? [] as $image) {
            Storage::disk('public')->delete($image);
        }

        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Product \"{$name}\" deleted successfully.");
    }
}
