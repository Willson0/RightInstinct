<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebhookController extends Controller
{
    public function tg (Request $request) {
        $update = Telegram::getWebhookUpdate();

        if (isset($update['message'])) {
            $message = $update['message'];

            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';

            if (trim($text) === '/start') {
                Telegram::sendPhoto([
                    'chat_id' => $chatId,
                    'text'    => 'Hello!',
                ]);
            }
        }

        return 'ok';
    }
}
