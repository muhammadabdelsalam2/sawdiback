<?php

namespace App\DTOs\Api;

class CategoryDTO
{
    public function __construct(
        public ?int $id = null,
        public string $name,
        public ?string $slug = null,
        public ?string $image = null,
        public ?string $description = null,
        public bool $is_active = true,
    ) {
    }
}