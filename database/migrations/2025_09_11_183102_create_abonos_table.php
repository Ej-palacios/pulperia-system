<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('abonos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credito_id')->constrained()->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'datáfono'])->default('efectivo');
            $table->timestamps();
            
            $table->index('credito_id');
            $table->index('cliente_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abonos');
    }
};