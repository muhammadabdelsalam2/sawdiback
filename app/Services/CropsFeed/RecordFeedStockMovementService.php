<?php

namespace App\Services\CropsFeed;

use App\Models\FeedStockMovement;
use App\Models\FeedType;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RecordFeedStockMovementService
{
    public function __construct(private readonly FeedStockService $stockService)
    {
    }

    public function execute(array $data): FeedStockMovement
    {
        return DB::transaction(function () use ($data) {
            $tenantId = $data['tenant_id'] ?? (string) auth()->user()->tenant_id;
            $quantity = (float) $data['quantity'];

            if ($quantity <= 0) {
                throw new RuntimeException(__('crops_feed.messages.validation.quantity_positive') ?? 'يجب أن تكون الكمية عدداً موجباً.');
            }

            $feedType = FeedType::query()->where('tenant_id', $tenantId)->findOrFail($data['feed_type_id']);

            // ضبط وحدة القياس: إذا تم الإدخال بالطن وكان العلف مسجلاً بالكيلو يتم التحويل للتوحيد
            $unit = $data['unit'] ?? 'kg';
            $normalizedQuantity = ($unit === 'ton') ? ($quantity * 1000) : $quantity;

            $unitCost = array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null
                ? (float) $data['unit_cost']
                : null;

            $totalCost = $unitCost !== null ? round($unitCost * $quantity, 2) : null;

            if ($data['movement_type'] === 'out') {
                $stock = $this->stockService->stockOnHand((int) $feedType->id);
                if ($stock < $normalizedQuantity) {
                    throw new RuntimeException(__('crops_feed.messages.validation.insufficient_stock') ?? 'رصيد العلف الحالي لا يكفي لهذه العملية.');
                }
            }

            // تحديد مصدر العلف (مشترى / مزروع داخلياً بالمزرعة / يدوي)
            $sourceType = $data['source_type'] ?? 'purchased';

            return FeedStockMovement::query()->create([
                'tenant_id'     => $tenantId,
                'feed_type_id'  => $feedType->id,
                'movement_type' => $data['movement_type'],
                'quantity'      => $normalizedQuantity,
                'unit_cost'     => $unitCost,
                'total_cost'    => $totalCost,
                'movement_date' => $data['movement_date'],
                'source_type'   => $sourceType,
                'source_id'     => $data['source_id'] ?? null,
                'notes'         => $data['notes'] ?? null,
            ]);
        });
    }
}
