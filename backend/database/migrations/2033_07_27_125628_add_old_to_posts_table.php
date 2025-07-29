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
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean("is_old")->default(true)->after("user_id");

            $table->string("father")->nullable()->after("breed_id");
            $table->string("mother")->nullable()->after("breed_id");

            $table->string("nursery")->nullable()->after("city_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(["is_old", "father_breed_id", "mother_breed_id", "nursery"]);
        });
    }
};
