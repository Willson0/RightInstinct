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
                    'caption'    => '🐾 Добро пожаловать в наш уютный уголок для любителей собак! Здесь каждый сможет найти всё для себя и своего любимца. Откройте для себя мир новых возможностей:

— Покупка и продажа собак разных пород
**— Выбор лучшего корма, лакомств и витаминов**
— Большой ассортимент аксессуаров и игрушек
**— Прямое общение с продавцами и другими владельцами**
— Планирование интересных мероприятий и встреч
**— Делитесь опытом, советами и милыми фото своих хвостатых друзей**
— Удобный и быстрый поиск нужных товаров или услуг

Погрузитесь в атмосферу заботы и весёлого общения, делайте жизнь своих питомцев ещё ярче и счастливее вместе с нами! ✨🐶',
                    'parse_mode' => 'MarkdownV2',
                    "photo" => "https://" . env("DOMAIN") . "/storage/dog.jpg"
                ]);
            }
        }

        return 'ok';
    }
}
