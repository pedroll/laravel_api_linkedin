<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Userdata */
class UserdataResource extends JsonResource
{


    public function toArray(Request $request): array
    {
        // handle collection with pgination



        //add pagination

        return [

            'id' => $this->id,
            'nombre' => $this->nombre,
            'foto' => $this->foto,
            'edad' => $this->edad,
            'acercade' => $this->acercade,
            'genero' => $this->genero,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
