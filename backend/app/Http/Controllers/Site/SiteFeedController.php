<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Post;
use App\Models\Service;
use App\Models\Wall;
use Illuminate\Http\Request;

class SiteFeedController extends Controller
{
    public function index (Request $request) {
        $feed = [];
        $feed["posts"] = Post::limit(10)->orderByDesc("id")->with("pictures")->with("breed")->with("user")->with("city")->with("category")->get();
        $feed["services"] = Service::limit(10)->orderByDesc("id")->with("pictures")->with("user")->with("city")->with("category")->get();
        $feed["walls"] = Wall::limit(5)->orderByDesc("id")->with("pictures")->with("user")->get();

        $postsPopular = Post::orderByDesc("rating")->limit(15)->with(["pictures", "breed", "user", "city", "category"])->get();
        $servicesPopular = Service::orderByDesc("rating")->limit(15)->with(["pictures", "user", "city", "category"])->get();

        $items = $postsPopular
            ->concat($servicesPopular)
            ->sortByDesc('rating')
            ->take(10)
            ->values();
        $feed["popular"] = $items;
        $feed["events"] = Event::limit(10)->orderByDesc("id")->with("pictures")->with("user")->with("city")->with("category")->where("moderated", "1")->get();

        return response()->json($feed);
    }
}
