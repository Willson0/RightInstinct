<?php

namespace App\Http\Controllers;

use App\Http\Requests\Favourite\FavouriteStoreRequest;
use App\Http\Requests\Rating\RatingRateRequest;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function rate (RatingRateRequest $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        $tables = [
            "post" => "posts",
            "service" => "services",
            "event" => "events",
            "user" => "users",
        ];
        $table = $tables[$request["type"]];

        $review = Review::where("user_id", $user->id)->where("type", $request["type"])
            ->where("object_id", $request["object_id"])->first();
        if ($review) {
            $review->rating = $request["rating"];
            $review->save();
        } else {
            $review = Review::create([
                "user_id" => $user->id,
                "object_id" => $request["object_id"],
                "type" => $request["type"],
                "rating" => $request["rating"],
            ]);
        }
        DB::table($table)->where("id", $request["object_id"])->update([
            "rating" => Review::where("type", $request["type"])
                ->where("object_id", $request["object_id"])->avg("rating"),
        ]);
        return response()->json($review);
    }
}
