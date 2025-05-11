<?php

namespace App\Http\Controllers;

use App\Http\utils;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function subscribe (Request $request) {
        if (!$request->has("user_subscription_id")) abort (422, "user_subscription_id is required!");

        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        if (Subscription::where("user_id", $user->id)->where("user_subscription_id", $request->user_subscription_id)->exists())
            abort(409, "Already exists");

        $response = Subscription::create([
            "user_id" => $user->id,
            "user_subscription_id" => $request->user_subscription_id,
        ]);

        utils::addNotification($user, "subscribe", "user", $request->user_subscription_id);

        return response()->json($response);
    }

    public function unsubscribe (Request $request) {
        if (!$request->has("user_subscription_id")) abort(422, "user_subscription_id is required!");

        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        $subscription = Subscription::where("user_id", $user->id)->where("user_subscription_id", $request->user_subscription_id)->first();

        if (!$subscription) abort(409, "Not exists");

        $subscription->delete();
        return response()->json(["ok" => true]);
    }
}
