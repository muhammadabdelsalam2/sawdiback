<?php

namespace App\DTOs\Auth;
namespace App\DTOs\Auth;


class RegisterDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly string $password
    ) {}

    public static function fromRequest( $request): self
    {
        $data = $request->validated();

        return new self(
            $data['name'],
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['password'],
        );
    }
}