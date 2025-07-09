<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = false;

    public function attachments () {
        return $this->hasMany(MessagePicture::class);
    }
}
