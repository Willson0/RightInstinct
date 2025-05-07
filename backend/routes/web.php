<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\PostController;
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
    });

    Route::group(["prefix" => "data"], function () {
        Route::get("/", [DataController::class, 'index']);
        Route::get("/category", [DataController::class, 'category']);
    });

    Route::group(["prefix" => "chat", "middleware" => CheckTelegram::class], function () {
        Route::post("/{companion}", [ChatController::class, 'show']);
        Route::post("/{companion}/send", [ChatController::class, 'send']);
    });
});
