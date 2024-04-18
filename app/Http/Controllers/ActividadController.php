<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActividadRequest;
use App\Http\Resources\ActividadResource;
use App\Models\Actividad;

class ActividadController extends Controller
{
    public function index()
    {
        return ActividadResource::collection(Actividad::all());
    }

    public function store(ActividadRequest $request)
    {
        return new ActividadResource(Actividad::create($request->validated()));
    }

    public function show(Actividad $actividad)
    {
        return new ActividadResource($actividad);
    }

    public function update(ActividadRequest $request, Actividad $actividad)
    {
        $actividad->update($request->validated());

        return new ActividadResource($actividad);
    }

    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return response()->json();
    }
}
