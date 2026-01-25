<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'variants'])
            ->where('is_active', true);

        if ($request->has('category_id')) {
            $categoryId = $request->category_id;
            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereHas('category', function ($subQ) use ($categoryId) {
                        $subQ->where('parent_id', $categoryId);
                    });
            });
        }

        return response()->json($query->latest()->get());
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'images', 'variants'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json($product);
    }

    /**
     * Admin: Store a new product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'] ?? null,
            'is_active' => true
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $url = asset('storage/' . $path);

            ProductImage::create([
                'product_id' => $product->id,
                'url' => $url,
                'is_primary' => true
            ]);
        }

        return response()->json($product->load(['category', 'images']), 201);
    }

    /**
     * Admin: Update a product.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable', // Flexible validation for string/bool from FormData
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        // Clean values from string to primary types if needed
        if (isset($validated['is_active'])) {
            $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
        }

        $product->update(collect($validated)->except('image')->toArray());

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $url = asset('storage/' . $path);

            // Reemplazar todas las imágenes anteriores (simplificado)
            $product->images()->delete();

            ProductImage::create([
                'product_id' => $product->id,
                'url' => $url,
                'is_primary' => true
            ]);
        }

        return response()->json($product->load(['category', 'images']));
    }

    /**
     * Admin: Delete a product.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
