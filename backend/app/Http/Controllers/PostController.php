<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\PostStoreRequest;
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

        $validated["user_id"] = $user->id;
        $validated["rating"] = 0;

        $pictures = $validated["pictures"];
        unset($validated["pictures"]);

        $post = Post::create($validated);

        foreach ($pictures as $picture) {
            $time = time();
            $url = "post/image_$time." . $picture->extension();
            Storage::disk("public")->putFileAs("post", $picture, "image_$time." . $picture->extension());

            Picture::create([
                "type" => "post",
                "object_id" => $post->id,
                "url" => $url,
            ]);
        }

        return response()->json($post);
    }
}
