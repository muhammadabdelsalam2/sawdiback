<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Ecommerce\ReviewSubmitRequest;
use App\Services\API\Ecommerce\Review\ReviewService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {
    }

    public function open(Request $request, string $locale, int $order): JsonResponse
    {
        $result = $this->reviewService->openReview($request->user(), $order);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code']);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function submit(ReviewSubmitRequest $request, string $locale, int $order): JsonResponse
    {
        $payload = $request->validated();
        $imagePaths = [];
        $imageUrls = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('reviews', 'public');
            }
        }

        $payload['images'] = !empty($imagePaths) ? $imagePaths : null;

        $result = $this->reviewService->submitReview($request->user(), $order, $payload);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['errors'] ?? []);
        }

        if (!empty($imagePaths)) {
            foreach ($imagePaths as $path) {
                $imageUrls[] = Storage::disk('public')->url($path);
            }

            if (is_array($result['data'])) {
                $result['data']['image_urls'] = $imageUrls;
            }
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }
}
