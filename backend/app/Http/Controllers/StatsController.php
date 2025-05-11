<?php

namespace App\Http\Controllers;

use App\Models\Analytic;
use App\Models\AnalyticUser;
use App\Models\Event;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use App\Models\UserService;
use App\Models\UserWebinar;
use App\Models\VentureDeal;
use App\Models\Webinar;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index() {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $accountarr = [];

        for ($month = 1; $month <= 12; $month++) {
            $startMonth = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endMonth = Carbon::create($currentYear, $month, 1)->endOfMonth();

            $count = User::whereBetween("created_at", [$startMonth, $endMonth])->count();
            $accountarr[] = $count;
        }
        $startMonth = Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
        $money = Post::where("created_at", ">=", Carbon::now()->subDays(30))->count();
        $money30d = Service::where("created_at", ">=", Carbon::now()->subDays(30))->count();
        $usersperday = Event::where("created_at", ">=", Carbon::now()->subDays(30))->count();
        $logsperday = Event::where("moderated", 0)->count();

        return response()->json(["accounts" => $accountarr,
            "money" => $money, "money30" => $money30d, "usersPerDay" => $usersperday,
            "logsPerDay" => $logsperday],
            200);
    }
}
