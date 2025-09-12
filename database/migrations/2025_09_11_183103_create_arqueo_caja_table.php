<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('arqueo_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->decimal('efectivo_inicial', 12, 2)->default(0);
            $table->decimal('efectivo_final', 12, 2);
            $table->decimal('ventas_contado', 12, 2)->default(0);
            $table->decimal('otros_ingresos', 12, 2)->default(0);
            $table->decimal('otros_gastos', 12, 2)->default(0);
            $table->decimal('total_teorico', 12, 2);
            $table->decimal('diferencia', 12, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('arqueo_caja');
    }
};