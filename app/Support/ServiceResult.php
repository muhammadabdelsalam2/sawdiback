<?php

namespace App\Support;

class ServiceResult
{
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200,
        ?string $nextEndpoint = null,
        array $meta = []
    ): array {

        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'code'    => $code,
        ];

        // Add nextEndpoint only if provided
        if ($nextEndpoint !== null) {
            $response['nextEndpoint'] = $nextEndpoint;
        }

        // Add meta only if not empty
        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return $response;
    }

    public static function error(
        string $message,
        ?string $nextEndpoint = null,
        mixed $errors = null,
        int $code = 400
    ): array {

        return [
            'success'      => false,
            'message'      => $message,
            'nextEndpoint' => $nextEndpoint,
            'errors'       => $errors,
            'code'         => $code,
        ];
    }
    
}