<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ComplainController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Site\SiteAuthController;
use App\Http\Controllers\Site\SiteDocumentsController;
use App\Http\Controllers\Site\SiteFeedController;
use App\Http\Controllers\Site\SiteNotificationsController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\CheckAdminMiddleware;
use App\Http\Middleware\CheckTelegram;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "api"], function () {
    Route::group(["prefix" => "site"], function () {
        Route::group(["prefix" => "auth"], function () {
            Route::post("register", [SiteAuthController::class, "register"]);
            Route::post("verify", [SiteAuthController::class, 'verify']);
            Route::post("login", [SiteAuthController::class, "login"]);
            Route::post("telegram", [SiteAuthController::class, 'telegram']);

            Route::group(["prefix" => "recovery"], function () {
                Route::post("send", [SiteAuthController::class, "recoverySend"]);
                Route::post("check", [SiteAuthController::class, "recoveryCheck"]);
            });

            Route::post("notifications", [SiteNotificationsController::class, 'store'])->middleware(CheckTelegram::class);

            Route::post("settings", [SiteAuthController::class, 'settings'])->middleware(CheckTelegram::class);
            Route::post("change", [SiteAuthController::class, 'change'])->middleware(CheckTelegram::class);
        });
        Route::group(["prefix" => "document", "middleware" => CheckTelegram::class], function () {
            Route::post("/", [SiteDocumentsController::class, "store"]);
            Route::post("/change", [SiteDocumentsController::class, "change"]);
        });
        Route::post("/test", [SiteAuthController::class, 'test']);
        Route::get("/feed", [SiteFeedController::class, 'index']);
    });

    Route::group(["prefix" => "auth", "middleware" => CheckTelegram::class], function () {
        Route::post("profile", [AuthController::class, 'profile']);
        Route::post("update", [AuthController::class, 'update']);
    });

    Route::get("/post", [PostController::class, 'index'])->middleware(CheckTelegram::class);;
    Route::any("/post/{id}", [PostController::class, 'show']);
    Route::group(["prefix" => "post", "middleware" => CheckTelegram::class], function () {
        Route::post("/", [PostController::class, 'store']);
        Route::post("/{post}/delete", [PostController::class, 'destroy']);
        Route::post("/{post}/update", [PostController::class, 'update']);
    });

    Route::get("/service", [ServiceController::class, 'index'])->middleware(CheckTelegram::class);;
    Route::any("/service/{id}", [ServiceController::class, 'show']);
    Route::group(["prefix" => "service", "middleware" => CheckTelegram::class], function () {
        Route::post("/", [ServiceController::class, 'store']);
        Route::post("/{service}/delete", [ServiceController::class, 'destroy']);
        Route::post("/{service}/update", [ServiceController::class, 'update']);
    });

    Route::get("/event", [EventController::class, 'index'])->middleware(CheckTelegram::class);;
    Route::any("/event/{id}", [EventController::class, 'show']);
    Route::group(["prefix" => "event", "middleware" => CheckTelegram::class], function () {
        Route::post("/", [EventController::class, 'store']);
        Route::post("/{event}/delete", [EventController::class, 'destroy']);
        Route::post("/{event}/update", [EventController::class, 'update']);
    });

    Route::get("/wall", [WallController::class, 'index'])->middleware(CheckTelegram::class);;
    Route::any("/wall/{id}", [WallController::class, 'show']);
    Route::group(["prefix" => "wall", "middleware" => CheckTelegram::class], function () {
        Route::post("/", [WallController::class, 'store']);
        Route::post("/{wall}/delete", [WallController::class, 'destroy']);
        Route::post("/{wall}/update", [WallController::class, 'update']);
        Route::post("/{wall}/share", [WallController::class, 'share']);
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
        Route::get('events/moderate', [AdminController::class, 'moderate']);
        Route::get('events/moderate/{event}/accept', [AdminController::class, 'moderateAccept']);
        Route::get('events/moderate/{event}/delete', [AdminController::class, 'moderateDelete']);
        Route::get("{type}/{id}", [AdminController::class, 'show']);
        Route::delete("{type}/{id}", [AdminController::class, 'destroy']);
    });

    Route::group(["prefix" => "complain"], function () {
       Route::post("/", [ComplainController::class, 'store'])->middleware(CheckTelegram::class);
        Route::get("/", [ComplainController::class, 'index'])->middleware(CheckAdminMiddleware::class);
        Route::delete("/{complain}", [ComplainController::class, 'index'])->middleware(CheckAdminMiddleware::class);
    });

    Route::group(["prefix" => "stats", "middleware" => CheckAdminMiddleware::class], function () {
        Route::get("/", [StatsController::class, "index"]);
    });

    Route::group(["prefix" => "webhook"], function () {
        Route::post("/tg", [WebhookController::class, 'tg']);
    });
});
