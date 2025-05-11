<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\EventUpdateRequest;
use App\Http\Requests\EventStoreRequest;
use App\Http\utils;
use App\Models\Event;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function store (EventStoreRequest $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        $validated = $request->validated();

        $validated["user_id"] = $user->id;
        $validated["rating"] = 0;
        $validated["moderated"] = false;

        $validated["start_date"] = Carbon::parse($validated["start_date"]);
        $validated["end_date"] = Carbon::parse($validated["end_date"]);

        $pictures = $validated["pictures"];
        unset($validated["pictures"]);

        $event = Event::create($validated);

        $index = 0;
        foreach ($pictures as $picture) {
            $time = time();
            $url = "event/image_$time" . $index . "." . $picture->extension();
            Storage::disk("public")->putFileAs("event", $picture, "image_$time" . $index . "." . $picture->extension());

            Picture::create([
                "type" => "event",
                "object_id" => $event->id,
                "url" => $url,
            ]);
            $index++;
        }

        return response()->json($event);
    }

    public function destroy (Event $event, Request $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        if ($event->user_id !== $user->id) abort (409);

        $event->delete();
        return response()->json($event);
    }

    public function index (Request $request) {
        $data = utils::index(Event::class, $request);
        return response()->json($data);
    }

    public function show ($id, Request $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        $post = Event::where("id", $id)->with("pictures")->with("user")->with("city")->with("category")->first();
        if ($post->user_id !== $user->id) abort (409);

        return response()->json($post);
    }

    public function update (Event $event, EventUpdateRequest $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        return utils::update($event, $user, $request, "event");
    }
}
