<?php

namespace App\Services\API\Account;

use App\DTOs\Account\UpdateAccountDTO;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AccountService
{
    public function __construct(
        protected ClientRepositoryInterface $clientRepository
    ) {}

    public function updateAccount(UpdateAccountDTO $dto): array
    {
        $user = $this->clientRepository->findById(Auth::id());

        if (!$user) {
            return [
                'success' => false,
                'message' => __('auth.user_not_found'),
                'data' => null,
                'code' => 404
            ];
        }

        $data = [
            'name' => $dto->name,
            'email' => $dto->email,
        ];

        // If email changed → reset verification
        if ($user->email !== $dto->email) {
            $data['email_verified_at'] = null;
        }

        $user = $this->clientRepository->update($user, $data);

        return [
            'success' => true,
            'message' => __('account.updated_successfully'),
            'data' => $user,
            'code' => 200
        ];
    }
}