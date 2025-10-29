<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteNotificationsController extends Controller
{
    public function store (Request $request) {
        $user = $request->get('user');
        if (!$user) abort(401);
        if (!$request->has("settings")) abort(400, "Неправильный запрос на сервер");

        $settings = $request->get("settings");
        $notifications = ['message', 'subscription', 'comment', "repost", "favourite_post",
            "favourite_service", "favourite_event", "event", "anons"];

        $settings = array_values(array_intersect($settings, $notifications));
        $user->notifications_settings = $settings;
        $user->save();

        return response()->json("ok");
    }
}
