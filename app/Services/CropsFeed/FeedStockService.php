<?php

namespace App\Services\CropsFeed;

use App\Models\FeedStockMovement;

class FeedStockService
{
    public function stockOnHand(int $feedTypeId): float
    {
        $incoming = (float) FeedStockMovement::query()
            ->where('feed_type_id', $feedTypeId)
            ->where('movement_type', 'in')
            ->sum('quantity');

        $outgoing = (float) FeedStockMovement::query()
            ->where('feed_type_id', $feedTypeId)
            ->where('movement_type', 'out')
            ->sum('quantity');

        return round($incoming - $outgoing, 2);
    }
}
