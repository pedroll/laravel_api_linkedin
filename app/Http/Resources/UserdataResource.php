<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Userdata */
class UserdataResource extends JsonResource
{


    public function toArray(Request $request): array
    {
        return [
            'nombre' => $this->nombre,
            'foto' => $this->foto,
            'edad' => $this->edad,
            'acercade' => $this->acercade,
            'genero' => $this->genero,
            'created_at' => $this->formattedCreatedAt(),
            'updated_at' => $this->formattedUpdatedAt(),
        ];
    }

    private function formattedCreatedAt(): string
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    private function formattedUpdatedAt(): string
    {
        return $this->updated_at->format('Y-m-d H:i:s');
    }
}
