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

    protected $appends = ['image_url'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accesor para obtener la URL completa de la imagen.
     * Se accede como $image->image_url o en JSON como "image_url"
     */
    public function getImageUrlAttribute()
    {
        if (!array_key_exists('url', $this->attributes) || !$this->attributes['url']) {
            return null;
        }

        $value = $this->attributes['url'];

        // Si es una URL absoluta (contiene http), intentamos extraer solo la ruta relativa
        // para reconstruirla con el APP_URL actual. Esto evita problemas si se guardan
        // URLs absolutas de otros entornos (localhost o dominios antiguos).
        if (str_contains($value, 'http')) {
            if (str_contains($value, '/storage/')) {
                $parts = explode('/storage/', $value);
                $value = end($parts);
            } else {
                // Si es una URL externa (tipo Unsplash), la dejamos tal cual
                return $value;
            }
        }

        // Si por algún motivo el valor empieza con 'storage/', lo limpiamos
        if (str_starts_with($value, 'storage/')) {
            $value = substr($value, 8);
        }

        // URL base para storage: usa ASSET_URL si está definido, si no usa APP_URL
        // Esto es necesario porque APP_URL puede terminar en /api
        // pero el storage se sirve desde la raíz del public de Laravel
        $baseUrl = rtrim(config('app.asset_url') ?: config('app.url'), '/');

        // Si APP_URL termina en /api, lo removemos para la URL de storage
        // porque el storage symlink está en public_html/api/storage/
        // y la URL correcta es https://qaytuperu.com/api/storage/...
        // (no https://qaytuperu.com/api/api/storage/...)
        $url = $baseUrl . '/storage/' . ltrim($value, '/');

        // Si no estamos en localhost, forzamos HTTPS para evitar bloqueos de contenido mixto
        if (!str_contains($url, 'localhost') && !str_contains($url, '127.0.0.1')) {
            $url = str_replace('http://', 'https://', $url);
        }

        return $url;
    }
}
