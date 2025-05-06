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
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->unsignedBigInteger("city_id");
            $table->index("city_id");
            $table->foreign("city_id")->references("id")->on("cities");

            $table->integer("price");

            $table->unsignedBigInteger("type_id");
            $table->index("type_id");
            $table->foreign("type_id")->references("id")->on("service_types");

            $table->text("description");

            $table->float("rating");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
