<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\confirmacion */
class ConfirmacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'actividad_id' => $this->actividad_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'id' => $this->id,

//            'actividad' => new ActividadResource($this->whenLoaded('actividad')),
//            'user' => new ActividadResource($this->whenLoaded('user')),
        ];
    }
}
