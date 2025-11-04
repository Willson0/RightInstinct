<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthStoreRequest;
use App\Models\Event;
use App\Models\Favourite;
use App\Models\Message;
use App\Models\Post;
use App\Models\Service;
use App\Models\Site\UserEmail;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Wall;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function profile (Request $request) {
        $user = $request->get('user');
        if (!$user) $user = User::where("telegram_id", $request["initData"]["user"]["id"])->first();

        if (!$user && isset($request["initData"])) {
            $user = User::create([
                "telegram_id" => $request["initData"]["user"]["id"],
                "fullname" => $request["initData"]["user"]["first_name"] . " " . $request["initData"]["user"]["last_name"],
                "username" => $request["initData"]["user"]["username"] ?? null,
                "notification" => false,
                "rating" => 0,
                "avatar" => $request["initData"]["user"]["photo_url"],
            ]);
        } else if (!$user) abort (401);
        $user->city;
        $user->notifications;
        $user->subscriptions = $user->subscriptions()->with("user_subscription")->get();
        $user->documents = json_decode($user->documents, 1);
        $user->notifications_settings = json_decode($user->notifications_settings, 1);
        $user->reviews = $user->reviews()->get()->groupBy("type")->map(function ($group) {
            return $group->map(function ($review) {
                return [
                    'id' => $review->object_id,
                    'rating' => $review->rating,
                ];
            });
        })
        ->toArray();

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

        $favourites = Favourite::where('user_id', $userId)->get();

        $user["favourites"] = $favourites->groupBy('type')->map(function($items) {
            return $items->pluck('object_id')->toArray();
        })->toArray();

        $user["chat"] = $dialogs;

        $feed = [];
        $feed["posts"] = Post::limit(10)->orderByDesc("id")->with("pictures")->with("breed")->with("user")->with("city")->with("category")->get();
        $feed["services"] = Service::limit(10)->orderByDesc("id")->with("pictures")->with("user")->with("city")->with("category")->get();

        $postsPopular = Post::orderByDesc("rating")->limit(15)->with(["pictures", "breed", "user", "city", "category"])->get();
        $servicesPopular = Service::orderByDesc("rating")->limit(15)->with(["pictures", "user", "city", "category"])->get();

        $items = $postsPopular
            ->concat($servicesPopular)
            ->sortByDesc('rating')
            ->take(10)
            ->values();
        $feed["popular"] = $items;

        $feed["events"] = Event::limit(10)->orderByDesc("id")->with("pictures")->with("user")->with("city")->with("category")->where("moderated", "1")->get();

        $user["feed"] = $feed;

        $my = [];
        $my["posts"] = $user->posts()->with("pictures")->with("breed")->with("user")->with("city")->with("category")->get();
        $my["services"] = $user->services()->with("pictures")->with("user")->with("city")->with("category")->get();
        $my["events"] = $user->events()->with("pictures")->with("user")->with("city")->with("category")->get();;
        $my["walls"] = $user->walls()->with("pictures")->with("user")->get();

        $user["my"] = $my;

        return response()->json($user);
    }

    public function update (AuthStoreRequest $request) {
        $user = $request->get('user');
        if (!$user) $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        $data = $request->validated();

        if ($request->has("email"))
            if ((User::where('email', $data['email'])->exists())
            OR (UserEmail::where('email', $data['email'])->exists()))
                unset ($data['email']);

        $user->update($data);
        return $this->profile($request);
    }

     public function show (User $user, Request $request) {
        $sender = $request->get('user');
        if (!$sender) $sender = User::where("telegram_id", $request["initData"]["user"]["id"] ?? null)->first();

        $user->city;
        $user->posts = $user->posts()->with("pictures")->with("breed")->with("city")->with("category")->get();
        $user->services = $user->services()->with("pictures")->with("user")->with("city")->with("category")->get();
        $user->events = $user->events()->where("moderated", "1")->with("pictures")->with("user")->with("city")->with("category")->get();
        $user->walls = $user->walls()->with("pictures")->with("user")->get();

        if ($sender) {
            if (Subscription::where("user_id", $sender->id)->where("user_subscription_id", $user->id)->exists())
                $user->isSubscribe = true;
            else $user->isSubscribe = false;
        }

        return response()->json($user);
    }
}
