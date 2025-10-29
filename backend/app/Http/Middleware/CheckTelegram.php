<?php

namespace App\Http\Middleware;

use App\Http\utils;
use App\Models\Site\UserCookie;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckTelegram
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $init = $request->initData;

        if ($init) {
            $token = env('TELEGRAM_BOT_TOKEN');
            if (!utils::isSafe($token, $init)) abort (401);

            $decodedData = urldecode($init); // Декодируем строку из URL
            parse_str($decodedData, $data);
            $data["user"] = json_decode($data["user"], true);

            $request->merge ([
                'initData' => $data // Преобразуем в массив, если данные в JSON-формате
            ]);
        } else {
            $cookieparam = Cookie::get("user");
            if ($cookieparam) {
                $cookie = UserCookie::where("cookie", $cookieparam)->first();
                if ($cookie) {
                    $user = $cookie->user;
                    if ($user)
                        $request->merge ([
                            'user' => $user
                        ]);
                }
            }
        }
        return $next($request);
    }
}
