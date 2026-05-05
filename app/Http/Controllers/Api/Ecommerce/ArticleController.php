<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Services\ArticleService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService
    ) {
    }

    public function index(Request $request, string $locale): JsonResponse
    {
        // ضبط الـ locale يدوياً
        if (in_array($locale, ['en', 'ar'])) {
            app()->setLocale($locale);
        }

        $request->validate([
            'per_page' => 'nullable|integer|min:1',
            'perPage'  => 'nullable|integer|min:1',
        ]);

        $perPage = (int) $request->input('per_page', $request->input('perPage', 15));
        $result  = $this->articleService->paginate($perPage);

        return ApiResponse::success(
            data: $result['data'],
            message: $result['message'],
            code: $result['code']
        );
    }

    public function show(string $locale, int $id): JsonResponse
    {
        // ضبط الـ locale يدوياً
        if (in_array($locale, ['en', 'ar'])) {
            app()->setLocale($locale);
        }

        $result = $this->articleService->findById($id);

        if (!$result['success']) {
            return ApiResponse::error(
                message: $result['message'],
                code: $result['code']
            );
        }

        return ApiResponse::success(
            data: $result['data'],
            message: $result['message'],
            code: $result['code']
        );
    }
}
