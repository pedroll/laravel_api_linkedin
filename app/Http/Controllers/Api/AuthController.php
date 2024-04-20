<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * AuthController handles authentication processes including user registration, login, and logout.
 */
class AuthController extends ApiController
{
    /**
     * Register a new user with the provided credentials.
     *
     * @param Request $request The request object containing user input.
     * @return JsonResponse|null A JSON response with the access token and user details on success, or an error message on failure.
     */
    public function register(Request $request): ?JsonResponse
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            Log::error('Validation error during registration', ['email' => $request->email, 'errors' => $validator->errors()]);
            return $this->sendError('Validation error', $validator->errors(), 422);
        }

        // Attempt to create a new user
        DB::beginTransaction();
        try {
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request['password']),
                'remember_token' => Str::random(10),
            ]);
            $user->save();

            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            DB::commit();

            Log::info('User registration successful', ['user_id' => $user->id]);
            return $this->sendResponse(['token' => $token, 'user' => $user], 'User registered successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error', ['email' => $request->email, 'error' => $e->getMessage()]);
            return $this->sendError('Registration error', ['error' => $e->getMessage()], 500);
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
            $result = [
                'user' => $user,
                'token' => $request->bearerToken(),
            ];
            // Return the authenticated user's details
            return $this->sendResponse($result, 'User authenticated successfully');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('OAuth Test Error: ' . $e->getMessage());

            // Return a generic error response
            return $this->sendError('An error occurred during OAuth validation', [], 500);
        }
    }

}
