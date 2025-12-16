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
        Schema::table('accesses', function (Blueprint $table) {
            $table->boolean('show_on_dashboard')->default(false)->change();
        });

        // Reset all existing records to hidden
        DB::table('accesses')->update(['show_on_dashboard' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accesses', function (Blueprint $table) {
            $table->boolean('show_on_dashboard')->default(true)->change();
        });
    }
};
