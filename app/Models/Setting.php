<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Accesor para procesar el valor de la configuración.
     * Si el key es sugerente de un archivo (como yape_qr), devuelve la URL completa.
     */
    public function getValueAttribute($value)
    {
        if (!$value) {
            return $value;
        }

        // Si la llave es yape_qr o contiene '_image', '_logo', '_file'
        // y el valor no es una URL absoluta ya, lo convertimos.
        $imageKeys = ['yape_qr', 'logo', 'favicon'];

        $isImageKey = false;
        foreach ($imageKeys as $imageKey) {
            if (str_contains($this->key, $imageKey)) {
                $isImageKey = true;
                break;
            }
        }

        if ($isImageKey) {
            // Limpieza de legacy localhost o rutas absolutas mal construidas
            if (str_contains($value, 'http')) {
                if (str_contains($value, '/storage/')) {
                    $parts = explode('/storage/', $value);
                    $value = end($parts);
                } else {
                    return $value;
                }
            }

            // Construcción manual de la URL para máxima compatibilidad con CPanel
            $baseUrl = rtrim(config('app.url'), '/');
            $url = $baseUrl . '/storage/' . ltrim($value, '/');

            if (!str_contains($url, 'localhost') && !str_contains($url, '127.0.0.1')) {
                $url = str_replace('http://', 'https://', $url);
            }
            return $url;
        }

        return $value;
    }
}
