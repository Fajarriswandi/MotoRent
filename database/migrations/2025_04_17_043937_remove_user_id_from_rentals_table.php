<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // jika ada foreign key
            $table->dropColumn('user_id');
        });
    }

    public function down()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
        });
    }

};
