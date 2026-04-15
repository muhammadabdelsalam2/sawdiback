<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'title' => $this->title,
            'description' => $this->description,
            'amount' => (float) $this->amount,
            'formatted_amount' => ($this->amount >= 0 ? '+' : '') . $this->wallet->currency . ' ' . number_format((float) $this->amount, 2, '.', ''),
            'points' => (int) $this->points,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toDateTimeString(),
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}
