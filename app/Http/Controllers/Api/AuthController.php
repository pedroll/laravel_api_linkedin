<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            Log::error('An error occurred during login: ' . $validator->errors(), ['username' => $request->email]);
            return $this->sendError('An error occurred during validation', $validator->errors(), 422);
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

            Log::info('User logged in', ['user_id' => $request->user()->id]);
            return $this->sendResponse([$token, $user], 'Successfully logged in');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('An error occurred during login: ' . $e->getMessage(), ['username' => $request->email]);
            return $this->sendError('An error occurred during login', $e->getMessage(), 500);


        }
    }

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
            return $this->sendResponse([], 'Successfully logged out');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            Log::error('An error occurred during logout: ' . $e->getMessage(), ['user_id' => $request->user()->id]);
            // Return a generic error response
            return $this->sendError('An error occurred during logout', [], 500);
        }
    }

    /**
     * Test OAuth token validation and return user information.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function testOauth(Request $request): JsonResponse
    {
        try {
            // Assuming the user is authenticated via OAuth token
            $user = Auth::user();

            // Check if the user object is not null
            if (!$user) {
                return $this->sendError('Unauthorized', [], 401); // Use sendError from BaseController
            }

            // Return the authenticated user's details
            return $this->sendResponse($user, 'User authenticated successfully');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('OAuth Test Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError('An error occurred during OAuth validation', [], 500);
        }
    }

}
