<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustedDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_token',
        'user_agent',
        'ip',
        'expires_at',
    ];

    protected $hidden = [
        'device_token',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
