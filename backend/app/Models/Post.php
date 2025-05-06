<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = false;

    public function city () {
        return $this->belongsTo(City::class);
    }
    public function category () {
        return $this->belongsTo(Category::class);
    }
}
