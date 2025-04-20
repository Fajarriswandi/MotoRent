<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->decimal('price_hour', 10, 2)->nullable();
            $table->decimal('price_day', 10, 2)->nullable();
            $table->decimal('price_week', 10, 2)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->decimal('price_hour', 10, 2)->nullable();
            $table->decimal('price_day', 10, 2)->nullable();
            $table->decimal('price_week', 10, 2)->nullable();
        });

    }
};
