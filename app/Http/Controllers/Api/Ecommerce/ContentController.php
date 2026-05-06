<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Content\StoreContentRequest;
use App\Http\Requests\Api\Content\UpdateContentRequest;
use App\Models\Content;
use App\Services\ContentService;
use App\Support\ApiResponse;
use App\Support\LocaleResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function __construct(
        private readonly ContentService $contentService
    ) {
    }

    public function index(Request $request, string $locale): JsonResponse
    {
        LocaleResolver::apply($locale);

        $request->validate([
            'per_page' => 'nullable|integer|min:1',
            'perPage' => 'nullable|integer|min:1',
        ]);

        $perPage = (int) $request->input('per_page', $request->input('perPage', 15));
        $result = $this->contentService->paginate($perPage);

        return ApiResponse::success(
            data: $result['data'],
            message: $result['message'],
            code: $result['code']
        );
    }

    public function show(string $locale, Content $content): JsonResponse
    {
        LocaleResolver::apply($locale);

        $result = $this->contentService->findById($content->id);

        return ApiResponse::success(
            data: $result['data'],
            message: $result['message'],
            code: $result['code']
        );
    }

    public function store(StoreContentRequest $request, string $locale): JsonResponse
    {
        LocaleResolver::apply($locale);

        $result = $this->contentService->create($this->normalizedPayload($request));

        return ApiResponse::success(
            data: $result['data'],
            message: $result['message'],
            code: $result['code']
        );
    }

    public function update(UpdateContentRequest $request, string $locale, Content $content): JsonResponse
    {
        LocaleResolver::apply($locale);

        $result = $this->contentService->update($content, $this->normalizedPayload($request, $content));

        return ApiResponse::success(
            data: $result['data'],
            message: $result['message'],
            code: $result['code']
        );
    }

    public function destroy(string $locale, Content $content): JsonResponse
    {
        LocaleResolver::apply($locale);

        $result = $this->contentService->delete($content);

        return ApiResponse::success(
            data: $result['data'],
            message: $result['message'],
            code: $result['code']
        );
    }

    private function normalizedPayload(Request $request, ?Content $content = null): array
    {
        $title = $request->input('title', []);
        $description = $request->input('description', []);

        return [
            'title' => [
                'ar' => $request->input('title_ar', $title['ar'] ?? $content?->title['ar'] ?? $content?->title['en'] ?? null),
                'en' => $request->input('title_en', $title['en'] ?? $content?->title['en'] ?? $content?->title['ar'] ?? null),
            ],
            'description' => [
                'ar' => $request->input('description_ar', $description['ar'] ?? $content?->description['ar'] ?? $content?->description['en'] ?? null),
                'en' => $request->input('description_en', $description['en'] ?? $content?->description['en'] ?? $content?->description['ar'] ?? null),
            ],
            'video' => $request->input('video'),
        ];
    }
}
