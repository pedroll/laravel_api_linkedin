<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'nombre',
        'foto',
        'descripcion',
        'fecha',
    ];

//    protected function user(): BelongsTo
//    {
//        return $this->belongsTo(User::class);
//    }
}
