<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained()->onDelete('cascade');
            $table->foreignId('producto_id')->constrained()->onDelete('restrict');
            $table->decimal('cantidad', 10, 3);
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            
            $table->index('compra_id');
            $table->index('producto_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detalle_compras');
    }
};