<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Actividad */
class ActividadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'foto' => $this->foto,
            'descripcion' => $this->descripcion,
            'fecha' => $this->fecha,

        ];
    }
}
