<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function show (Notification $notification, Request $request) {
        $user = $request->get('user');
        if (!$user) $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        if ($notification->user_id !== $user->id) abort (409);

        $notification->readed = true;
        $notification->save();

        $tables = [
            "post" => Post::class,
            "service" => Service::class,
            "event" => Event::class,
            "user" => User::class,
        ];

        $notification->object = $tables[$notification->type]::find($notification->object_id);

        try {
            $notification->object->pictures;
            $notification->object->category;
            $notification->object->city;
            $notification->object->breed;
        } catch (\Exception $e) {}

        return response()->json($notification);
    }
}
