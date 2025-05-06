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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->index("user_id");
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('title');
            $table->text("description");

            $table->unsignedBigInteger("city_id");
            $table->index("city_id");
            $table->foreign("city_id")->references("id")->on("cities");

            $table->dateTime("start_date");
            $table->dateTime("end_date");

            $table->unsignedBigInteger("type_id");
            $table->index("type_id");
            $table->foreign("type_id")->references("id")->on("service_types");

            $table->text("details");

            $table->float("rating");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
