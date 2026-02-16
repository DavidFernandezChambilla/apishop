<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'url',
        'is_primary'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accesor para obtener la URL completa de la imagen.
     * Si la URL ya es absoluta (empieza con http), la devuelve tal cual.
     * Si es relativa, le añade el helper asset('storage/')
     */
    public function getUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // Caso especial: Si la URL guardada contiene 'localhost', significa que se guardó mal 
        // (con la URL local del desarrollador). La limpiamos para usar el dominio actual.
        if (str_contains($value, 'localhost:8000/storage/')) {
            $parts = explode('/storage/', $value);
            $path = end($parts);
            return asset('storage/' . $path);
        }

        // Si ya es una URL absoluta externa válida, la devolvemos tal cual
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        // Por defecto, asumimos que es un path relativo y usamos el helper asset
        return asset('storage/' . $value);
    }
}
