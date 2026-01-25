<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::query()->delete();

        // --- ACCESORIOS ---
        $pareos = Category::where('name', 'Pareos')->first();
        $toallas = Category::where('name', 'Toallas')->first();

        $p1 = Product::create([
            'category_id' => $pareos->id,
            'name' => 'Pareo Boho Multi-uso',
            'slug' => Str::slug('Pareo Boho Multi-uso'),
            'description' => 'Pareo versátil con diseño bohemio.',
            'price' => 45.00,
            'stock' => 50,
        ]);
        $this->addImage($p1->id, 'https://images.unsplash.com/photo-1596464716127-f2a82984de30?q=80&w=800');

        $p2 = Product::create([
            'category_id' => $toallas->id,
            'name' => 'Toalla de Microfibra Tropical',
            'slug' => Str::slug('Toalla de Microfibra Tropical'),
            'description' => 'Toalla de secado rápido con estampado tropical.',
            'price' => 89.90,
            'stock' => 30,
        ]);
        $this->addImage($p2->id, 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=800');

        // --- BIKINIS ---
        $tops = Category::where('name', 'Tops')->first();
        $bottoms = Category::where('name', 'Bottoms')->first();

        $p3 = Product::create([
            'category_id' => $tops->id,
            'name' => 'Top Bikini Halter',
            'slug' => Str::slug('Top Bikini Halter'),
            'description' => 'Top de bikini estilo halter en colores vibrantes.',
            'price' => 55.00,
            'stock' => 40,
        ]);
        $this->addImage($p3->id, 'https://images.unsplash.com/photo-1594212699903-ec8a3ecc50f6?q=80&w=800');

        $p4 = Product::create([
            'category_id' => $bottoms->id,
            'name' => 'Bottom High Waist',
            'slug' => Str::slug('Bottom High Waist'),
            'description' => 'Calzón de bikini tiro alto.',
            'price' => 49.90,
            'stock' => 40,
        ]);
        $this->addImage($p4->id, 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?q=80&w=800');

        // --- ROPA ---
        $vestidos = Category::where('name', 'Vestidos')->first();
        $shorts = Category::where('name', 'Shorts')->first();

        $p5 = Product::create([
            'category_id' => $vestidos->id,
            'name' => 'Vestido Maxi Mariposa',
            'slug' => Str::slug('Vestido Maxi Mariposa'),
            'description' => 'Vestido largo y fresco para el verano.',
            'price' => 120.00,
            'stock' => 20,
        ]);
        $this->addImage($p5->id, 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?q=80&w=800');

        $p6 = Product::create([
            'category_id' => $shorts->id,
            'name' => 'Short Denim Beach',
            'slug' => Str::slug('Short Denim Beach'),
            'description' => 'Short de jean cómodo.',
            'price' => 75.00,
            'stock' => 35,
        ]);
        $this->addImage($p6->id, 'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?q=80&w=800');

        // --- ROPA DE BAÑO ---
        $enterizos = Category::where('name', 'Enterizos')->first();
        $triquinis = Category::where('name', 'Triquinis')->first();

        $p7 = Product::create([
            'category_id' => $enterizos->id,
            'name' => 'Enterizo One-Piece Classic',
            'slug' => Str::slug('Enterizo One-Piece Classic'),
            'description' => 'Traje de baño enterizo elegante.',
            'price' => 149.00,
            'stock' => 15,
        ]);
        $this->addImage($p7->id, 'https://images.unsplash.com/photo-1502301197179-65217fd7ad6d?q=80&w=800');

        $p8 = Product::create([
            'category_id' => $triquinis->id,
            'name' => 'Triquini Exótico',
            'slug' => Str::slug('Triquini Exótico'),
            'description' => 'Triquini con cortes asimétricos.',
            'price' => 135.00,
            'stock' => 25,
        ]);
        $this->addImage($p8->id, 'https://images.unsplash.com/photo-1582533561751-ef6f6ab93a2e?q=80&w=800');
    }

    private function addImage($productId, $url)
    {
        ProductImage::create([
            'product_id' => $productId,
            'url' => $url,
            'is_primary' => true,
        ]);
    }
}
