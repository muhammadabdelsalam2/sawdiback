<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Currency::create([
            'code' => 'SAR',
            'symbol' => 'ر.س',
            'rate' => 1, // Base currency
            'is_default' => true,
        ]);

        Currency::create([
            'code' => 'EGP',
            'symbol' => 'ج.م',
            'rate' => 0.19, // Example rate (1 SAR ≈ 0.19 EGP)
            'is_default' => false,
        ]);

        Currency::create([
            'code' => 'USD',
            'symbol' => '$',
            'rate' => 0.27, // Example rate (1 SAR ≈ 0.27 USD)
            'is_default' => false,
        ]);
    }
}
