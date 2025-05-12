<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckAdminMiddleware;
use App\Http\Middleware\CheckTelegram;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "api"], function () {
    Route::group(["prefix" => "auth", "middleware" => CheckTelegram::class], function () {
        Route::post("profile", [AuthController::class, 'profile']);
        Route::post("update", [AuthController::class, 'update']);
    });

    Route::get("/post", [PostController::class, 'index']);
    Route::group(["prefix" => "post", "middleware" => CheckTelegram::class], function () {
        Route::post("/", [PostController::class, 'store']);
        Route::post("/{post}/delete", [PostController::class, 'destroy']);
        Route::post("/{id}", [PostController::class, 'show']);
        Route::post("/{post}/update", [PostController::class, 'update']);
    });

    Route::get("/service", [ServiceController::class, 'index']);
    Route::group(["prefix" => "service", "middleware" => CheckTelegram::class], function () {
        Route::post("/", [ServiceController::class, 'store']);
        Route::post("/{service}/delete", [ServiceController::class, 'destroy']);
        Route::post("/{id}", [ServiceController::class, 'show']);
        Route::post("/{service}/update", [ServiceController::class, 'update']);
    });

    Route::get("/event", [EventController::class, 'index']);
    Route::group(["prefix" => "event", "middleware" => CheckTelegram::class], function () {
        Route::post("/", [EventController::class, 'store']);
        Route::post("/{event}/delete", [EventController::class, 'destroy']);
        Route::post("/{id}", [EventController::class, 'show']);
        Route::post("/{event}/update", [EventController::class, 'update']);
    });

    Route::group(["prefix" => "data"], function () {
        Route::get("/", [DataController::class, 'index']);
        Route::get("/category", [DataController::class, 'category']);
    });

    Route::group(["prefix" => "chat", "middleware" => CheckTelegram::class], function () {
        Route::post("/{companion}", [ChatController::class, 'show']);
        Route::post("/{companion}/send", [ChatController::class, 'send']);
    });

    Route::post("/user/{user}", [AuthController::class, 'show'])->middleware(CheckTelegram::class);
    Route::get("/users", [UserController::class, "index"])->middleware(CheckAdminMiddleware::class);

    Route::group(["prefix" => "subscription", "middleware" => CheckTelegram::class], function () {
        Route::post("/subscribe", [SubscriptionController::class, 'subscribe']);
        Route::post("/unsubscribe", [SubscriptionController::class, 'unsubscribe']);
    });

    Route::group(["prefix" => "favourite", "middleware" => CheckTelegram::class], function () {
        Route::post("/", [FavouriteController::class, 'store']);
        Route::post("/delete", [FavouriteController::class, 'destroy']);
        Route::post("/index", [FavouriteController::class, 'index']);
    });

    Route::group(["prefix" => "rating", "middleware" => CheckTelegram::class], function () {
        Route::post("/rate", [RatingController::class, 'rate']);
        Route::post("/", [RatingController::class, "index"]);
    });

    Route::group(["prefix" => "notification", "middleware" => CheckTelegram::class], function () {
        Route::post("/{notification}", [NotificationController::class, 'show']);
    });

    Route::post("/admin/login", [AdminController::class, "login"]);
    Route::group(["prefix" => "admin", "middleware" => CheckAdminMiddleware::class], function () {
        Route::get("profile", [AdminController::class, "profile"]);
        Route::get('posts', [AdminController::class, 'posts']);
        Route::get('services', [AdminController::class, 'services']);
        Route::get('events', [AdminController::class, 'events']);
    });

    Route::group(["prefix" => "stats", "middleware" => CheckAdminMiddleware::class], function () {
        Route::get("/", [StatsController::class, "index"]);
    });
});
