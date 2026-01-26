<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Si existe la tabla antigua de variantes (creada en migraciones anteriores), la eliminamos
        Schema::dropIfExists('product_variants');
        // También eliminamos la tabla product_color que acabamos de crear, ya que esta la reemplazará
        Schema::dropIfExists('product_color');

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('color_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->constrained()->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->decimal('price_adjustment', 10, 2)->default(0); // Por si una talla/color cuesta más
            $table->timestamps();

            // Una variante es única por producto + color + talla
            $table->unique(['product_id', 'color_id', 'size_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
