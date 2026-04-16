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

    public function getBymodule($module)
    {
        return SupportItem::active()
            ->ordered()
            ->where('module', $module)
            ->get();
    }

    public function find(int $id): ?SupportItem
    {
        $item = SupportItem::active()
            ->ordered()
            ->find($id);
        return $item;
    }
}