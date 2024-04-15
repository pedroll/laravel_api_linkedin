<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    /**
     * funcion auth register
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): ?JsonResponse
    {
        // validacion
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Crear usuario
        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request['password']);
            $user->remember_token = Str::random(10);
            $user->save();
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            DB::commit();

            return response()->json([
                'token' => $token,
                'user' => $user], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Login api En lugar de esta funcion utilizar la ruta de oauth que tampoco es recomendable (Legacy))
     * {{base_url}}/oauth/token
     * ?username=me287@me.com
     * &password=pedro12345
     * &grant_type=password
     * &client_id=2
     * &client_secret=7FLaTrL88SvSpMz3Va4Jw0N7SyaYSW9b1mNyozUb
     * &scope=
     *
     * @param Request $request
     * @return JsonResponse
     */
    /*public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token];
                return response($response, 200);
            } else {
                $response = ['message' => 'Password mismatch'];
                return response($response, 422);
            }
        } else {
            $response = ['message' =>'User does not exist'];
            return response($response, 422);
        }
    }*/

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            Auth::logout();
            // Revocar token actual
            $token = $request->user()->token();
            if ($token) {
                $token->revoke();
            }

            // Opcional, borrar todos los tokens para mejor seguridad
            $request->user()->tokens->each(function ($token, $key) {
                $token->revoke(); // o    $token->delete();depende de version laravel
            });

            // opcional borrar sesion si se es utilizada
            if ($request->session()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            DB::commit();
            Log::info('User logged out', ['user_id' => $request->user()->id]);
            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('An error occurred during logout', ['user_id' => $request->user()->id]);

            return response()->json(['error' => 'An error occurred during logout'], 500);
        }
    }

    public function testOauth()
    {
        //todo
        return Http::get('http://localhost:8000/api/auth/user');
    }

}
