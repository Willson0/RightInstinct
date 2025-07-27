<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\PostStoreRequest;
use App\Http\Requests\Post\PostUpdateRequest;
use App\Http\utils;
use App\Models\Picture;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function store (PostStoreRequest $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        $validated = $request->validated();

        if (($validated["is_old"] === 0) and
            (!isset($validated["mother_breed_id"]) or !isset($validated["father_breed_id"])))
            abort (409);

        $validated["user_id"] = $user->id;
        $validated["rating"] = 0;

        $pictures = $validated["pictures"];
        unset($validated["pictures"]);

        $post = Post::create($validated);

        $index = 0;
        foreach ($pictures as $picture) {
            $time = time();
            $url = "post/image_$time" . $index . "." . $picture->extension();
            Storage::disk("public")->putFileAs("post", $picture, "image_$time" . $index . "." . $picture->extension());

            Picture::create([
                "type" => "post",
                "object_id" => $post->id,
                "url" => $url,
            ]);
            $index++;
        }

        return response()->json($post);
    }

    public function destroy (Post $post, Request $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        if ($post->user_id !== $user->id) abort (409);

        $post->delete();
        return response()->json($post);
    }

    public function index (Request $request) {
        $data = utils::index(Post::class, $request);
        return response()->json($data);
    }

    public function show ($id, Request $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        $post = Post::where("id", $id)->with("pictures")->with("breed")->with("user")
            ->with("city")->with("category")->first();
//        if ($post->user_id !== $user->id) abort (409);

        return response()->json($post);
    }

    public function update (Post $post, PostUpdateRequest $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        return utils::update($post, $user, $request);
    }
}
