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
        $query = Product::with(['category', 'subcategory', 'images', 'variants.color', 'variants.size'])
            ->where('is_active', true);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        return response()->json($query->latest()->get());
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'subcategory', 'images', 'variants.color', 'variants.size'])
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
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'variants' => 'nullable|array',
            'variants.*.color_id' => 'required|exists:colors,id',
            'variants.*.size_id' => 'required|exists:sizes,id',
            'variants.*.stock' => 'required|integer|min:0'
        ]);

        $totalStock = 0;
        if ($request->has('variants')) {
            foreach ($request->variants as $v) {
                $totalStock += $v['stock'];
            }
        }

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'] ?? null,
            'price' => $validated['price'],
            'stock' => $totalStock, // Stock total calculado
            'description' => $validated['description'] ?? null,
            'is_active' => true
        ]);

        // Procesar múltiples imágenes
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $index => $image) {
                $path = $image->store('products', 'public');
                // Guardamos solo el path relativo, no la URL completa
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $path,
                    'is_primary' => $index === 0
                ]);
            }
        }

        // Crear variantes
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $product->variants()->create([
                    'color_id' => $variant['color_id'],
                    'size_id' => $variant['size_id'],
                    'stock' => $variant['stock']
                ]);
            }
        }

        return response()->json($product->load(['category', 'subcategory', 'images', 'variants.color', 'variants.size']), 201);
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
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'existing_images.*' => 'nullable|string',
            'variants' => 'nullable|array',
            'variants.*.color_id' => 'required|exists:colors,id',
            'variants.*.size_id' => 'required|exists:sizes,id',
            'variants.*.stock' => 'required|integer|min:0'
        ]);

        // Clean values
        if (isset($validated['is_active'])) {
            $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
        }

        // Calcular nuevo stock total
        $totalStock = 0;
        if ($request->has('variants')) {
            foreach ($request->variants as $v) {
                $totalStock += $v['stock'];
            }
        }
        // Actualizar stock total en el producto principal
        $validated['stock'] = $totalStock;

        $product->update(collect($validated)->except(['images', 'existing_images', 'variants'])->toArray());

        // Eliminar todas las imágenes actuales
        $product->images()->delete();

        $imageOrder = 0;

        // Validar existing_images
        if ($request->has('existing_images')) {
            $existingUrls = $request->input('existing_images');
            foreach ($existingUrls as $url) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $url,
                    'is_primary' => $imageOrder === 0
                ]);
                $imageOrder++;
            }
        }

        // Agregar nuevas imágenes
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $path = $image->store('products', 'public');
                // Guardamos solo el path relativo
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $path,
                    'is_primary' => $imageOrder === 0
                ]);
                $imageOrder++;
            }
        }

        // Sincronizar variantes (reemplazo completo)
        if ($request->has('variants')) {
            $product->variants()->delete(); // Eliminar variantes anteriores

            foreach ($request->variants as $variant) {
                $product->variants()->create([
                    'color_id' => $variant['color_id'],
                    'size_id' => $variant['size_id'],
                    'stock' => $variant['stock']
                ]);
            }
        }

        return response()->json($product->load(['category', 'subcategory', 'images', 'variants.color', 'variants.size']));
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
