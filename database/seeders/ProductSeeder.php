<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::query()->delete();

        // --- ABRIGOS ---
        $abrigos = Category::where('name', 'Abrigos')->first();
        $largo = Subcategory::where('name', 'Largo Moderno')->first();
        $limitada = Subcategory::where('name', 'Edición Limitada')->first();

        $p1 = Product::create([
            'category_id' => $abrigos->id,
            'subcategory_id' => $largo->id,
            'name' => 'Mantón Heritage Oversize',
            'slug' => Str::slug('Manton Heritage Oversize'),
            'description' => 'Abrigo largo de 100% fibra de alpaca, tejido a mano por maestros artesanos de Arequipa. Una pieza eterna.',
            'price' => 450.00,
            'stock' => 10,
        ]);
        $this->addImage($p1->id, 'https://images.unsplash.com/photo-1544022613-e87dd75a784a?q=80&w=800');

        $p2 = Product::create([
            'category_id' => $abrigos->id,
            'subcategory_id' => $limitada->id,
            'name' => 'Abrigo Imperial Black-Gold',
            'slug' => Str::slug('Abrigo Imperial Black Gold'),
            'description' => 'Edición limitada de alpaca negra natural, sin tintes. Máxima suavidad y calidez para el invierno alemán.',
            'price' => 680.00,
            'stock' => 5,
        ]);
        $this->addImage($p2->id, 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?q=80&w=800');

        // --- POLOS & SWEATERS ---
        $polos = Category::where('name', 'Polos')->first();
        $babyAlpaca = Subcategory::where('name', '100% Baby Alpaca')->first();
        $cuelloAlto = Subcategory::where('name', 'Cuello Alto')->first();

        $p3 = Product::create([
            'category_id' => $polos->id,
            'subcategory_id' => $babyAlpaca->id,
            'name' => 'Polo Essence Antracita',
            'slug' => Str::slug('Polo Essence Antracita'),
            'description' => 'La versatilidad de la fibra de alpaca en un diseño minimalista. 100% Baby Alpaca.',
            'price' => 189.00,
            'stock' => 25,
        ]);
        $this->addImage($p3->id, 'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?q=80&w=800');

        $p4 = Product::create([
            'category_id' => $polos->id,
            'subcategory_id' => $cuelloAlto->id,
            'name' => 'Sweater Alpaca Nordic Grey',
            'slug' => Str::slug('Sweater Alpaca Nordic Grey'),
            'description' => 'Tejido grueso con patrón tradicional revisado. Perfecto para el estilo contemporáneo.',
            'price' => 245.00,
            'stock' => 15,
        ]);
        $this->addImage($p4->id, 'https://images.unsplash.com/photo-1516762689617-e1cffcef479d?q=80&w=800');

        // --- ACCESORIOS ---
        $accesorios = Category::where('name', 'Accesorios')->first();
        $bufandas = Subcategory::where('name', 'Bufandas')->first();
        $chales = Subcategory::where('name', 'Chales')->first();

        $p5 = Product::create([
            'category_id' => $accesorios->id,
            'subcategory_id' => $bufandas->id,
            'name' => 'Bufanda Infinito Alpaca',
            'slug' => Str::slug('Bufanda Infinito Alpaca'),
            'description' => 'Tacto de seda, calidez de los Andes. 100% fibra natural hipoalergénica.',
            'price' => 85.00,
            'stock' => 50,
        ]);
        $this->addImage($p5->id, 'https://images.unsplash.com/photo-1520903920243-00d872a2d1c9?q=80&w=800');

        $p6 = Product::create([
            'category_id' => $accesorios->id,
            'subcategory_id' => $chales->id,
            'name' => 'Chal Ceremonial Blanco',
            'slug' => Str::slug('Chal Ceremonial Blanco'),
            'description' => 'Elegancia pura en alpaca blanca. Ideal para eventos exclusivos.',
            'price' => 125.00,
            'stock' => 20,
        ]);
        $this->addImage($p6->id, 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?q=80&w=800');

        // --- LUXURY ---
        $luxury = Category::where('name', 'Colección Luxury')->first();
        $handSpun = Subcategory::where('name', 'Hand-Spun Gold')->first();

        $p7 = Product::create([
            'category_id' => $luxury->id,
            'subcategory_id' => $handSpun->id,
            'name' => 'Capa Real de Vicuña & Alpaca',
            'slug' => Str::slug('Capa Real De Vicuna Alpaca'),
            'description' => 'La joya de la corona. Combinación de las fibras más finas del mundo en una pieza numerada.',
            'price' => 1200.00,
            'stock' => 2,
        ]);
        $this->addImage($p7->id, 'https://images.unsplash.com/photo-1544022613-e87dd75a784a?q=80&w=800');
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
