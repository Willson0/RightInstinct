<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use App\Models\Category;
use App\Models\City;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index (Request $request) {
        $data = [];

        $data["breeds"] = Breed::all();
        $data["cities"] = City::all();
        $data["categories"] = Category::all();

        return response()->json($data);
    }

    public function category (Request $request) {
        return response()->json(Category::all());
    }
}
