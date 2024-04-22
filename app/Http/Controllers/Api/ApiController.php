<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function applyFilters(Builder $query, Request $request, array $allowedFields = []): void
    {
        if ($request->has('filters')) {
            $filters = json_decode($request->filters, true);
            foreach ($filters as $field => $value) {
                if (in_array($field, $allowedFields)) { // Check if the field is allowed
                    $query->where($field, 'like', "%{$value}%");
                }
            }
        }
    }

    public function applySorts(Builder $query, Request $request, array $allowedSortFields): void
    {
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
}
