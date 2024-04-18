<?php

namespace App\Http\Controllers;

use App\Http\Requests\userdataRequest;
use App\Http\Resources\userdataResource;
use App\Models\userdata;

class userdataController extends Controller
{
    public function index()
    {
        return userdataResource::collection(userdata::all());
    }

    public function store(userdataRequest $request)
    {
        return new userdataResource(userdata::create($request->validated()));
    }

    public function show(userdata $userdata)
    {
        return new userdataResource($userdata);
    }

    public function update(userdataRequest $request, userdata $userdata)
    {
        $userdata->update($request->validated());

        return new userdataResource($userdata);
    }

    public function destroy(userdata $userdata)
    {
        $userdata->delete();

        return response()->json();
    }
}
