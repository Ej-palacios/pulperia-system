<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'apellido')) {
                $table->string('apellido')->after('name');
            }
            // ⚠️ ¡ELIMINAMOS TOTALMENTE TODO LO RELACIONADO CON 'email'!
            // No hacemos nada con 'email' porque no existe y no lo queremos.
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'apellido')) {
                $table->dropColumn('apellido');
            }
            // ⚠️ ¡NO VOLVEMOS A TOCAR 'email'!
        });
    }
};
