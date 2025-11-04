<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\AuthChangeRequest;
use App\Http\Requests\Site\AuthRegisterRequest;
use App\Http\Requests\Site\AuthSettingsRequest;
use App\Http\Requests\Site\RecoveryCheckRequest;
use App\Http\utils;
use App\Mail\CodeMail;
use App\Mail\RecoveryMail;
use App\Models\Recovery;
use App\Models\Site\UserEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SiteAuthController extends Controller
{
    public function register (AuthRegisterRequest $request) {
        $validated = $request->validated();
        if ((User::where('email', $validated['email'])->exists())
            OR (UserEmail::where('email', $validated['email'])->exists()))
            abort(422, 'Почта уже занята');

        $user = User::create([
            "telegram_id" => 0,
            "fullname" => "",
            "notification" => true,
            "password" => bcrypt($validated["password"]),
        ]);

        $code = uniqid();
        UserEmail::create([
            "user_id" => $user->id,
            "email" => $validated['email'],
            "code" => $code,
        ]);

        Mail::to($validated['email'])->send(new CodeMail($code));
        return response()->json('ok');
    }

    public function verify (Request $request) {
        if (!$request->has('code')) abort(400);

        $userEmail = UserEmail::where('code', $request->get('code'))->first();
        if (!$userEmail) abort(422);

        $user = $userEmail->user;
        $user->email = $userEmail->email;
        $user->save();

        $userEmail->delete();
        return response()->json('ok');
    }

    public function login (Request $request) {
        $user = User::where("email", $request->email)->first();
        if (!$user or !password_verify($request->password, $user->password))
            abort (403, "Неверный логин или пароль");

        $cookie = utils::gen_cookie($user);
        $respcookie = Cookie::forever("user", $cookie);

        return response()
            ->json(["message" => "Успешная авторизация!", "cookie" => $cookie])
            ->withCookie($respcookie);
    }

    public function settings (AuthSettingsRequest $request) {
        $user = $request->get('user');
        if (!$user) abort(401);

        $validated = $request->validated();
        if ($request->has("phone")) $user->phone = $validated["phone"];
        if ($request->has("city")) $user->city_id = $validated["city"];
        if ($request->has("personal")) {
            $personal = $validated["personal"];
            $personal = array_intersect_key($personal,
                array_flip(['lastname', 'firstname', 'patronymic', 'gender']));

            $lastname = $personal["lastname"] ?? "";
            $firstname = $personal["firstname"] ?? "";
            $patronymic = $personal["patronymic"] ?? "";

            $fullname = trim($lastname . " " . $firstname . " " . $patronymic);
            if (strlen($fullname) != 0)
                $user->fullname = $fullname;
            $user->personal = $personal;
        }
        $user->save();
        return (new AuthController())->profile($request);
    }

    public function change (AuthChangeRequest $request) {
        $user = $request->get('user');
        if (!$user) abort(401);

        $validated = $request->validated();
        if ($user->email !== $validated['email']) abort(403, "Неправильная почта!");
        if (!password_verify($request->password, $user->password)) abort (403, "Неверный пароль!");

        $user->password = bcrypt($validated["newPassword"]);
        $user->save();

        return response()->json("ok");
    }

    public function recoverySend (Request $request) {
        if (!$request->has("email")) abort(400, "Нет электронной почты");

        $user = User::where("email", $request->email)->first();
        if (!$user) abort(400, "Пользователя с данной почтой не существует");
        Recovery::where("user_id", $user->id)->delete();

        $code = uniqid();
        Recovery::create([
            "user_id" => $user->id,
            "code" => $code,
        ]);

        Mail::to($user->email)->send(new RecoveryMail($code));
        return response()->json("ok");
    }

    public function recoveryCheck (RecoveryCheckRequest $request) {
        $validated = $request->validated();

        $recovery = Recovery::where("code", $validated["code"])->first();
        if (!$recovery) abort(404, "Такого кода не существует");

        $user = $recovery->user;
        $user->password = bcrypt($validated["password"]);
        $user->save();

        $recovery->delete();

        return response()->json("ok");
    }

    public function test (Request $request) {
        $respcookie = Cookie::forever("user", 1234);
        return response()
            ->json(["Message" => "Успешная авторизация!", "cookie" => 1234])
            ->withCookie($respcookie);
    }

    public function telegram (Request $request) {
        $data = $request->all();
        $hash = $data["hash"];
        unset($data["hash"]);

        ksort($data);
        $dataString = collect($data)->map(function ($value, $key) {
            return "$key=$value";
        })->implode("\n");

        $secretKey = hash('sha256', env("TELEGRAM_BOT_TOKEN"), true);
        $calcHash = hash_hmac('sha256', $dataString, $secretKey);
        if (!hash_equals($calcHash, $hash))
            return response()->json(["message" => "Недействительные данные"], 403);

        $user = User::where("telegram_id", $data["id"])->first();
        if (!$user) {
            $user = User::create([
                "telegram_id" => $data["id"],
                "fullname" => $data["first_name"] . " " . $data["last_name"],
                "username" => $data["username"] ?? null,
                "notification" => true,
                "rating" => 0,
                "avatar" => $data["photo_url"],
            ]);
        }

        $cookie = utils::gen_cookie($user);
        $respcookie = Cookie::forever("user", $cookie);

        return response()->json(["message" => "Успешная авторизация", "cookie" => $cookie])->withCookie($respcookie);
    }
}
