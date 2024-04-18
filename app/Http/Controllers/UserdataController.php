<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\userdataRequest;
use App\Http\Resources\UserdataResource;
use App\Models\User;
use App\Models\Userdata;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserdataController extends ApiController
{
    /*public function index()
    {
        return userdataResource::collection(userdata::all());
    }*/
    public function index(Request $request)
    {


        // Query building with optional filtering and sorting
        $query = Userdata::query();

        // Dynamic Filtering
        $this->applyFilters($query, $request);


        // Dynamic Sorting
        $this->applySorts($query, $request);


        // Enhanced Pagination
        $perPage = $request->query('perPage', 10);
        $userDatas = $query->paginate($perPage);

        // Using UserResource to transform the collection
        return UserdataResource::collection($userDatas);
        //return response()->json($userDatas, 200);

    }

    private function applyFilters(Builder $query, Request $request): void
    {
        $allowedFields = ['nombre', 'edad', 'genero']; // Define allowed fields for filtering
        if ($request->has('filters')) {
            $filters = json_decode($request->filters, true);
            foreach ($filters as $field => $value) {
                if (in_array($field, $allowedFields)) { // Check if the field is allowed
                    $query->where($field, 'like', "%{$value}%");
                }
            }
        }
    }

    private function applySorts(Builder $query, Request $request): void
    {
        $allowedSortFields = ['nombre', 'created_at']; // Define allowed fields for sorting
        if ($request->has('sorts')) {
            $sorts = json_decode($request->sorts, true);
            foreach ($sorts as $field => $direction) {
                if (in_array($field, $allowedSortFields) && in_array($direction, ['asc', 'desc'])) { // Validate field and direction
                    $query->orderBy($field, $direction);
                }
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    public function getUserdatas(Request $request): ?JsonResponse
    {
        try {
            //$users = User::all();
            $users = DB::table('users')
                ->join('userdatas', 'users.id', '=', 'userdatas.user_id')
                ->select("user_id", "nombre", 'foto', "edad", "genero")
                ->get();
            $result = [
                'users' => $users
            ];
            $message = "Userdatas recuperados correctamente";

            return $this->sendResponse($result, $message);
        } catch (ModelNotFoundException $e) {
            Log::error('Users not found: ' . $e->getMessage());
            return $this->sendError('Users not found', $e->getMessage(), 404);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage(), ['username' => $request->email]);
            return $this->sendError('An error occurred', $e->getMessage(), 500);
        }
    }

    public function getUserdataDetail($id, Request $request): ?JsonResponse
    {
        try {
            $user = User::findOrFail($id);
           // get Userdata
            $userdata = Userdata::where("user_id", "=", $id)->first();
            $userdata =new UserdataResource($userdata);
            // otro aproach
//            $user = User::findOrFail($id);
//            $userdata2 = new UserdataResource($user->userdata);

            $result = [
                'user' => $user,
                'userdata' => $userdata,
            ];
            $message = 'Userdatas recuperados correctamente';

            return $this->sendResponse($result, $message);
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: '. $id.'. '. $e->getMessage());
            return $this->sendError('User not found:'. $id, $e->getMessage(), 404);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage(), ['id' => $id]);
            return $this->sendError('An error occurred', $e->getMessage(), 500);
        }
    }

    public function store(userdataRequest $request)
    {
        return new UserdataResource(Userdata::create($request->validated()));
    }

    public function show(Userdata $userdata)
    {
        return new UserdataResource($userdata);
    }

    public function update(userdataRequest $request, Userdata $userdata)
    {
        $userdata->update($request->validated());

        return new UserdataResource($userdata);
    }

    public function destroy(Userdata $userdata)
    {
        $userdata->delete();

        return response()->json();
    }
}
