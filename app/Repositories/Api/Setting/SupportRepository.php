<?php
namespace App\Repositories\Api\Setting;

// use App\DTOs\ٍSetting\SupportItemDTO;
use App\Models\SupportItem;
use App\Repositories\Contracts\Setting\SupportRepositoryInterface;

class SupportRepository implements SupportRepositoryInterface
{
    public function getAll()
    {
        return SupportItem::active()
            ->ordered()
            ->get();
    }
}