<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wall extends Model
{
    protected $guarded = false;

    public function user () {
        return $this->belongsTo(User::class);
    }
    public function pictures () {
        return $this->hasMany(Picture::class, "object_id")->where("type", "wall");
    }
}
