<?php

namespace App\Http\Controllers;

use App\Http\utils;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request) {
        return utils::index(User::class, $request, true);
    }
}
