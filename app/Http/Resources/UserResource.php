<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        //    return $this->resource->toArray();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // Conditional attribute example
            //'is_admin' => $this->when($this->isAdmin(), true, false),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'userdata' => UserdataResource::collection($this->whenLoaded('userdata')),
            'actividades' => ActividadResource::collection($this->whenLoaded('actividades')),
        ];
    }
}
