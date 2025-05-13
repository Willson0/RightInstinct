<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = false;
    protected $fillable = ["user_id", "title", "city_id", "price", "type_id", "description", "rating", "link"];

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
        return $this->hasMany(Picture::class, "object_id")->where("type", "service");
    }
}
