<?php

namespace App\Services;

use App\Http\Resources\ArticleResource;
use App\Repositories\ArticleRepository;
use App\Support\ServiceResult;

class ArticleService
{
    public function __construct(
        private readonly ArticleRepository $articleRepository
    ) {
    }

    public function paginate(int $perPage = 15): array
    {
        $articles = $this->articleRepository->paginate($perPage);

        return ServiceResult::success(
            data: ArticleResource::collection($articles),
            message: 'Articles fetched successfully.',
            code: 200
        );
    }

    public function findById(int $id): array
    {
        $article = $this->articleRepository->findById($id);

        if (!$article) {
            return ServiceResult::error(
                message: 'Article not found.',
                errors: [],
                code: 404
            );
        }

        return ServiceResult::success(
            data: new ArticleResource($article),
            message: 'Article fetched successfully.',
            code: 200
        );
    }
}
