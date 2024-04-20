<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Userdata extends Model
{
    protected $fillable = [
        'nombre',
        'foto',
        'edad',
        'acercade',
        'genero',
        'user_id'
    ];

    protected function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
