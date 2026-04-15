<?php

namespace App\DTOs\Account;

use App\Http\Requests\Api\Account\UpdateAccountRequest;

class UpdateAccountDTO
{
    public function __construct(
        public string $name,
        public string $email
    ) {}

    public static function fromRequest(UpdateAccountRequest $request): self
    {
        return new self(
            name: $request?->name,
            email: $request?->email
        );
    }
}
