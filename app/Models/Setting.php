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
            // Limpieza de legacy localhost
            if (str_contains($value, 'localhost:8000/storage/')) {
                $parts = explode('/storage/', $value);
                $path = end($parts);
                return asset('storage/' . $path);
            }

            // Si es relativo, devolver URL completa
            if (!str_starts_with($value, 'http')) {
                return asset('storage/' . $value);
            }
        }

        return $value;
    }
}
