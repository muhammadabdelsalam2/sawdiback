<?php

namespace App\Services;

use App\Http\Resources\ContentResource;
use App\Models\Content;
use App\Repositories\ContentRepository;
use App\Support\ServiceResult;

class ContentService
{
    public function __construct(
        private readonly ContentRepository $contentRepository
    ) {
    }

    public function paginate(int $perPage = 15): array
    {
        $content = $this->contentRepository->paginate($perPage);

        return ServiceResult::success(
            data: ContentResource::collection($content),
            message: 'Content fetched successfully.',
            code: 200
        );
    }

    public function findById(int $id): array
    {
        $content = $this->contentRepository->findById($id);

        if (!$content) {
            return ServiceResult::error(
                message: 'Content not found.',
                errors: [],
                code: 404
            );
        }

        return ServiceResult::success(
            data: new ContentResource($content),
            message: 'Content fetched successfully.',
            code: 200
        );
    }

    public function create(array $data): array
    {
        $content = $this->contentRepository->create($data);

        return ServiceResult::success(
            data: new ContentResource($content),
            message: 'Content created successfully.',
            code: 201
        );
    }

    public function update(Content $content, array $data): array
    {
        $content = $this->contentRepository->update($content, $data);

        return ServiceResult::success(
            data: new ContentResource($content),
            message: 'Content updated successfully.',
            code: 200
        );
    }

    public function delete(Content $content): array
    {
        $this->contentRepository->delete($content);

        return ServiceResult::success(
            data: null,
            message: 'Content deleted successfully.',
            code: 200
        );
    }
}
