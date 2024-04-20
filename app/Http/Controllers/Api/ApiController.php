<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * ApiController class to standardize JSON responses for API requests.
 */
class ApiController extends Controller
{
    /**
     * Send a standard JSON response for successful requests.
     *
     * @param array $result The payload to be returned, typically an array of data.
     * @param string $message A success message to accompany the response.
     * @param int $code HTTP status code, defaults to 200 if not specified.
     * @return JsonResponse Returns a JsonResponse object with success status.
     */
    public function sendResponse($result, $message, $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];
        // todo manejar Logging
        return response()->json($response, $code);
    }

    /**
     * Send a standard JSON response for failed requests.
     *
     * @param string $error A message describing the error.
     * @param array $errorMessages Additional error messages or details, defaults to an empty array.
     * @param int $code HTTP status code, defaults to 404 if not specified.
     * @return JsonResponse Returns a JsonResponse object with error status.
     */
    public function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        // todo manejar Logging
        return response()->json($response, $code);
    }
}
