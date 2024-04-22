<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserdataRequest;
use App\Http\Resources\UserdataResource;
use App\Models\User;
use App\Models\Userdata;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserdataController extends ApiController
{

    public function index(Request $request): JsonResponse
    {
        try {
            //$users = User::all();
            // query users and aply filters and sort with db::table
            $users = DB::table('users')
                ->join('userdatas', 'users.id', '=', 'userdatas.user_id')
                ->select("user_id", "nombre", 'foto', "edad", "genero");

//            $this->applyFilters($users, $request);
//            $this->applySorts($users, $request);

            $users = $users->get();

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

    public function show($id, Request $request): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            // get Userdata
            $userdata = Userdata::where("user_id", "=", $id)->first();
            $userdata = new UserdataResource($userdata);
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
            Log::error('User not found: ' . $id . '. ' . $e->getMessage());
            return $this->sendError('User not found:' . $id, $e->getMessage(), 404);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage(), ['id' => $id]);
            return $this->sendError('An error occurred', $e->getMessage(), 500);
        }
    }

    /**
     * @param UserdataRequest $request
     * @return JsonResponse
     */
    public function store(UserdataRequest $request): JsonResponse
    {
        // validate userdataRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $user = User::create($validated);

            $userdata = $validated + ['user_id' => $user->id, 'nombre' => $user->name];
            $userdata = Userdata::create($userdata);

            $token = $user->createToken('auth_token')->accessToken;

            $result = [
                'token' => $token,
                'user' => $user,
                'userdata' => $userdata,
            ];
            $message = 'User creado correctamente';
            //end transaction
            DB::commit();
            Log::info($message, ['user_id' => $user->id]);

// return redirect response
            //return redirect()->route('some.route')->with('success', 'User created successfully');
            return $this->sendResponse($result, $message);

            //return redirect()->route('form_view', ['id' => $id]);
        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollBack();
            Log::error('User creation failed: ' . $e->getMessage(), ['username' => $request->email]);
            return $this->sendError('User creation failed: ', $e->getMessage(), 500);
        }


    }

    public function create(): JsonResponse
    {
        $result = [
            'message' => 'Render form to store user',
        ];
        return $this->sendResponse($result, 'Render form to store user');
    }

    public function update(UserdataRequest $request, $id = null): JsonResponse
    {

        try {
            $validatedData = $request->validated();

            DB::BeginTransaction();

            // find user by id
            $user = User::findOrFail($id);
            // find userdata by user_id
            $userdata = Userdata::where('user_id', $user->id)->firstOrFail();

            // only uodate desired fields name , nombre, edad genero acercade. email and password no modificables
            $user->update([
                'name' => $validatedData['name'],

            ]);
            $userdata->update([
                'nombre' => $validatedData['name'],
                'edad' => $validatedData['edad'],
                'genero' => $validatedData['genero'],
                'acercade' => $validatedData['acercade']
            ]);

            $userdataResource = new UserdataResource($userdata);

            $result = [
                'userdata' => $userdataResource,
                'user' => $user,
            ];
            $message = 'User actualizado correctamente';
            //end transaction
            DB::commit();
            Log::info($message, ['user_id' => $user->id]);
            return $this->sendResponse($result, $message);
        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollBack();
            $message = 'User update failed: ';//end transaction

            Log::error($message . $e->getMessage(), ['email' => $validatedData['email']]);
            return $this->sendError('User update failed: ', $e->getMessage(), 500);
        }

    }

    public function destroy(int $id)
    {

        $user = User::findOrFail($id);
        $email = $user['email'];
        $userdata = Userdata::where("user_id", "=", $id)->firstOrFail();
        try {
            DB::beginTransaction();

            $userdata->delete();
            $user->delete();

            DB::commit();

            Log::info('User deleted: ' . $id . ' ' . $email);
            return $this->sendResponse([], 'Usuario borrado: ' . $id);
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $email . '. ' . $e->getMessage());
            return $this->sendError('User not found: ' . $id, $e->getMessage(), 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An error occurred: ' . $e->getMessage(), ['email' => $email]);
            return $this->sendError('An error occurred', $e->getMessage(), 500);
        }

    }

    // function create, renders form to store user

    public function edit($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $userdata = Userdata::where("user_id", "=", $id)->first();

            $result = [
                'user' => $user,
                'userdata' => $userdata,
            ];
            //redirect to form view, who renders form to edit user

            //return view('form_view', $result);

            return $this->sendResponse($result, 'Userdata edit retrieved successfully');
        } catch (ModelNotFoundException $e) {
            Log::error('User not found: ' . $id . '. ' . $e->getMessage());
            return $this->sendError('User not found: ' . $id, $e->getMessage(), 404);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage(), ['id' => $id]);
            return $this->sendError('An error occurred', $e->getMessage(), 500);
        }
    }

    //edit function

}
