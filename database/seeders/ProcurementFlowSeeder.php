<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\InventoryProduct;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Finance\Account;
use App\Services\Procurement\PurchaseRequisitionService;
use App\Services\Procurement\RfqService;
use App\Services\Procurement\QuotationService;
use App\Services\Procurement\PurchaseOrderService;
use App\Services\Procurement\GoodsReceiptService;
use App\Services\Procurement\PurchaseInvoiceService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProcurementFlowSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $user = User::query()
                ->where('email', 'customer@elsawady.com')
                ->first();

            if (!$user) {
                throw new \RuntimeException('User customer@elsawady.com not found.');
            }

            Auth::login($user);

            $tenantId = $user->tenant_id;
            if (!$tenantId) {
                throw new RuntimeException('customer@elsawady.com must have tenant_id set before running ProcurementFlowSeeder.');
            }

            $accountsPayableExists = Account::query()
                ->where('tenant_id', $tenantId)
                ->where('code', '2100')
                ->exists();

            if (!$accountsPayableExists) {
                $this->call(FinanceAccountsSeeder::class);
            }

            $departments = collect([
                Department::query()->firstOrCreate(
                    ['tenant_id' => $tenantId, 'code' => 'D-001'],
                    [
                        'tenant_id'   => $tenantId,
                        'name'        => 'Feed',
                        'description' => 'Feed department',
                        'is_active'   => true,
                    ]
                ),
                Department::query()->firstOrCreate(
                    ['tenant_id' => $tenantId, 'code' => 'D-002'],
                    [
                        'tenant_id'   => $tenantId,
                        'name'        => 'Veterinary',
                        'description' => 'Veterinary department',
                        'is_active'   => true,
                    ]
                ),
                Department::query()->firstOrCreate(
                    ['tenant_id' => $tenantId, 'code' => 'D-003'],
                    [
                        'tenant_id'   => $tenantId,
                        'name'        => 'Maintenance',
                        'description' => 'Maintenance department',
                        'is_active'   => true,
                    ]
                ),
            ]);

            $products = collect([
                InventoryProduct::query()->firstOrCreate(
                    ['tenant_id' => $tenantId, 'code' => 'FD-001'],
                    [
                        'tenant_id'           => $tenantId,
                        'name'                => 'Corn Feed 25kg',
                        'category'            => 'feed',
                        'unit'                => 'bag',
                        'tax'                 => 0,
                        'track_expiry'        => true,
                        'low_stock_threshold' => 10,
                        'is_active'           => true,
                    ]
                ),
                InventoryProduct::query()->firstOrCreate(
                    ['tenant_id' => $tenantId, 'code' => 'FD-002'],
                    [
                        'tenant_id'           => $tenantId,
                        'name'                => 'Alfalfa Hay 20kg',
                        'category'            => 'feed',
                        'unit'                => 'bag',
                        'tax'                 => 0,
                        'track_expiry'        => true,
                        'low_stock_threshold' => 8,
                        'is_active'           => true,
                    ]
                ),
                InventoryProduct::query()->firstOrCreate(
                    ['tenant_id' => $tenantId, 'code' => 'VM-001'],
                    [
                        'tenant_id'           => $tenantId,
                        'name'                => 'Antibiotic Injection',
                        'category'            => 'vet_medicine',
                        'unit'                => 'box',
                        'tax'                 => 0,
                        'track_expiry'        => true,
                        'low_stock_threshold' => 5,
                        'is_active'           => true,
                    ]
                ),
                InventoryProduct::query()->firstOrCreate(
                    ['tenant_id' => $tenantId, 'code' => 'VM-002'],
                    [
                        'tenant_id'           => $tenantId,
                        'name'                => 'Vitamin Mix',
                        'category'            => 'vet_medicine',
                        'unit'                => 'box',
                        'tax'                 => 0,
                        'track_expiry'        => true,
                        'low_stock_threshold' => 5,
                        'is_active'           => true,
                    ]
                ),
                InventoryProduct::query()->firstOrCreate(
                    ['tenant_id' => $tenantId, 'code' => 'EQ-001'],
                    [
                        'tenant_id'           => $tenantId,
                        'name'                => 'Milking Machine Parts',
                        'category'            => 'equipment',
                        'unit'                => 'set',
                        'tax'                 => 0,
                        'track_expiry'        => false,
                        'low_stock_threshold' => 2,
                        'is_active'           => true,
                    ]
                ),
                InventoryProduct::query()->firstOrCreate(
                    ['tenant_id' => $tenantId, 'code' => 'EQ-002'],
                    [
                        'tenant_id'           => $tenantId,
                        'name'                => 'Water Pump',
                        'category'            => 'equipment',
                        'unit'                => 'unit',
                        'tax'                 => 0,
                        'track_expiry'        => false,
                        'low_stock_threshold' => 1,
                        'is_active'           => true,
                    ]
                ),
            ]);

            $suppliers = collect([
                Supplier::query()->firstOrCreate(
                    ['email' => 'sales@alwatan-feed.test'],
                    [
                        'name'      => 'Al Watan Feed Co.',
                        'phone'     => '+966500001001',
                        'address'   => 'Riyadh Industrial Area',
                        'is_active' => true,
                    ]
                ),
                Supplier::query()->firstOrCreate(
                    ['email' => 'contact@vetcare.test'],
                    [
                        'name'      => 'VetCare Supplies',
                        'phone'     => '+966500001002',
                        'address'   => 'Jeddah Medical District',
                        'is_active' => true,
                    ]
                ),
                Supplier::query()->firstOrCreate(
                    ['email' => 'sales@feh.test'],
                    [
                        'name'      => 'Farm Equipment Hub',
                        'phone'     => '+966500001003',
                        'address'   => 'Dammam Tech Park',
                        'is_active' => true,
                    ]
                ),
                Supplier::query()->firstOrCreate(
                    ['email' => 'inactive@suppliers.test'],
                    [
                        'name'      => 'Inactive Supplier',
                        'phone'     => '+966500001004',
                        'address'   => 'Inactive Address',
                        'is_active' => false,
                    ]
                ),
            ]);

            $requisitionService = app(PurchaseRequisitionService::class);
            $rfqService         = app(RfqService::class);
            $quotationService   = app(QuotationService::class);
            $purchaseOrderService = app(PurchaseOrderService::class);
            $goodsReceiptService  = app(GoodsReceiptService::class);
            $invoiceService       = app(PurchaseInvoiceService::class);

            $reqPending1 = $requisitionService->create([
                'department_id' => $departments[0]->id,
                'requested_by'  => $user->id,
                'status'        => 'pending',
                'notes'         => 'Routine feed restock.',
                'items' => [
                    ['product_id' => $products[0]->id, 'quantity' => 20, 'estimated_price' => 110],
                    ['product_id' => $products[1]->id, 'quantity' => 15, 'estimated_price' => 95],
                ],
            ]);

            $reqPending2 = $requisitionService->create([
                'department_id' => $departments[1]->id,
                'requested_by'  => $user->id,
                'status'        => 'pending',
                'notes'         => 'Vaccine & supplements request.',
                'items' => [
                    ['product_id' => $products[2]->id, 'quantity' => 10, 'estimated_price' => 250],
                    ['product_id' => $products[3]->id, 'quantity' => 12, 'estimated_price' => 180],
                ],
            ]);

            $reqApproved = $requisitionService->create([
                'department_id' => $departments[2]->id,
                'requested_by'  => $user->id,
                'status'        => 'approved',
                'notes'         => 'Maintenance tooling.',
                'items' => [
                    ['product_id' => $products[4]->id, 'quantity' => 2, 'estimated_price' => 1200],
                ],
            ]);

            $reqDownstream = $requisitionService->create([
                'department_id' => $departments[0]->id,
                'requested_by'  => $user->id,
                'status'        => 'converted_to_po',
                'notes'         => 'Urgent feed delivery.',
                'items' => [
                    ['product_id' => $products[0]->id, 'quantity' => 30, 'estimated_price' => 108],
                    ['product_id' => $products[1]->id, 'quantity' => 20, 'estimated_price' => 92],
                ],
            ]);

            $rfqOpen = $rfqService->create([
                'purchase_requisition_id' => $reqPending1->id,
                'status' => 'open',
            ]);

            $rfqSent = $rfqService->create([
                'purchase_requisition_id' => $reqPending2->id,
                'status' => 'sent',
            ]);

            $rfqAwarded = $rfqService->create([
                'purchase_requisition_id' => $reqDownstream->id,
                'status' => 'awarded',
            ]);

            $quotationSelected = $quotationService->create([
                'rfq_id'       => $rfqAwarded->id,
                'supplier_id'  => $suppliers[0]->id,
                'status'       => 'selected',
                'items' => [
                    ['product_id' => $products[0]->id, 'quantity' => 30, 'unit_price' => 105],
                    ['product_id' => $products[1]->id, 'quantity' => 20, 'unit_price' => 90],
                ],
            ]);

            $quotationRejected = $quotationService->create([
                'rfq_id'       => $rfqAwarded->id,
                'supplier_id'  => $suppliers[1]->id,
                'status'       => 'rejected',
                'items' => [
                    ['product_id' => $products[0]->id, 'quantity' => 30, 'unit_price' => 112],
                    ['product_id' => $products[1]->id, 'quantity' => 20, 'unit_price' => 98],
                ],
            ]);

            $quotationSent = $quotationService->create([
                'rfq_id'      => $rfqSent->id,
                'supplier_id' => $suppliers[1]->id,
                'status'      => 'submitted',
                'items' => [
                    ['product_id' => $products[2]->id, 'quantity' => 10, 'unit_price' => 245],
                    ['product_id' => $products[3]->id, 'quantity' => 12, 'unit_price' => 175],
                ],
            ]);

            $poDraft = $purchaseOrderService->create([
                'supplier_id' => $suppliers[2]->id,
                'status'      => 'draft',
                'vat'         => 0,
                'items' => [
                    ['product_id' => $products[4]->id, 'quantity' => 2, 'unit_price' => 1180],
                ],
            ]);

            $poConfirmed = $purchaseOrderService->create([
                'supplier_id'  => $suppliers[1]->id,
                'rfq_id'       => $rfqSent->id,
                'quotation_id' => $quotationSent->id,
                'status'       => 'confirmed',
                'vat'          => 0,
                'items' => [
                    ['product_id' => $products[2]->id, 'quantity' => 10, 'unit_price' => 245],
                    ['product_id' => $products[3]->id, 'quantity' => 12, 'unit_price' => 175],
                ],
            ]);

            $poFromSelected = $purchaseOrderService->createFromQuotation($quotationSelected, [
                'status' => 'confirmed',
            ]);

            $grnPartial = $goodsReceiptService->create([
                'purchase_order_id' => $poFromSelected->id,
                'received_by'       => $user->id,
                'status'            => 'partial',
                'items' => [
                    ['product_id' => $products[0]->id, 'quantity' => 10],
                    ['product_id' => $products[1]->id, 'quantity' => 8],
                ],
            ]);

            $grnCompleted = $goodsReceiptService->create([
                'purchase_order_id' => $poFromSelected->id,
                'received_by'       => $user->id,
                'status'            => 'completed',
                'items' => [
                    ['product_id' => $products[0]->id, 'quantity' => 20],
                    ['product_id' => $products[1]->id, 'quantity' => 12],
                ],
            ]);

            $invoiceService->create([
                'supplier_id'       => $suppliers[2]->id,
                'department_id'     => $departments[2]->id,
                'purchase_order_id' => $poDraft->id,
                'status'            => 'draft',
                'invoice_date'      => now()->toDateString(),
                'subtotal'          => $poDraft->total,
                'tax'               => 0,
                'discount'          => 0,
                'notes'             => 'Draft invoice for equipment.',
            ]);

            $invoiceService->create([
                'supplier_id'       => $suppliers[0]->id,
                'department_id'     => $departments[0]->id,
                'purchase_order_id' => $poFromSelected->id,
                'goods_receipt_id'  => $grnCompleted->id,
                'status'            => 'posted',
                'invoice_date'      => now()->toDateString(),
                'subtotal'          => $poFromSelected->total,
                'tax'               => $poFromSelected->vat,
                'discount'          => 0,
                'notes'             => 'Posted invoice for completed receipt.',
            ]);

            $invoiceService->create([
                'supplier_id'       => $suppliers[1]->id,
                'department_id'     => $departments[1]->id,
                'purchase_order_id' => $poConfirmed->id,
                'status'            => 'posted',
                'invoice_date'      => now()->toDateString(),
                'subtotal'          => $poConfirmed->total,
                'tax'               => $poConfirmed->vat,
                'discount'          => 0,
                'notes'             => 'Posted invoice for vet supplies.',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
