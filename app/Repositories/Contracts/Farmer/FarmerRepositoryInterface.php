<?php


namespace App\Repositories\Contracts\Farmer;

use App\DTOs\Farmer\FarmerDTO;

interface FarmerRepositoryInterface
{

    // public function create(array $data);

    // public function update(int $id, array $data);

    // Get all farmers with pagination
    public function getAll(int $perPage = 15);
    public function create(FarmerDTO $farmerDTO);
    // public function update(int $id, FarmerDTO $farmerDTO);
}