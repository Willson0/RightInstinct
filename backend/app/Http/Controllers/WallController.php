<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\EventUpdateRequest;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\WallStoreRequest;
use App\Http\utils;
use App\Models\Event;
use App\Models\Picture;
use App\Models\User;
use App\Models\Wall;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class WallController extends Controller
{
    public function store (WallStoreRequest $request) {
        $user = $request->get('user');
        if (!$user) $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        $validated = $request->validated();

        $validated["user_id"] = $user->id;

        $pictures = $validated["pictures"];
        unset($validated["pictures"]);

        $wall = Wall::create($validated);

        $index = 0;
        foreach ($pictures as $picture) {
            $time = time();
            $url = "wall/image_$time" . $index . "." . $picture->extension();
            Storage::disk("public")->putFileAs("wall", $picture, "image_$time" . $index . "." . $picture->extension());

            Picture::create([
                "type" => "wall",
                "object_id" => $wall->id,
                "url" => $url,
            ]);
            $index++;
        }

        return response()->json($wall);
    }

    public function destroy (Event $event, Request $request) {
        $user = $request->get('user');
        if (!$user) $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        if ($event->user_id !== $user->id) abort (409);

        $event->delete();
        return response()->json($event);
    }

    public function index (Request $request) {
        $data = utils::index(Event::class, $request);
        return response()->json($data);
    }

    public function show ($id, Request $request) {
        $post = Event::where("id", $id)->with("pictures")->with("user")->with("city")->with("category")->first();
        return response()->json($post);
    }

    public function update (Event $event, EventUpdateRequest $request) {
        $user = $request->get('user');
        if (!$user) $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        return utils::update($event, $user, $request, "event");
    }

    public function share (Wall $wall) {
        $wall->shares += 1;
        $wall->save();

        return response()->json("ok");
    }
}
