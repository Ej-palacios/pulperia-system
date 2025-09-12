<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained()->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->decimal('monto', 12, 2);
            $table->decimal('saldo_pendiente', 12, 2);
            $table->date('fecha_limite');
            $table->enum('estado', ['pendiente', 'parcialmente_pagado', 'pagado', 'vencido'])->default('pendiente');
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->index('venta_id');
            $table->index('cliente_id');
            $table->index('estado');
            $table->index('fecha_limite');
        });
    }

    public function down()
    {
        Schema::dropIfExists('creditos');
    }
};