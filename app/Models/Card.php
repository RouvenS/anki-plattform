<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'user_id',
        'front',
        'back',
        'tts',
        'audio_path',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
