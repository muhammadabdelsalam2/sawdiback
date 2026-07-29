# ERP Sawdi Project Audit

Reference: `docs/نظام ERP مزرعة السوادي.pdf`

Audit date: 2026-07-29

## Summary Matrix

| # | Section | Status | Implemented code surface | Missing / Notes |
|---|---|---|---|---|
| 1 | General farm structure | Present | `Farm`, `FarmPen`; tables `farms`, `farm_pens`; routes `{locale}/farms`, `{locale}/farm-pens`, super admin farm dashboards | Farm types exist as `owned` / `rented`; named farms such as Sweihan/Al Hayer are data records, not hardcoded. |
| 2 | HR | Present | `Employee`, `EmployeeAttachment`; tables `employees`, `employee_attachments`; routes `{locale}/hr/*`; command `hr:check-document-expiries` | Attachments now use the public file route, not `public/storage`. |
| 3 | Livestock | Present | `LivestockAnimal`, `FarmPen`, `LivestockPenFinancialEntry`, health/feeding/milk/reproduction models; routes `{locale}/livestock/*` | Added literal requirement field `livestock_animals.intended_purpose` for milk/breeding/sale. Pen is the independent unit and computes animal count, gender count, mortality, net profit. |
| 4 | Crops | Present | `Crop`, `CropGrowthStage`, `CropCostItem`, `CropMaterialUsage`, `CropSeedlingStock`; routes `{locale}/crops-feed/*` | Added literal requirement field `crops.greenhouse_number`; `greenhouse_location` remains for location. Computed total cost, cost per ton, profit/loss, loss rate exist. |
| 5 | Poultry | Present | `PoultryBroilerCycle`, `PoultryLayerFlock`, hatchery, breeds and log models; routes `{locale}/poultry/*` | Net profit, mortality rate, age days, hatch success rate are computed accessors. |
| 6 | Health and vaccines | Present | `Vaccine`, `VaccineBatch`, `AnimalVaccination`, `AnimalHealthRecord`; routes `{locale}/livestock/vaccines`, vaccination alerts | Vaccine batch stock and expiry dates exist; vaccine batches are nullable by farm for central stock. Vaccination links animal and pen; species is available through animal relation. |
| 7 | Feed and inventory | Present | `FeedType`, `FeedStockMovement`, `InventoryProduct`, `InventoryBatch`, `InventoryMovement`; routes `{locale}/inventory/*`, `{locale}/crops-feed/feed` | Low-stock and expiring-batch alerts implemented in services. Product farm link is required in forms and enforced as `NOT NULL` by the 2026-07-29 backfill migration on MySQL. |
| 8 | Sales | Present | Ecommerce `Order`, `OrderItem`; distribution `SalesOrder`, `SalesCustomer`, invoice/payment/shipment models; API `/api/v1/{locale}/products`, `/orders/*` and web sales routes | `Order::farms()`, `scopeLinkedToFarm()`, and `farmLineTotal()` implement indirect farm linking through `order_items -> inventory_products.farm_id`. |
| 9 | Financial reports | Present | `ProfitLossService`, finance dashboard, accounts, expenses, journal entries; routes `{locale}/finance/*` | Department profit, best product, highest cost, mortality rate, staff performance are computed. Staff performance is currently simple attendance/employee-count based. |
| 10 | Warehouse | Present | `WarehouseAsset`, `WarehouseAssetAttachment`; routes `{locale}/warehouse-assets` | Equipment, water pipes, iron, storage location, farm, and attachments exist. Attachments now use the public file route. |
| 11 | Analytics | Present | `AnalyticsService`; route `{locale}/analytics` | Best customer, best product, highest/lowest margin are implemented from sales and finance summaries. |
| 12 | Production | Present | `Plan`, `Subscription`, plus subscription models; routes `{locale}/superadmin/plans`, `{locale}/superadmin/subscriptions`, `/api/v1/{locale}/plus` | Covers packages/plans and subscriptions. |
| 13 | System and users | Present | roles/permissions, super admin access management, user management, profile/settings/logout routes | Permissions follow existing Spatie pattern. |

## Field Comparison Highlights

| Requirement | Existing fields / logic | Change made |
|---|---|---|
| HR worker ID, profession, hire date, salary, status, passport/iqama alerts, attachments, department link | `employees.worker_number`, `profession`, `hire_date`, `salary`, `employment_status`, `passport_expiry_date`, `iqama_expiry_date`, `operational_department`, `farm_id`, `employee_attachments` | Attachment URL accessor changed to `/files/public/...`. |
| Livestock intended purpose | Not present before this pass | Added nullable `intended_purpose`; validation enum: `milk`, `breeding`, `sale`; form and show page updated. |
| Livestock pen independence and financials | `farm_pens`, `livestock_animals.pen_id`, `livestock_pen_financial_entries`, `LivestockPenProfitService` | Already present. |
| Crops greenhouse number | Previously only `greenhouse_location` combined number/location | Added nullable `greenhouse_number`; form and show page updated. |
| Crops loss rate and profit | `wasted_tons`, `yield_tons`, `sale_price_per_ton`, cost/material/water/labor fields with accessors | Already present. |
| Poultry broilers/layers/hatchery/breeds | Dedicated poultry tables with computed accessors | Already present. |
| Vaccine expiry and stock | `vaccine_batches.quantity`, `expiry_date`, `Vaccine::current_stock`, livestock alert routes | Already present. |
| Inventory farm separation | `inventory_products.farm_id`; product UI requires farm | Added backfill + MySQL `NOT NULL` migration for DB-level enforcement. |
| Images and attachments without symlink | Product/category used reusable route; HR/warehouse/user/content/review views/resources still had old URL generation | Added `App\Support\PublicFileUrl`; converted remaining URL generation away from `/storage/`. |

## Endpoint Verification Examples

These examples were captured from Feature tests using Laravel's real routing, validation, models, migrations, and response layer.

### 1. Public Products API

Request:

```http
GET /api/v1/en-SA/products
```

Response excerpt:

```json
{
  "success": true,
  "data": {
    "data": [
      {
        "name": "Corn Mix",
        "image": "http://localhost/files/public/inventory/products/example.png",
        "category": "Feed",
        "farm_id": null
      }
    ]
  }
}
```

Assertions: response is 200; response contains no `/storage/`; image URL opens 200.

### 2. Livestock Animal Create / Show

Request:

```http
POST /en-SA/livestock/animals
```

Payload excerpt:

```json
{
  "tag_number": "GOAT-001",
  "pen_id": 1,
  "species_id": 1,
  "gender": "female",
  "source_type": "purchased",
  "health_status": "healthy",
  "intended_purpose": "milk"
}
```

Verified response/DB result:

```json
{
  "tag_number": "GOAT-001",
  "intended_purpose": "milk"
}
```

Follow-up `GET /en-SA/livestock/animals/{id}` returns 200 and shows `Intended Purpose` / `Milk`.

### 3. Crop Create / Show

Request:

```http
POST /en-SA/crops-feed/crops
```

Payload excerpt:

```json
{
  "farm_id": 1,
  "name": "Tomato",
  "greenhouse_type": "Tunnel",
  "greenhouse_number": "GH-07",
  "greenhouse_location": "North Field",
  "irrigation_type": "ground"
}
```

Verified response/DB result:

```json
{
  "name": "Tomato",
  "greenhouse_number": "GH-07"
}
```

Follow-up `GET /en-SA/crops-feed/crops/{id}` returns 200 and shows `Greenhouse Number` / `GH-07`.

## Verification

Commands run:

```bash
php artisan migrate --force
php artisan test tests/Feature/RequirementFieldsTest.php tests/Feature/InventoryProductImagesTest.php tests/Feature/HrEmployeeDocumentsTest.php tests/Feature/WarehouseAssetsAndSuperAdminDashboardTest.php
```

Focused test result:

```text
9 passed, 105 assertions
```

The local migration command was run against a temporary sqlite database at `database/audit.sqlite` to verify the new migration applies cleanly.

Latest full-suite verification after the staff performance and sidebar changes:

```bash
php artisan test
```

```text
41 passed, 236 assertions
```

## Sidebar Alignment

The customer and super admin dashboard sidebars were reordered to match the ERP requirements document sequence exactly:

| Order | Sidebar Section |
|---:|---|
| 1 | الموارد البشرية (HR) |
| 2 | إدارة الحيوانات |
| 3 | الزراعة |
| 4 | الدواجن |
| 5 | الصحة والتطعيمات |
| 6 | المخزون (الأعلاف + المستلزمات) |
| 7 | المبيعات |
| 8 | التقارير المالية |
| 9 | المستودع |
| 10 | التحليل |
| 11 | الإنتاج |
| 12 | إدارة النظام |
| 13 | إدارة المستخدمين |
| 14 | تسجيل الخروج |

Existing sub-navigation items remain under their closest matching document section, including poultry submodules under الدواجن and settings/farm assignment links under إدارة النظام. Existing links that are useful in the product but are not part of the original requirements document, such as Dashboard, Farms & Pens, E-Commerce, and Procurement, were kept after تسجيل الخروج so the required document order remains directly comparable.

## Added Files

| File | Purpose |
|---|---|
| `app/Support/PublicFileUrl.php` | Reusable safe URL builder for files stored under `storage/app/public` without relying on `public/storage` symlink. |
| `database/migrations/2026_07_29_000100_add_requirement_fields_to_livestock_animals_and_crops.php` | Adds `intended_purpose` and `greenhouse_number`. |
| `database/migrations/2026_07_29_000200_backfill_and_require_inventory_products_farm_id.php` | Backfills null inventory product farm links, ensures required farms for affected tenants, and enforces `inventory_products.farm_id` as `NOT NULL` on MySQL. |
| `tests/Feature/RequirementFieldsTest.php` | Verifies new livestock and crop requirement fields through real web endpoints. |
| `PROJECT_AUDIT.md` | This audit report. |

## Modified Files

| File | Change |
|---|---|
| `app/Models/Concerns/HasPublicFileUrl.php` | Delegates file URL logic to reusable support class. |
| `app/Models/EmployeeAttachment.php` | Attachment URL uses public file route. |
| `app/Models/WarehouseAssetAttachment.php` | Attachment URL uses public file route. |
| `app/Models/LivestockAnimal.php` | Added `intended_purpose` to fillable fields. |
| `app/Models/Crop.php` | Added `greenhouse_number` to fillable fields. |
| `app/Services/Livestock/RegisterAnimalService.php` | Persists `intended_purpose` during animal registration. |
| `app/Services/Finance/ProfitLossService.php` | Fixes staff attendance rate to use employee-day capacity for multi-day periods. |
| `app/Http/Requests/Livestock/LivestockAnimalStoreRequest.php` | Validates `intended_purpose`. |
| `app/Http/Requests/Livestock/LivestockAnimalUpdateRequest.php` | Validates `intended_purpose`. |
| `app/Http/Requests/CropsFeed/CropStoreRequest.php` | Validates `greenhouse_number`. |
| `app/Http/Requests/CropsFeed/CropUpdateRequest.php` | Validates `greenhouse_number`. |
| `app/Http/Resources/UserResource.php` | Avatar URLs use public file route. |
| `app/Http/Resources/ProfileOverviewResource.php` | Avatar URLs use public file route. |
| `app/Http/Resources/ContentResource.php` | Video file URLs use public file route. |
| `app/Http/Controllers/Api/Ecommerce/ReviewController.php` | Review upload URLs use public file route. |
| `app/Http/Controllers/DashboardController.php` | Farm image helper uses public file route. |
| `resources/views/shared/dashboard/navbar.blade.php` | Navbar avatar uses public file route. |
| `resources/views/superadmin/content/_form.blade.php` | Content video preview uses public file route. |
| `resources/views/superadmin/content/index.blade.php` | Content video list preview uses public file route. |
| `resources/views/superadmin/account/settings.blade.php` | Avatar preview uses public file route. |
| `resources/views/superadmin/account/profile.blade.php` | Avatar preview uses public file route. |
| `resources/views/dashboard/customer/account/profile.blade.php` | Avatar preview uses public file route. |
| `resources/views/dashboard/livestock/animals/_form.blade.php` | Adds intended purpose field. |
| `resources/views/dashboard/livestock/animals/show.blade.php` | Shows intended purpose. |
| `resources/views/dashboard/crops_feed/crops/_form.blade.php` | Adds greenhouse number field. |
| `resources/views/dashboard/crops_feed/crops/show.blade.php` | Shows greenhouse number. |
| `resources/lang/en/livestock.php` | Adds intended purpose labels/options. |
| `resources/lang/ar/livestock.php` | Adds intended purpose labels/options. |
| `resources/lang/en/crops_feed.php` | Adds greenhouse number label. |
| `resources/lang/ar/crops_feed.php` | Adds greenhouse number label. |
| `resources/lang/en/dashboard.php` | Adds document-aligned sidebar labels. |
| `resources/lang/ar/dashboard.php` | Adds document-aligned sidebar labels. |
| `resources/views/shared/dashboard/customer/partial/sidebar.blade.php` | Reorders customer sidebar to match the ERP requirements document while preserving permissions. |
| `resources/views/shared/dashboard/superadmin/partial/sidebar.blade.php` | Reorders super admin sidebar to match the ERP requirements document while preserving permissions. |
| `tests/Feature/HrEmployeeDocumentsTest.php` | Verifies HR attachments open through `/files/public/...`. |
| `tests/Feature/WarehouseAssetsAndSuperAdminDashboardTest.php` | Verifies warehouse attachments open through `/files/public/...`. |
| `tests/Feature/OperationalClosureTest.php` | Verifies multi-day staff attendance rate remains between 0 and 100. |
