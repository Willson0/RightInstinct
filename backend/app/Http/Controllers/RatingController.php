<?php

namespace App\Http\Controllers;

use App\Http\Requests\Favourite\FavouriteStoreRequest;
use App\Http\Requests\Rating\RatingRateRequest;
use App\Models\Event;
use App\Models\Post;
use App\Models\Review;
use App\Models\Service;
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

    public function index (Request $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        $reviews = Review::where("user_id", $user->id)->get();

        $tables = [
            "post" => Post::class,
            "service" => Service::class,
            "event" => Event::class,
        ];
        foreach ($reviews as $review) {
            if ($review->type === "user") unset($review);
            else {
                $review->object = $tables[$review->type]::find($review->object_id);
                if (!$review->object) $review->delete();
                else {
                    $review->object->pictures;
                    $review->object->category;
                }
            }
        }
        return response()->json($reviews);
    }
}
