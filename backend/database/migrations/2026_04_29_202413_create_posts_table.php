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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->index("user_id");
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('title');
            $table->integer("age");
            $table->boolean("gender");

            $table->unsignedBigInteger("breed_id");
            $table->index("breed_id");
            $table->foreign("breed_id")->references("id")->on("breeds");

            $table->unsignedBigInteger("city_id");
            $table->index("city_id");
            $table->foreign("city_id")->references("id")->on("cities");

            $table->integer("price");

            $table->unsignedBigInteger("category_id");
            $table->index("category_id");
            $table->foreign("category_id")->references("id")->on("categories");

            $table->text("description");
            $table->text("rewards");

            $table->float("rating");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
