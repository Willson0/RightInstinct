<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = false;

    public function city () {
        return $this->belongsTo(City::class);
    }
    public function posts () {
        return $this->hasMany(Post::class);
    }
    public function services() {
        return $this->hasMany(Service::class);
    }
    public function events () {
        return $this->hasMany(Event::class);
    }
    public function notifications () {
        return $this->hasMany(Notification::class);
    }
}
