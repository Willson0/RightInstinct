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
    public function user () {
        return $this->belongsTo(User::class);
    }
    public function pictures () {
        return $this->hasMany(Picture::class, "object_id")->where("type", "post");
    }
    public function breed () {
        return $this->belongsTo(Breed::class);
    }
}
