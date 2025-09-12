<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('numero_factura')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('impuestos', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('tipo_pago', ['contado', 'credito'])->default('contado');
            $table->enum('estado', ['pendiente', 'completada', 'anulada'])->default('completada');
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('proveedor_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('compras');
    }
};