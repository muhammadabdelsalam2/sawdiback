<?php

namespace App\DTOs\Auth;

namespace App\DTOs\Auth;

class LoginDTO
{
    public readonly string $identifier;
    public readonly string $password;
    public readonly string $type;

    public function __construct(
        string $identifier,
    ) {
        $this->identifier = $identifier;
        $this->type = $this->detectType($identifier);
    }

    public static function fromRequest($request): self
    {
        return new self(
            identifier: $request->identifier,
        );
    }

    private function detectType(string $identifier): string
    {
        return filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';
    }

    public function isEmail(): bool
    {
        return $this->type === 'email';
    }

    public function isPhone(): bool
    {
        return $this->type === 'phone';
    }
}
