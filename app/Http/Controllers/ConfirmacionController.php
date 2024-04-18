<?php

namespace App\Http\Controllers;

use App\Http\Requests\confirmacionRequest;
use App\Http\Resources\confirmacionResource;
use App\Models\confirmacion;

class confirmacionController extends Controller
{
    public function index()
    {
        return confirmacionResource::collection(confirmacion::all());
    }

    public function store(confirmacionRequest $request)
    {
        return new confirmacionResource(confirmacion::create($request->validated()));
    }

    public function show(confirmacion $confirmacion)
    {
        return new confirmacionResource($confirmacion);
    }

    public function update(confirmacionRequest $request, confirmacion $confirmacion)
    {
        $confirmacion->update($request->validated());

        return new confirmacionResource($confirmacion);
    }

    public function destroy(confirmacion $confirmacion)
    {
        $confirmacion->delete();

        return response()->json();
    }
}
