<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Confirmacion extends Model
{
    protected $table = 'confirmaciones';

    protected $fillable = [
        'user_id',
        'actividad_id',
    ];

    protected function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }
}
