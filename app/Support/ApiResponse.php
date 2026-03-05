<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200,
        ?string $nextRoute = null,
        array $meta = []
    ): JsonResponse {

        // Preserve old structure
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        // Add next_route only if provided
        if ($nextRoute) {
            $response['next_route'] = $nextRoute;
        }

        // Add meta only if not empty
        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    public static function error(
        string $message,
        int $code = 400,
        mixed $errors = null,
        ?string $nextRoute = null
    ): JsonResponse {

        // Preserve old structure
        $response = [
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
            'code'    => $code,
        ];

        // Add next_route only if provided
        if ($nextRoute) {
            $response['next_route'] = $nextRoute;
        }

        return response()->json($response, $code);
    }
}