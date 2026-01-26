<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_variant') // Assuming pivot similar to colors or variants
            ->withPivot('stock')
            ->withTimestamps();
    }
}
