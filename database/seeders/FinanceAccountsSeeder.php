<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\Finance\Account;

class FinanceAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::query()->get();

        foreach ($tenants as $tenant) {
            DB::transaction(function () use ($tenant) {
                $assets = Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '1000'],
                    ['name' => 'Assets', 'type' => 'asset', 'parent_id' => null, 'is_active' => true]
                );

                $liabilities = Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '2000'],
                    ['name' => 'Liabilities', 'type' => 'liability', 'parent_id' => null, 'is_active' => true]
                );

                $equity = Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '3000'],
                    ['name' => 'Equity', 'type' => 'equity', 'parent_id' => null, 'is_active' => true]
                );

                $revenues = Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '4000'],
                    ['name' => 'Revenues', 'type' => 'revenue', 'parent_id' => null, 'is_active' => true]
                );

                $expenses = Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '5000'],
                    ['name' => 'Expenses', 'type' => 'expense', 'parent_id' => null, 'is_active' => true]
                );

                Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '1010'],
                    ['name' => 'Cash', 'type' => 'asset', 'parent_id' => $assets->id, 'is_active' => true]
                );

                Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '1020'],
                    ['name' => 'Bank', 'type' => 'asset', 'parent_id' => $assets->id, 'is_active' => true]
                );

                Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '1200'],
                    ['name' => 'Accounts Receivable', 'type' => 'asset', 'parent_id' => $assets->id, 'is_active' => true]
                );

                Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '2100'],
                    ['name' => 'Accounts Payable', 'type' => 'liability', 'parent_id' => $liabilities->id, 'is_active' => true]
                );

                Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '1300'],
                    ['name' => 'Inventory', 'type' => 'asset', 'parent_id' => $assets->id, 'is_active' => true]
                );

                Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '4100'],
                    ['name' => 'Revenue', 'type' => 'revenue', 'parent_id' => $revenues->id, 'is_active' => true]
                );

                Account::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'code' => '5100'],
                    ['name' => 'Expenses', 'type' => 'expense', 'parent_id' => $expenses->id, 'is_active' => true]
                );
            });
        }
    }
}
