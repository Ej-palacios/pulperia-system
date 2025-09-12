<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('configuracion_tienda', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->default('Pulpería Managua');
            $table->decimal('impuesto', 5, 2)->default(15.00);
            $table->string('moneda')->default('C$');
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->text('mensaje_ticket')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        // Insertar configuración por defecto
        DB::table('configuracion_tienda')->insert([
            'nombre' => 'Pulpería Managua',
            'impuesto' => 15.00,
            'moneda' => 'C$',
            'mensaje_ticket' => '¡Gracias por su compra! Vuelva pronto.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('configuracion_tienda');
    }
};