<?php

namespace App\Http\Controllers;

use App\Http\Requests\MailingRequest;
use App\Http\utils;
use App\Models\Admin;
use App\Models\Event;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function profile (Request $request) {
        return $request->get("user");
    }

    public function login (Request $request) {
        $admin = Admin::where("login", $request->login)->first();
        if (!$admin or !password_verify($request->password, $admin->password))
            abort (403, "Неверный логин или пароль");

        $cookie = utils::gen_cookie($admin, isadmin: true);
        $respcookie = Cookie::forever("admin", $cookie);

        return response()
            ->json(["Message" => "Успешная авторизация!", "cookie" => $cookie])
            ->withCookie($respcookie);
    }

    public function posts (Request $request) {
        return utils::index(Post::class, $request, true);
    }
    public function services (Request $request) {
        return utils::index(Service::class, $request, true);
    }
    public function events (Request $request) {
        return utils::index(Event::class, $request, true);
    }
}
