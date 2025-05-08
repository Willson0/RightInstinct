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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(["type", "object_id"]);
            $table->unsignedBigInteger("user_subscription_id")->after("user_id");
            $table->index("user_subscription_id");
            $table->foreign('user_subscription_id')->references("id")->on("users")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(["user_subscription_id"]);
            $table->string("type");
            $table->unsignedBigInteger("object_id");
        });
    }
};
