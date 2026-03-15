<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletOverviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $pointsPerAmount = (int) config('wallet.points.conversion.points', 100);
        $amountPerBlock = (float) config('wallet.points.conversion.amount', 1);

        $redeemableAmount = 0;
        if ($pointsPerAmount > 0) {
            $redeemableAmount = ((int) $this->points_balance / $pointsPerAmount) * $amountPerBlock;
        }

        return [
            'wallet' => [
                'id' => $this->id,
                'balance' => (float) $this->balance,
                'currency' => $this->currency,
                'formatted_balance' => $this->currency . ' ' . number_format((float) $this->balance, 2, '.', ''),
            ],
            'loyalty_points' => [
                'balance' => (int) $this->points_balance,
                'conversion_rule' => [
                    'points' => $pointsPerAmount,
                    'amount' => $amountPerBlock,
                    'currency' => $this->currency,
                    'label' => "{$pointsPerAmount} Points = {$amountPerBlock} {$this->currency}",
                ],
                'redeemable_amount' => round($redeemableAmount, 2),
                'formatted_redeemable_amount' => $this->currency . ' ' . number_format((float) $redeemableAmount, 2, '.', ''),
            ],
            'history' => WalletTransactionResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
