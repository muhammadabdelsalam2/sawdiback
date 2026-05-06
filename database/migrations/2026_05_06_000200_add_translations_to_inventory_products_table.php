<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_products', function (Blueprint $table) {
            $table->json('title')->nullable()->after('name');
            $table->json('description')->nullable()->after('notes');
        });

        DB::table('inventory_products')
            ->select(['id', 'name', 'notes'])
            ->orderBy('id')
            ->chunkById(100, function ($products) {
                foreach ($products as $product) {
                    DB::table('inventory_products')
                        ->where('id', $product->id)
                        ->update([
                            'title' => json_encode([
                                'ar' => $product->name,
                                'en' => $product->name,
                            ], JSON_UNESCAPED_UNICODE),
                            'description' => json_encode([
                                'ar' => $product->notes,
                                'en' => $product->notes,
                            ], JSON_UNESCAPED_UNICODE),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('inventory_products', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });
    }
};
