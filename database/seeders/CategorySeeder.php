<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $hierarchy = [
            'Accesorios' => ['Pareos', 'Toallas'],
            'Bikini' => ['Tops', 'Bottoms'],
            'Ropa' => ['Vestidos', 'Shorts', 'Salidas', 'Otros'],
            'Ropa De Baño' => ['Enterizos', 'Triquinis']
        ];

        foreach ($hierarchy as $parentName => $children) {
            $parent = Category::create([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'description' => "Colección de $parentName",
                'is_active' => true,
            ]);

            foreach ($children as $childName) {
                Category::create([
                    'parent_id' => $parent->id,
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'description' => "$childName dentro de la colección $parentName",
                    'is_active' => true,
                ]);
            }
        }
    }
}
