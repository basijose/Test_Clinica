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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();
        });

        Schema::create('rubros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();
        });

        Schema::create('rubro_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubro_id')->constrained()->onDelete('cascade');
            $table->string('nombre'); // key for the JSON attribute
            $table->string('label'); // Human readable label
            $table->string('tipo'); // text, number, select, boolean
            $table->json('opciones')->nullable(); // For select type
            $table->boolean('requerido')->default(false);
            $table->timestamps();
        });

        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubro_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nombre'); // Internal identifier or name
            $table->string('serial')->nullable();
            $table->string('estado')->default('operativo'); // operativo, reparacion, baja
            $table->json('attributes')->nullable(); // Stores dynamic values based on rubro_fields
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('rubro_fields');
        Schema::dropIfExists('rubros');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('categories');
    }
};
