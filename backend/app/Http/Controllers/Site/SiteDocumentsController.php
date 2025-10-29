<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteDocumentsController extends Controller
{
    public function store (Request $request) {
        $user = $request->get('user');
        if (!$user) abort(401);
        if (!$request->hasFile('document')) abort(400, "Не приложен документ");

        $documents = json_decode($user->documents, 1);
        if ($documents == null) $documents = [];

        $file = $request->file('document');

        $time = time();
        $url = "documents/{$user->id}_$time." . $file->extension();
        Storage::disk("public")->putFileAs("documents", $file, "{$user->id}_$time." . $file->extension());

        $documents[$url] = $file->getClientOriginalName();
        $user->documents = $documents;
        $user->save();

        return response()->json(["url" => $url]);
    }

    public function change (Request $request) {
        $user = $request->get('user');
        if (!$user) abort(401);
        if (!$request->has("documents")) abort(400, "Не приложен список");

        $user->documents = $request->documents;
        $user->save();

        return response()->json(["ok"]);
    }
}
