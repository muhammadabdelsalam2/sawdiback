<?php

namespace App\DTOs\ٍSetting;

class SupportItemDTO
{
    public function __construct(
        public string $title,
        public ?string $subtitle,
        public ?string $icon,
        public string $type,
        public ?string $value,
    ) {
    }

    public static function fromModel($item): self
    {
        return new self(
            $item->title,
            $item->subtitle,
            $item->icon,
            $item->type,
            $item->value
        );
    }
}