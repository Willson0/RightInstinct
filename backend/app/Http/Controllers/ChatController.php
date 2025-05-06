<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\ChatSendRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function show (User $companion, Request $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        $dialog = Message::where(function($q) use($user, $companion) {
            $q->where("sender_id", $user->id)
                ->where("recipient_id", $companion->id);
        })->orWhere(function($q) use($user, $companion) {
            $q->where("sender_id", $companion->id)
                ->where("recipient_id", $user->id);
        })->get();

        Message::where("sender_id", $companion->id)
            ->where("recipient_id", $user->id)->update(["readed" => true]);

        return response()->json([
            "companion" => $companion,
            "dialog" => $dialog
        ]);
    }

    public function send (User $companion, ChatSendRequest $request) {
        $user = User::where("telegram_id", $request["initData"]["user"]["id"])->firstOrFail();
        Message::create([
            "sender_id" => $user->id,
            "recipient_id" => $companion->id,
            "message" => $request["message"],
            "url" => "",
            "readed" => false,
        ]);

        return $this->show($companion, $request);
    }
}
