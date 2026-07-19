# ERP Sawdi Audit Report

Date: 2026-07-14

## Scope And Sources

This report is based on the actual Laravel project in `I:\Development\larvel\work\sawdiback`.

Attached source files found:

- `C:\Users\vip\.codex\attachments\f50faebd-e66a-4261-8cd5-00725a322087\pasted-text.txt`
- `C:\Users\vip\.codex\attachments\eb75588a-d1e8-437d-b995-381704c5415e\pasted-text.txt`

Both contain the same ERP audit prompt. No separate full technical report document or separate original owner-requirements document was found in the currently open/mentioned attachments. Therefore, the technical source summary and 14 requirement items embedded in the prompt were used as the available source of truth.

## Project Map

High-level file counts from the requested areas:

| Area | Count |
|---|---:|
| `routes` | 7 |
| `database/migrations` | 96 |
| `database/seeders` | 34 |
| `app/Models` direct | 76 |
| `app/Models` subfolders | 12 |
| `app/Http/Controllers` | 42 |
| `app/Http/Requests` | 122 |
| `app/Services` | 68 |
| `app/Repositories` | 79 |
| `app/DTOs` | 9 |
| `resources/views` | 214 |
| `resources/lang/ar` | 18 |
| `resources/lang/en` | 21 |

Main dashboard route files reviewed:

- `routes/web.php`
- `routes/web/customer.php`
- `routes/web/superadmin.php`
- `routes/api.php`
- `routes/api/customer.php`
- `routes/api/ecommerce.php`

RBAC seeders reviewed:

- `database/seeders/RolePermissionSeeder.php`
- `database/seeders/RoleSeeder.php`

The seeded Spatie permissions are exactly 14:

`dashboard.view`, `roles.manage`, `animals.view`, `animals.manage`, `production.view`, `production.manage`, `inventory.view`, `inventory.manage`, `sales.view`, `sales.manage`, `hr.view`, `hr.manage`, `settings.view`, `settings.manage`.

## Implemented Modules Observed

Customer dashboard modules are present in `routes/web/customer.php`:

- Livestock: animals, species, breeds, feed types, vaccines, reproduction cycles, alerts.
- Inventory/Warehouse: categories, products, batches, movements, production, deliveries, alerts, traceability.
- Crops & Feed: crops, growth stages, crop costs, feed stock movements, feed consumption, crop allocations, reports.
- Sales & Distribution: customers, contracts, orders, shipments, invoices, payments.
- Procurement: suppliers, requisitions, RFQs, quotations, purchase orders, goods receipts, invoices.
- Finance: dashboard, accounts, journal entries, ledger, expenses, profit/loss.
- Ecommerce dashboard: orders only.
- HR: departments, job titles, employees, attendance, leaves, gated by `feature:hr_management`.
- Subscription: customer subscription.

SuperAdmin dashboard modules are present in `routes/web/superadmin.php`:

- Dashboard, profile, settings, password.
- Access management, users, plans, features, subscriptions.
- Content.
- Countries, cities, theme settings.

## Traceability Matrix

| # | Requirement | Status | Evidence | Missing / Risk |
|---:|---|---|---|---|
| 1 | HR | Partial | `employees`, `departments`, `job_titles`, `attendances`, `leave_requests`; dashboard views under `resources/views/dashboard/customer/hr`; controllers under `app/Http/Controllers/Customer/HR`. | Missing worker number, profession/specialty as a direct field, detailed worker state enum `active/on_leave/contract_ended`, passport/iqama expiry dates, automatic passport/iqama alerts, attachments for passport/iqama/ID, and fixed section taxonomy for poultry/crops/livestock. Current employee success messages are hardcoded in `EmployeeController`. |
| 2 | Livestock Registry | Partial | `livestock_animals`, `animal_species`, `animal_breeds`, feeding, health, vaccination, reproduction, birth, milk, weight, status history tables in `2026_02_16_000100_create_livestock_management_tables.php`; dashboard views under `resources/views/dashboard/livestock`. | No pen/barn entity or pen number. Current design is animal-by-animal, not "each pen as independent unit". Missing animal count per pen, male/female counts per pen, sales fields, slaughter/packaging costs, automatic net profit per pen, and intended benefit enum. |
| 3 | Crops | Partial | `crops`, `crop_growth_stages`, `crop_cost_items`, `crop_feed_allocations`; `Crop` has automatic `total_cost`, `cost_per_ton`, `profit_or_loss`. | Missing crop materials used as structured fields, greenhouse type/location, irrigation type, expected harvest date, daily/weekly production, water/labor cost categories as structured fields, waste percentage, seed/seedling stock. |
| 4 | Poultry | Not found | Search found only product/category labels for Poultry in category seeders. | No broiler cycles, layer flock, hatcheries, or chicken breed modules/models/controllers/views. |
| 5 | Health And Vaccinations | Partial | `vaccines`, `animal_vaccinations`, `LivestockAlertService` vaccination due/overdue alerts, dashboard alert views. | Vaccine inventory quantity/stock is not modeled in `vaccines`; no vaccine expiry date field. Link is animal-based, not animal type plus pen number. |
| 6 | Feed And Inventory | Partial | `feed_types`, `feed_stock_movements`, `inventory_products`, `inventory_batches`, `inventory_movements`, `WarehouseAlertService::lowStockProducts()`. | Inventory supports category/unit/stock threshold/expiry, but farm location/supplier/cost are not complete as first-class fields on product. Feed type categories are concentrate/roughage/supplement, not required `feed/seed/equipment`. |
| 7 | Sales | Partial | Sales customers/orders/invoices/payments in `2026_02_24_100000_create_sales_distribution_tables.php`; dashboard views under `resources/views/dashboard/customer/sales_distribution`. | General sales are present, but product taxonomy is not constrained to eggs/chicken/vegetables. Payment method exists on `sales_payments.method`. |
| 8 | Financial Reports | Partial | Finance P&L uses journal entries via `ProfitLossService`; finance dashboard and profit/loss views exist. | No specific report for profit by poultry/crops/livestock, best product, highest cost, mortality percentage, or worker performance. |
| 9 | Warehouse | Partial | Warehouse inventory products/batches/movements/deliveries/traceability views exist. | Equipment, water pipes, iron, and attachments are not explicitly modeled as warehouse asset types. |
| 10 | Analytics | Partial | SuperAdmin dashboard has customer/order analytics; sales dashboard exists. | No explicit best customer, best product, highest margin, lowest margin analysis for farm operations. |
| 11 | Production / Plans / Subscriptions | Partial | Plans/features/subscriptions exist in SuperAdmin and customer subscription dashboard. | Requirement wording says "production: packages, subscriptions"; subscription side exists, but not farm production packages if intended separately. |
| 12 | System Management | Partial | SuperAdmin settings, countries, cities, theme, access management exist. | Some settings routes are not guarded by specific `settings.*` permissions, mostly role-level or `roles.manage`. |
| 13 | User Management | Present | `SuperAdmin\UserManagementController`, `resources/views/dashboard/superadmin/users`, routes guarded by `role:SuperAdmin` and `permission:roles.manage`. | Permission choice may be too broad: user management uses `roles.manage` instead of a dedicated user permission. |
| 14 | Logout | Present | Web logout route in `routes/web.php`; sidebar logout form posts to `logout`; API logout exists. | No issue found in this pass. |

## API-Only Endpoints Without Matching Dashboard

The Ecommerce/API surface has many mobile/account features without dashboard screens:

- Account address book/payment methods/notification settings/wallet/contact update/profile APIs.
- Cart, checkout, reviews, address management APIs.
- Plus subscription setup/manage/skip APIs.
- Public content create/update/delete API endpoints are present, while SuperAdmin content dashboard also exists.

Per the prompt rule, API-only features count as missing if they are part of the required dashboard scope. Several ecommerce client features appear mobile-app oriented rather than farm ERP dashboard oriented, so they should be classified before implementation.

## i18n Findings

Translation structure exists under `resources/lang/ar` and `resources/lang/en`, and dashboard layout sets `dir` from `$direction`.

Confirmed hardcoded user-facing strings:

- `app/Http/Controllers/Customer/HR/EmployeeController.php`: `Employee created.`, `Employee updated.`, `Employee deleted.`
- API controllers contain hardcoded success messages in account/wallet/plus/product/address flows.
- `resources/views/layouts/customer/dashboard.blade.php` has default title `My SaaS`.

Also, `resources/lang/en` has `validation.php`, `passwords.php`, and `pagination.php`, while `resources/lang/ar` does not include matching files in the current tree.

## Automatic Calculation And Alert Findings

Implemented:

- Crop `total_cost`, `cost_per_ton`, `profit_or_loss` accessors in `app/Models/Crop.php`.
- Finance P&L net profit in `app/Services/Finance/ProfitLossService.php`.
- Livestock vaccination due/overdue, expected deliveries, under-treatment alerts in `app/Services/Livestock/LivestockAlertService.php`.
- Warehouse low-stock and expiring-batch alerts in `app/Services/Warehouse/WarehouseAlertService.php`.

Missing:

- Net profit per livestock pen/unit.
- Net profit for broiler cycle and layer flock.
- Mortality percentage for livestock/poultry.
- Crop waste percentage.
- Hatchery success percentage.
- Employee passport/iqama expiry alerts.
- Vaccine expiry/stock alerts as vaccine inventory.

## RBAC Findings

Global route guarding exists:

- Customer routes: `set.locale`, `auth`, `role:Customer|SuperAdmin`.
- SuperAdmin routes: `set.locale`, `auth`, `role:SuperAdmin`.
- Some SuperAdmin areas additionally use `permission:roles.manage`.
- HR routes additionally use `feature:hr_management`.

Risk:

- Customer module groups are not protected by fine-grained permissions like `animals.view`, `animals.manage`, `inventory.view`, etc. The seeded permissions exist, but most customer dashboard modules rely on role-level access only.
- User management, plans, features, subscriptions, and access management share `roles.manage`, which is broader than the feature-specific permissions listed in the seeder.

## Farm Separation Finding

The code consistently uses `tenant_id` for multi-tenant separation. No explicit model/table for the four named farms `Suweihan`, `Al Hayer`, `Rent`, `Owned` was found in this pass. Current separation appears tenant-level, not farm-unit-level inside one tenant.

## Execution Priority From This Audit

Do not start implementation until the missing scope is confirmed or the full audit is accepted. The largest missing domain is Poultry, followed by farm-unit/pen modeling and HR document/expiry tracking.

Recommended implementation order:

1. Poultry module: broiler cycles, layers, hatcheries, chicken breeds, dashboard screens, translations, permissions.
2. Farm unit and pen model: four farms, pens/barns scoped under each farm, attach livestock/poultry/crops/inventory to farm units.
3. HR document and expiry tracking: passport/iqama/ID attachments and alerts.
4. Livestock financial calculations per pen/unit.
5. Crop missing fields and waste percentage.
6. Vaccine inventory and expiry.
7. Fine-grained permission middleware for dashboard modules.
8. Translation cleanup for hardcoded strings and missing language files.
