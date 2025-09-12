<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('descripcion');
            $table->enum('categoria', ['alquiler', 'luz', 'agua', 'transporte', 'mantenimiento', 'otros']);
            $table->decimal('monto', 10, 2);
            $table->date('fecha');
            $table->string('comprobante')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('categoria');
            $table->index('fecha');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gastos');
    }
};