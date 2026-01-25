<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Negro', 'hex_code' => '#000000'],
            ['name' => 'Blanco', 'hex_code' => '#FFFFFF'],
            ['name' => 'Rojo', 'hex_code' => '#FF0000'],
            ['name' => 'Azul Marino', 'hex_code' => '#000080'],
            ['name' => 'Azul Cielo', 'hex_code' => '#87CEEB'],
            ['name' => 'Verde', 'hex_code' => '#008000'],
            ['name' => 'Amarillo', 'hex_code' => '#FFFF00'],
            ['name' => 'Rosa', 'hex_code' => '#FFC0CB'],
            ['name' => 'Morado', 'hex_code' => '#800080'],
            ['name' => 'Naranja', 'hex_code' => '#FFA500'],
            ['name' => 'Gris', 'hex_code' => '#808080'],
            ['name' => 'Beige', 'hex_code' => '#F5F5DC'],
            ['name' => 'Coral', 'hex_code' => '#FF7F50'],
            ['name' => 'Turquesa', 'hex_code' => '#40E0D0'],
            ['name' => 'Café', 'hex_code' => '#8B4513'],
        ];

        foreach ($colors as $color) {
            Color::create([
                'name' => $color['name'],
                'hex_code' => $color['hex_code'],
                'is_active' => true
            ]);
        }
    }
}
