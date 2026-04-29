<?php

namespace App\Services\Farmer;

use App\DTOs\Farmer\FarmerDTO;
use App\Models\Farmer;
use App\Repositories\Farmer\FarmerRepository;

class FarmerService
{
    protected $farmerRepository;

    public function __construct(FarmerRepository $farmerRepository)
    {
        // Initialize any dependencies here, such as repositories
        $this->farmerRepository = $farmerRepository;
    }

    // Get all farmers with pagination
    public function getAllFarmers(int $perPage = 15)
    {
        // Implement logic to retrieve farmers with pagination
        // This is a placeholder implementation, replace with actual logic
        // Test If Farmer have Data or not
        return $this->farmerRepository->getAll($perPage);
    }

    public function create(FarmerDTO $dto)
    {
        return $this->farmerRepository->create($dto);
    }


    public function getProductsByFarmer(Farmer $farmer)
    {
        return $this->farmerRepository->getProductsByFarmer($farmer);
    }

    public function getOrdersByFarmer(Farmer $farmer)
    {
        return $this->farmerRepository->getOrdersByFarmer($farmer);
    }

    public function getLivestockByFarmer(Farmer $farmer)
    {
        // Assuming Farmer has a relationship defined as livestock()
        return [];
    }

}