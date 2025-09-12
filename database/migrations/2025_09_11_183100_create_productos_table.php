<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo_barras')->nullable()->unique();
            $table->foreignId('categoria_id')->constrained()->onDelete('restrict');
            $table->string('marca')->nullable();
            $table->string('unidad_compra'); // kg, lb, unidad, caja, etc.
            $table->string('unidad_venta');  // kg, lb, unidad, etc.
            $table->decimal('factor_conversion', 8, 3)->default(1); // Ej: 1 kg = 2.2 lb
            $table->decimal('costo_compra', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('stock', 10, 3)->default(0);
            $table->decimal('stock_minimo', 10, 3)->default(0);
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('categoria_id');
            $table->index('activo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
};