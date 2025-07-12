<?php

namespace App\Http\Controllers;

use App\Http\Requests\Favourite\FavouriteStoreRequest;
use App\Http\utils;
use App\Models\Favourite;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavouriteController extends Controller
{
    public function store (FavouriteStoreRequest $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        $data = $request->validated();
        $data["user_id"] = $user->id;
        if (Favourite::where("user_id", $user->id)->where("type", $data["type"])
            ->where("object_id", $data["object_id"])->exists()) abort(409, "Already exists");

        $favourite = Favourite::create($data);

//        try {
            utils::addNotification($user, "favourite", $data["type"], $data["object_id"]);
//        } catch (\Exception $e) {}

        return response()->json($favourite);
    }

    public function destroy (FavouriteStoreRequest $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        $data = $request->validated();
        $favourite = Favourite::where("user_id", $user->id)->where("type", $data["type"])
            ->where("object_id", $data["object_id"])->first();
        if (!$favourite) abort(409, "Not Exists");

        $favourite->delete();
        return response()->json(["ok" => "true"]);
    }

    public function index (Request $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        $favouritesIds = Favourite::where("user_id", $user->id)->get();
        $groups = $favouritesIds->groupBy("type")->toArray();

        foreach ($groups as $groupName => &$group) {
            foreach ($group as &$favourite) {
                if ($groupName === "post") {
                    if (!Post::where("id", $favourite["object_id"])->exists()) Favourite::destroy($favourite["object_id"]);
                    $favourite = Post::where("id", $favourite["object_id"])
                        ->with("pictures")->with("breed")->with("user")->with("city")->with("category")->firstOrFail();
                }
                else if ($groupName === "service") {
                    if (!Service::where("id", $favourite["object_id"])->exists()) Favourite::destroy($favourite["object_id"]);
                    $favourite = Service::where("id", $favourite["object_id"])
                        ->with("pictures")->with("user")->with("city")->with("category")->firstOrFail();
                }
                else {
                    $table = [
                        'post' => "posts",
                        'service' => "services",
                        'event' => "events",
                        'user' => "users",
                    ];
                    $object_id = $favourite["object_id"];
                    $favourite = DB::table($table[$favourite["type"]])->find($favourite["object_id"]);
                    if (!$favourite) Favourite::destroy($object_id);
                }
            }
            unset($favourite);
        }
        unset($group);
        return response()->json($groups);
    }
}
