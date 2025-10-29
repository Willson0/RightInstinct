<?php

namespace App\Http\Controllers;

use App\Http\Requests\Complain\ComplainStoreRequest;
use App\Models\Complain;
use App\Models\Event;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class ComplainController extends Controller
{
    public function index() {
        $complains = Complain::all();
        foreach ($complains as $complain) {
            $tables = [
                "post" => Post::class,
                "service" => Service::class,
                "event" => Event::class,
                "user" => User::class,
            ];
            $complain->object = $tables[$complain->type]::find($complain->object_id);
            $complain->user;
        }

        return response()->json($complains);
    }

    public function store (ComplainStoreRequest $request) {
        $user = $request->get('user');
        if (!$user) $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();

        $data = $request->validated();
        $data["user_id"] = $user->id;

        $complain = Complain::create($data);

        return $complain;
    }

    public function destroy (Complain $complain) {
        $complain->delete();
        return response()->json(["ok" => "true"]);
    }
}
