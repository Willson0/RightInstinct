<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $guarded = false;

    public function user_subscription () {
        return $this->belongsTo(User::class, "user_subscription_id");
    }
}
