<?php

namespace App\DTOs\Farmer;

use App\Http\Requests\Farmer\StoreFarmerRequest;

class FarmerDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?float $opening_balance = null,
        public ?bool $is_active = null,
    ) {
    }

    public function formRequest(StoreFarmerRequest $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            opening_balance: $data['opening_balance'] ?? null,
            is_active: $data['is_active'] ?? null,
        );
    }
}