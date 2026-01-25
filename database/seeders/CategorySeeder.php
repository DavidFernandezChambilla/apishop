<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Hombres', 'Mujeres', 'Niños', 'Accesorios', 'Calzado'];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "Colección de alta calidad para $name",
                'is_active' => true,
            ]);
        }
    }
}
