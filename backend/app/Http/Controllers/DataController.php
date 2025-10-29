<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use App\Models\Category;
use App\Models\City;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Post;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index (Request $request) {
        $data = [];

        $data["breeds"] = Breed::all();
        $data["cities"] = City::all();
        $data["categories"] = Category::all();
        $data["types"] = ServiceType::all();
        $data["event_types"] = EventType::all();
        $data["count"] = [
            "post" => Post::count(),
            "service" => Service::count(),
            "event" => Event::count(),
        ];

        return response()->json($data);
    }

    public function category (Request $request) {
        return response()->json(Category::all());
    }
}
