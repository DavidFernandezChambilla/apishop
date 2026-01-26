<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'U']; // U = Unitalla

        foreach ($sizes as $size) {
            Size::create([
                'name' => $size,
                'slug' => Str::slug($size),
                'is_active' => true
            ]);
        }
    }
}
