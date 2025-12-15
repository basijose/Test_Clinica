<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('estado', ['abierto', 'en_progreso', 'resuelto', 'cerrado'])->default('abierto');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->foreignId('user_id')->constrained('users'); // Creador
            $table->foreignId('assigned_to')->nullable()->constrained('users'); // Asignado a
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
