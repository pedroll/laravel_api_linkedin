<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $routePath = 'user';
    protected $viewPath = 'user';

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // Retrieve query parameters for filtering and sorting
        $filterBy = $request->query('filterBy');
        $sortBy = $request->query('sortBy', 'created_at');
        $sortOrder = $request->query('sortOrder', 'desc');

        // Query building with optional filtering and sorting
        $query = User::query();

        if ($filterBy) {
            $query->where('name', 'like', "%{$filterBy}%"); // Example filtering by name
        }

        $users = $query->orderBy($sortBy, $sortOrder)->paginate(10);

        // Using UserResource to transform the collection
        //return UserResource::collection($users);
        return response()->json($users, 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request)
    {
        $item = new User;
        $item->fill($request->validated());
        $item->save();
        return response()->json(compact('item'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $item = User::query()->findOrFail($id);
        return response()->json(compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function update($id, UpdateUserRequest $request)
    {
        $item = User::query()->findOrFail($id);
        $item->update($request->validated());
        return response()->json(compact('item'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return response()->json('Error', 400);
    }
}
