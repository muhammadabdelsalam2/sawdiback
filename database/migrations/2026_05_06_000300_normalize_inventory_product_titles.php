<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('inventory_products')
            ->select(['id', 'name', 'title'])
            ->orderBy('id')
            ->chunkById(100, function ($products): void {
                foreach ($products as $product) {
                    $title = $product->title;
                    $decodedTitle = is_string($title) ? json_decode($title, true) : null;

                    if (is_array($decodedTitle)) {
                        continue;
                    }

                    DB::table('inventory_products')
                        ->where('id', $product->id)
                        ->update([
                            'title' => json_encode([
                                'ar' => $product->name,
                                'en' => $product->name,
                            ], JSON_UNESCAPED_UNICODE),
                        ]);
                }
            });
    }

    public function down(): void
    {
    }
};
