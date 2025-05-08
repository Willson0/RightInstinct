<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthStoreRequest;
use App\Models\Event;
use App\Models\Message;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function profile (Request $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->first();
        if (!$user) {
            $user = User::create([
                "telegram_id" => $request["initData"]["user"]["id"],
                "fullname" => $request["initData"]["user"]["first_name"] . " " . $request["initData"]["user"]["last_name"],
                "username" => $request["initData"]["user"]["username"] ?? null,
                "notification" => false,
                "rating" => 0,
                "avatar" => $request["initData"]["user"]["photo_url"],
            ]);
        }
        $user->city;
        $user->notifications;
        $user->subscriptions = $user->subscriptions()->with("user_subscription")->get();

        foreach ($user->subscriptions as $us) {
            $us->user_subscription->city;
        }

        $userId = $user->id;
        $dialogQuery = Message::selectRaw('
                CASE
                    WHEN sender_id = ? THEN recipient_id
                    ELSE sender_id
                END as companion_id
            ', [$user->id])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->orWhere('recipient_id', $userId);
            })
            ->groupBy('companion_id');
        $companionIds = $dialogQuery->pluck('companion_id');
        $dialogs = [];

        foreach ($companionIds as $companionId) {
            $lastMessage = Message::where(function($q) use ($userId, $companionId) {
                $q->where('sender_id', $userId)->where('recipient_id', $companionId);
            })->orWhere(function($q) use ($userId, $companionId) {
                $q->where('sender_id', $companionId)->where('recipient_id', $userId);
            })->orderByDesc('id')->first();

            $unreadCount = Message::where('sender_id', $companionId)
                ->where('recipient_id', $userId)
                ->where('readed', 0)
                ->count();

            $companion = User::find($companionId);
            $dialogs[] = [
                "user" => $companion,
                "unreaded" => $unreadCount,
                "last_message" => $lastMessage ? $lastMessage->message : "",
                "from_last_message" => $lastMessage && $lastMessage->sender_id == $userId ? 0 : 1,
                "checked" => $lastMessage
                    ? ($lastMessage->sender_id == $userId ? ($lastMessage->readed ? 1 : 0) : 0)
                    : 0,
            ];
        }

        $user["chat"] = $dialogs;

        $feed = [];
        $feed["posts"] = Post::limit(10)->with("pictures")->with("breed")->with("user")->with("city")->with("category")->get();
        $feed["services"] = Service::limit(10)->with("city")->get();
        $feed["popular"] = null;
        $feed["events"] = Event::limit(10)->with("city")->get();

        $user["feed"] = $feed;

        $my = [];
        $my["posts"] = $user->posts()->with("pictures")->with("breed")->with("user")->with("city")->with("category")->get();
        $my["services"] = $user->services;
        $my["events"] = $user->events;

        $user["my"] = $my;

        return response()->json($user);
    }

    public function update (AuthStoreRequest $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->first();
        $data = $request->validated();

        $user->update($data);
        return $this->profile($request);
    }

     public function show (User $user, Request $request) {
        $user->city;
        $user->posts;
        $user->services;

        return response()->json($user);
    }
}
