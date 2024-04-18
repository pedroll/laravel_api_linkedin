<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'nombre',
        'foto',
        'edad',
        'acercade',
        'genero',
    ];

    protected function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
