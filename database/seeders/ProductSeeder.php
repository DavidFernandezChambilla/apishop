<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $hombres = Category::where('name', 'Hombres')->first();
        $mujeres = Category::where('name', 'Mujeres')->first();

        // Producto 1
        $p1 = Product::create([
            'category_id' => $hombres->id,
            'name' => 'Camiseta Premium de Algodón',
            'slug' => Str::slug('Camiseta Premium de Algodón'),
            'description' => 'Camiseta de algodón 100% orgánico, tacto suave y corte moderno.',
            'price' => 29.90,
            'stock' => 100,
        ]);

        ProductImage::create([
            'product_id' => $p1->id,
            'url' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?q=80&w=800',
            'is_primary' => true,
        ]);

        foreach (['S', 'M', 'L', 'XL'] as $size) {
            ProductVariant::create([
                'product_id' => $p1->id,
                'size' => $size,
                'color' => 'Blanco',
                'stock' => 25,
            ]);
        }

        // Producto 2
        $p2 = Product::create([
            'category_id' => $mujeres->id,
            'name' => 'Vestido Floral de Verano',
            'slug' => Str::slug('Vestido Floral de Verano'),
            'description' => 'Vestido ligero con estampado floral, ideal para días soleados.',
            'price' => 45.00,
            'stock' => 50,
        ]);

        ProductImage::create([
            'product_id' => $p2->id,
            'url' => 'https://images.unsplash.com/photo-1572804013307-a9a111281b6a?q=80&w=800',
            'is_primary' => true,
        ]);

        ProductVariant::create([
            'product_id' => $p2->id,
            'size' => 'M',
            'color' => 'Azul Floral',
            'stock' => 50,
        ]);
    }
}
