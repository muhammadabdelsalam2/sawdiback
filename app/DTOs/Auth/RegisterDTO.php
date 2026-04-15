<?php

namespace App\DTOs\Auth;


class RegisterDTO
{
    public function __construct(
        public readonly ?string $email,
        public readonly ?string $phone,
    ) {}

    public static function fromRequest( $request): self
    {
        $data = $request->validated();

        return new self(
            $data['email'] ?? null,
            $data['phone'] ?? null,
        );
    }
}
