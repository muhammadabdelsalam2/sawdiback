# Subscriptions & Plans Module

## Overview
This module adds:
- Plan management (CRUD)
- Feature catalog
- Plan-feature assignment (many-to-many with pivot payload)
- Subscription lifecycle workflow with service layer and history logging

## Main Files
- `app/Models/Plan.php`
- `app/Models/Feature.php`
- `app/Models/Subscription.php`
- `app/Models/SubscriptionHistory.php`
- `app/Services/Subscriptions/SubscriptionService.php`
- `app/Http/Controllers/SuperAdmin/PlanController.php`
- `app/Http/Controllers/SuperAdmin/FeatureController.php`
- `app/Http/Controllers/SuperAdmin/SubscriptionController.php`
- `app/Http/Requests/SuperAdmin/*`

## Database
Migrations:
- `plans`
- `features`
- `feature_plan` (pivot with `value` and `enabled`)
- `subscriptions`
- `subscription_histories`

## Access Control
- Routes are under `/{locale}/super-admin/...`
- Protected by middleware: `auth`, `set.locale`, and `superadmin`
- `superadmin` middleware currently supports:
  - `hasRole('SuperAdmin')` if available
  - `role === superadmin` if role attribute exists
  - fallback admin email: `admin@elsawady.com`

## Subscription Service API
- `create(array $data, ?int $actorId = null)`
- `upgradeOrDowngrade(Subscription $subscription, int $newPlanId, ?int $actorId = null)`
- `renew(Subscription $subscription, ?string $fromDate = null, ?int $actorId = null)`
- `cancel(Subscription $subscription, ?int $actorId = null)`
- `expire(Subscription $subscription, ?int $actorId = null)`

All mutating methods use database transactions and write into `subscription_histories`.

## Routing
Defined in `routes/web.php` with route name prefix `superadmin.`:
- `superadmin.plans.*`
- `superadmin.features.*`
- `superadmin.subscriptions.*`

## Tests
Feature tests:
- `tests/Feature/PlanCrudTest.php`
- `tests/Feature/PlanFeatureAssignmentTest.php`
- `tests/Feature/SubscriptionWorkflowTest.php`
