<?php

namespace App\Models\Site;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserCookie extends Model
{
    protected $guarded = false;

    public function user () {
        return $this->belongsTo(User::class);
    }
}
