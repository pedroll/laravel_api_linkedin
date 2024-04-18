<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class confirmacion extends Model
{
    protected $table = 'confirmaciones';

    protected function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }
}
