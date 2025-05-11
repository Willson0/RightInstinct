<?php

namespace App\Http\Controllers;

use App\Http\Requests\Service\ServiceStoreRequest;
use App\Http\Requests\Service\ServiceUpdateRequest;
use App\Http\utils;
use App\Models\Picture;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function store (ServiceStoreRequest $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        $validated = $request->validated();

        $validated["user_id"] = $user->id;
        $validated["rating"] = 0;

        $pictures = $validated["pictures"];
        unset($validated["pictures"]);

        $service = Service::create($validated);

        $index = 0;
        foreach ($pictures as $picture) {
            $time = time();
            $url = "service/image_$time" . $index . "." . $picture->extension();
            Storage::disk("public")->putFileAs("service", $picture, "image_$time" . $index . "." . $picture->extension());

            Picture::create([
                "type" => "service",
                "object_id" => $service->id,
                "url" => $url,
            ]);
            $index++;
        }

        return response()->json($service);
    }

    public function destroy (Service $service, Request $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        if ($service->user_id !== $user->id) abort (409);

        $service->delete();
        return response()->json($service);
    }

    public function index (Request $request) {
        $data = utils::index(Service::class, $request);
        return response()->json($data);
    }

    public function show ($id, Request $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        $post = Service::where("id", $id)->with("pictures")->with("user")->with("city")->with("category")->first();
        if ($post->user_id !== $user->id) abort (409);

        return response()->json($post);
    }

    public function update (Service $service, ServiceUpdateRequest $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        return utils::update($service, $user, $request, "service");
    }
}
