<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = false;
    protected $fillable = ["user_id", "title", "description", "city_id", "start_date", "end_date", "type_id",
        "details", "rating", "moderated"];

    public function city () {
        return $this->belongsTo(City::class);
    }
    public function category () {
        return $this->belongsTo(ServiceType::class, "type_id", "id");
    }
    public function user () {
        return $this->belongsTo(User::class);
    }
    public function pictures () {
        return $this->hasMany(Picture::class, "object_id")->where("type", "event");
    }
}
