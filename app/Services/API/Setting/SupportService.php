<?php


namespace App\Services\API\Setting;

use App\DTOs\ٍSetting\SupportItemDTO;
use App\Repositories\Api\Setting\SupportRepository;

class SupportService
{
    public function __construct(
        protected SupportRepository $repository
    ) {
    }

    public function getSupportItems()
    {
        return $this->repository->getAll();
    }

    public function getSupportItemtemsgetBymodule($module)
    {   

        return $this->repository->getBymodule($module);
    }
}
