<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->decimal('cantidad', 10, 3);
            $table->enum('tipo', ['entrada', 'salida', 'ajuste']);
            $table->string('motivo');
            $table->morphs('referencia'); // Para relacionar con ventas, compras, ajustes, etc.
            $table->timestamps();
            
            $table->index('producto_id');
            $table->index('user_id');
            $table->index('tipo');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};