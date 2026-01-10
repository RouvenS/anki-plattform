<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'status',
        'error_message',
        'input_vocabulary',
        'prompt_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prompt()
    {
        return $this->belongsTo(Prompt::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
}