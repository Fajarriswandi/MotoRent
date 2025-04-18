<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->decimal('rental_price_hour', 12, 2)->change();
            $table->decimal('rental_price_day', 12, 2)->change();
            $table->decimal('rental_price_week', 12, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->decimal('rental_price_hour', 8, 2)->change();
            $table->decimal('rental_price_day', 8, 2)->change();
            $table->decimal('rental_price_week', 8, 2)->change();
        });
    }
};
