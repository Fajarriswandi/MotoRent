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
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->dropColumn(['rental_price_hour', 'rental_price_week']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->dropColumn(['rental_price_hour', 'rental_price_week']);
        });

    }
};
