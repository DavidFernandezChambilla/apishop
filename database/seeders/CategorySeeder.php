<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $hierarchy = [
            'Abrigos' => ['Largo Moderno', 'Corto Casual', 'Edición Limitada'],
            'Polos' => ['100% Baby Alpaca', 'Mezcla Premium', 'Cuello Alto'],
            'Accesorios' => ['Bufandas', 'Gorros', 'Chales', 'Guantes'],
            'Colección Luxury' => ['Hand-Spun Gold', 'Heritage Series']
        ];

        foreach ($hierarchy as $parentName => $children) {
            $category = Category::create([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'description' => "Colección de $parentName",
                'is_active' => true,
            ]);

            foreach ($children as $childName) {
                Subcategory::create([
                    'category_id' => $category->id,
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'description' => "$childName dentro de la colección $parentName",
                    'is_active' => true,
                ]);
            }
        }
    }
}
