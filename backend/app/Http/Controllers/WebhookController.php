<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\FileUpload\InputFile;
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
                    'caption'    => 'Это удобная платформа, где можно купить или продать щенков и взрослых охотничьих собак, а также найти или предложить услуги по дрессировке, натаске, содержанию и вязке. Пользователи могут размещать объявления, просматривать профили собак с фото и родословными, общаться с владельцами и специалистами, а также получать актуальную информацию о мероприятиях и выставках.',
//                    'parse_mode' => 'MarkdownV2',
                    "photo" => InputFile::create(Storage::disk("public")->path("dog.jpg")),
                    "reply_markup" => json_encode([
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "Открыть веб-приложение",
                                    "web_app" => [
                                        "url" => "https://" . env("DOMAIN") . "?s=home",
                                    ]
                                ]
                            ]
                        ]
                    ])
                ]);
            }
        }

        return 'ok';
    }
}
