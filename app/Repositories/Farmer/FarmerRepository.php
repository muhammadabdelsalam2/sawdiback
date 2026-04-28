<?php



namespace App\Repositories\Farmer;

use App\DTOs\Farmer\FarmerDTO;
use App\Models\Farmer;
use App\Repositories\Contracts\Farmer\FarmerRepositoryInterface;

class FarmerRepository implements FarmerRepositoryInterface
{
    protected $model;
    public function __construct(Farmer $model)
    {
        $this->model = $model;
    }
    // Get all farmers with pagination
    public function getAll(int $perPage = 15)
    {
        return $this->model->latest()->paginate($perPage);
    }

    public function create(FarmerDTO $dto)
    {
        return Farmer::create([
            'name' => $dto->name,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'opening_balance' => $dto->opening_balance,
            'address' => $dto->address,
            'is_active' => $dto->is_active,
        ]);
    }

}