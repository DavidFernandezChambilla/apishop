<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'status',
        'transaction_id',
        'proof_image'
    ];

    protected $appends = ['proof_image_url'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Accesor para la imagen de prueba de pago.
     */
    public function getProofImageUrlAttribute()
    {
        $value = $this->attributes['proof_image'] ?? null;
        if (!$value)
            return null;

        if (str_contains($value, 'http')) {
            if (str_contains($value, '/storage/')) {
                $parts = explode('/storage/', $value);
                $value = end($parts);
            } else {
                return $value;
            }
        }

        if (str_starts_with($value, 'storage/')) {
            $value = substr($value, 8);
        }

        $baseUrl = rtrim(config('app.url'), '/');
        $url = $baseUrl . '/storage/' . ltrim($value, '/');

        if (!str_contains($url, 'localhost') && !str_contains($url, '127.0.0.1')) {
            $url = str_replace('http://', 'https://', $url);
        }
        return $url;
    }
}
