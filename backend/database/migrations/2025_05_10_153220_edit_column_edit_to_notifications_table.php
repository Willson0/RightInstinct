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
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn("data");
            $table->string("title")->after("user_id");
            $table->text("description")->after("title");
            $table->string("type")->after("description");
            $table->unsignedBigInteger("object_id")->after("type");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(["title", "description", "type", "object_id"]);
            $table->json('data')->nullable();
        });
    }
};
