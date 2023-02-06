<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $table = 'fcm_tokens';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
