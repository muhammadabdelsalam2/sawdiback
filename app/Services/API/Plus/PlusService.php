<?php

namespace App\Services\API\Plus;

use App\Models\Category;
use App\Models\PlusSubscription;
use App\Models\PlusSubscriptionSkip;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserPaymentMethod;
use App\Repositories\Contracts\Api\PlusRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlusService
{
    public function __construct(
        protected PlusRepositoryInterface $plusRepository
    ) {
    }

    public function overview(User $user): array
    {
        $subscription = $this->getNormalizedCurrentSubscription($user);

        return [
            'banner' => $this->buildBannerPayload($subscription),
            'pricing' => [
                'monthly_price' => (float) config('plus.pricing.monthly_price', 50),
                'currency' => config('plus.pricing.currency', 'AED'),
                'label' => sprintf(
                    '%s %s/Month',
                    number_format((float) config('plus.pricing.monthly_price', 50), 0),
                    config('plus.pricing.currency', 'AED')
                ),
                'cancel_anytime' => true,
            ],
            'benefits' => config('plus.benefits', []),
            'how_it_works' => config('plus.how_it_works', []),
            'is_subscribed' => (bool) $subscription,
            'cta' => [
                'primary_text' => $subscription ? 'Manage Subscription' : 'Subscribe Now',
                'secondary_text' => $subscription ? 'Manage Orders' : 'Shop & Schedule Now',
            ],
            'current_subscription' => $subscription
                ? $this->transformSubscriptionSummary($subscription)
                : null,
        ];
    }

    public function setup(User $user): array
    {
        $subscription = $this->getNormalizedCurrentSubscription($user);
        $addresses = $this->plusRepository->getUserAddresses($user);
        $paymentMethods = $this->plusRepository->getUserPaymentMethods($user);
        $categories = $this->plusRepository->getAvailableCategories();

        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
        $defaultPaymentMethod = $paymentMethods->firstWhere('is_default', true) ?? $paymentMethods->first();

        return [
            'banner' => $this->buildBannerPayload($subscription),
            'pricing' => [
                'monthly_price' => (float) config('plus.pricing.monthly_price', 50),
                'currency' => config('plus.pricing.currency', 'AED'),
            ],
            'is_subscribed' => (bool) $subscription,
            'requirements' => [
                'has_address' => $addresses->isNotEmpty(),
                'has_payment_method' => $paymentMethods->isNotEmpty(),
                'can_subscribe' => $addresses->isNotEmpty() && $paymentMethods->isNotEmpty() && !$subscription,
            ],
            'frequency_options' => config('plus.frequency_options', []),
            'defaults' => [
                'address_id' => $subscription?->user_address_id ?? $defaultAddress?->id,
                'payment_method_id' => $subscription?->user_payment_method_id ?? $defaultPaymentMethod?->id,
                'frequency' => $subscription?->frequency ?? config('plus.defaults.frequency', 'weekly'),
                'delivery_days' => $subscription?->delivery_days ?? config('plus.defaults.delivery_days', [6, 2]),
                'start_date' => $subscription?->starts_at?->toDateString() ?? now()->toDateString(),
                'category_ids' => $subscription?->categories?->pluck('id')->values()->all() ?? [],
                'auto_renew' => $subscription?->auto_renew ?? (bool) config('plus.defaults.auto_renew', true),
            ],
            'addresses' => $addresses->map(fn (UserAddress $address) => $this->transformAddress($address))->values()->all(),
            'payment_methods' => $paymentMethods->map(fn (UserPaymentMethod $paymentMethod) => $this->transformPaymentMethod($paymentMethod))->values()->all(),
            'categories' => $categories->map(fn (Category $category) => $this->transformCategory($category))->values()->all(),
            'current_subscription' => $subscription
                ? $this->transformSubscriptionSummary($subscription)
                : null,
        ];
    }

    public function subscribe(User $user, array $payload): array
    {
        $existing = $this->getNormalizedCurrentSubscription($user);

        if ($existing) {
            throw ValidationException::withMessages([
                'subscription' => ['The user already has an active or paused Plus subscription.'],
            ]);
        }

        // Extract values from payload with fallbacks to defaults
        $frequency = $payload['frequency'] ?? config('plus.defaults.frequency', 'weekly');
        $deliveryDays = $payload['delivery_days'] ?? config('plus.defaults.delivery_days', [6, 2]);
        $startDateStr = $payload['start_date'] ?? now()->toDateString();
        $autoRenew = $payload['auto_renew'] ?? (bool) config('plus.defaults.auto_renew', true);
        $categoryIds = $payload['category_ids'] ?? [];
        $notes = $payload['notes'] ?? null;

        // Automatically select default address and payment method if not provided
        $addressId = $payload['address_id'] ?? null;
        if (!$addressId) {
            $addressId = $user->addresses()->where('is_default', true)->first()?->id ?? $user->addresses()->first()?->id;
        }

        $paymentMethodId = $payload['payment_method_id'] ?? null;
        if (!$paymentMethodId) {
            $paymentMethodId = $user->paymentMethods()->where('is_default', true)->first()?->id ?? $user->paymentMethods()->first()?->id;
        }

        // Final check for essential data
        if (!$addressId) {
            throw ValidationException::withMessages(['address_id' => ['Please add a delivery address to your account first.']]);
        }
        if (!$paymentMethodId) {
            throw ValidationException::withMessages(['payment_method_id' => ['Please add a payment method to your account first.']]);
        }

        $startsAt = Carbon::parse($startDateStr)->startOfDay();
        $deliveryDaysArr = array_values(array_unique(array_map('intval', (array) $deliveryDays)));
        sort($deliveryDaysArr);

        $nextDeliveryAt = $this->calculateNextDeliveryAt(
            frequency: $frequency,
            startsAt: $startsAt,
            deliveryDays: $deliveryDaysArr
        );

        $nextBillingAt = $startsAt->copy()->addMonth()->setTime(
            (int) config('plus.defaults.delivery_hour', 8),
            (int) config('plus.defaults.delivery_minute', 0)
        );

        $subscription = DB::transaction(function () use ($user, $frequency, $deliveryDaysArr, $startsAt, $nextDeliveryAt, $nextBillingAt, $addressId, $paymentMethodId, $autoRenew, $notes, $categoryIds) {
            $subscription = $this->plusRepository->createForUser($user, [
                'status' => PlusSubscription::STATUS_ACTIVE,
                'monthly_price' => (float) config('plus.pricing.monthly_price', 50),
                'currency' => config('plus.pricing.currency', 'AED'),
                'frequency' => $frequency,
                'delivery_days' => $deliveryDaysArr,
                'starts_at' => $startsAt->toDateString(),
                'next_delivery_at' => $nextDeliveryAt,
                'next_billing_at' => $nextBillingAt,
                'paused_until' => null,
                'vacation_mode' => false,
                'canceled_at' => null,
                'user_address_id' => $addressId,
                'user_payment_method_id' => $paymentMethodId,
                'auto_renew' => (bool) $autoRenew,
                'notes' => $notes,
                'metadata' => [
                    'source' => 'mobile_api',
                    'category_count' => count($categoryIds),
                ],
            ]);

            return $this->plusRepository->syncCategories(
                $subscription,
                $categoryIds
            );
        });

        return [
            'subscription' => $this->transformDetailedSubscription($subscription),
            'upcoming_deliveries' => $this->buildUpcomingDeliveries($subscription),
        ];
    }

    public function manage(User $user): array
    {
        $subscription = $this->getNormalizedCurrentSubscription($user);

        if (!$subscription) {
            return [
                'is_subscribed' => false,
                'banner' => $this->buildBannerPayload(null),
                'subscription' => null,
                'upcoming_deliveries' => [],
                'actions' => [
                    'primary_text' => 'Shop & Schedule Now',
                    'secondary_text' => null,
                ],
            ];
        }

        return [
            'is_subscribed' => true,
            'banner' => $this->buildBannerPayload($subscription),
            'subscription' => $this->transformDetailedSubscription($subscription),
            'upcoming_deliveries' => $this->buildUpcomingDeliveries($subscription),
            'actions' => [
                'primary_text' => 'Shop & Schedule Now',
                'secondary_text' => 'Manage Subscription',
            ],
        ];
    }

    public function profileSummary(User $user): array
    {
        $subscription = $this->getNormalizedCurrentSubscription($user);

        if (!$subscription) {
            return [
                'is_subscribed' => false,
                'subscription_status' => null,
                'ui_state' => 'landing',
            ];
        }

        $upcomingDeliveries = $this->buildUpcomingDeliveries($subscription);

        return [
            'is_subscribed' => true,
            'subscription_status' => $subscription->status,
            'ui_state' => empty($upcomingDeliveries) ? 'empty_state' : 'manage_orders',
        ];
    }

    public function manageSubscription(User $user): array
    {
        $subscription = $this->getNormalizedCurrentSubscription($user);

        if (!$subscription) {
            throw ValidationException::withMessages([
                'subscription' => ['No active Plus subscription found for this user.'],
            ]);
        }

        return $this->buildManageSubscriptionPayload($subscription);
    }

    public function updateManageSubscription(User $user, array $payload): array
    {
        $subscription = $this->getNormalizedCurrentSubscription($user);

        if (!$subscription) {
            throw ValidationException::withMessages([
                'subscription' => ['No active Plus subscription found for this user.'],
            ]);
        }

        if ($subscription->is_canceled) {
            throw ValidationException::withMessages([
                'subscription' => ['This Plus subscription has already been canceled.'],
            ]);
        }

        $subscription = DB::transaction(function () use ($subscription, $payload) {
            $data = [];

            if (array_key_exists('auto_renew', $payload)) {
                $data['auto_renew'] = (bool) $payload['auto_renew'];
            }

            if (!empty($payload['payment_method_id'])) {
                $data['user_payment_method_id'] = $payload['payment_method_id'];
            }

            $shouldCancel = !empty($payload['cancel_subscription']);

            if (!$shouldCancel && array_key_exists('vacation_mode', $payload)) {
                $vacationMode = (bool) $payload['vacation_mode'];

                if ($vacationMode) {
                    $resumeAt = Carbon::parse($payload['resume_at'])->startOfDay();

                    $this->plusRepository->createSkip($subscription, [
                        'action' => PlusSubscriptionSkip::ACTION_PAUSE,
                        'scheduled_for' => null,
                        'resume_at' => $resumeAt->toDateString(),
                        'reason' => 'Pause from manage subscription',
                        'metadata' => [
                            'source' => 'mobile_api',
                            'entry_point' => 'manage_subscription_update',
                        ],
                    ]);

                    $data['vacation_mode'] = true;
                    $data['status'] = PlusSubscription::STATUS_PAUSED;
                    $data['paused_until'] = $resumeAt->toDateString();
                    $data['next_delivery_at'] = $this->calculateNextEligibleDeliveryFromDate($subscription, $resumeAt);
                } else {
                    $data['vacation_mode'] = false;
                    $data['status'] = PlusSubscription::STATUS_ACTIVE;
                    $data['paused_until'] = null;
                    $data['next_delivery_at'] = $this->calculateNextEligibleDeliveryFromNow($subscription);
                }
            }

            if ($shouldCancel) {
                $data['status'] = PlusSubscription::STATUS_CANCELED;
                $data['canceled_at'] = now();
                $data['auto_renew'] = false;
                $data['vacation_mode'] = false;
                $data['paused_until'] = null;
                $data['next_delivery_at'] = null;
            }

            $updatedSubscription = $this->plusRepository->update($subscription, $data);

            return $this->plusRepository->loadFull($updatedSubscription);
        });

        return [
            'subscription' => $this->transformDetailedSubscription($subscription),
            'manage_subscription' => $this->buildManageSubscriptionPayload($subscription),
        ];
    }

    public function skip(User $user, array $payload): array
    {
        $subscription = $this->getNormalizedCurrentSubscription($user);

        if (!$subscription) {
            throw ValidationException::withMessages([
                'subscription' => ['No active Plus subscription found for this user.'],
            ]);
        }

        if ($subscription->is_canceled) {
            throw ValidationException::withMessages([
                'subscription' => ['This Plus subscription has already been canceled.'],
            ]);
        }

        return DB::transaction(function () use ($subscription, $payload) {
            if ($payload['action'] === 'skip_once') {
                if ($subscription->is_paused) {
                    throw ValidationException::withMessages([
                        'subscription' => ['You cannot skip a delivery while the subscription is paused.'],
                    ]);
                }

                $nextDelivery = $this->resolveRequestedDeliveryDate($subscription, $payload);

                if (!$nextDelivery) {
                    throw ValidationException::withMessages([
                        'delivery' => ['No upcoming delivery found to skip.'],
                    ]);
                }

                $existingSkip = $subscription->skips()
                    ->where('action', PlusSubscriptionSkip::ACTION_SKIP_ONCE)
                    ->whereDate('scheduled_for', $nextDelivery->toDateString())
                    ->exists();

                if ($existingSkip) {
                    throw ValidationException::withMessages([
                        'delivery' => ['The selected delivery has already been skipped.'],
                    ]);
                }

                $this->plusRepository->createSkip($subscription, [
                    'action' => PlusSubscriptionSkip::ACTION_SKIP_ONCE,
                    'scheduled_for' => $nextDelivery->toDateString(),
                    'resume_at' => null,
                    'reason' => $payload['reason'] ?? 'Skip selected delivery',
                    'metadata' => [
                        'source' => 'mobile_api',
                        'delivery_id' => $payload['delivery_id'] ?? null,
                        'order_id' => $payload['order_id'] ?? null,
                    ],
                ]);

                $newNext = $this->calculateNextEligibleDeliveryFromDate(
                    $subscription,
                    $nextDelivery->copy()->addDay()
                );

                $subscription = $this->plusRepository->update($subscription, [
                    'next_delivery_at' => $newNext,
                ]);
            }

            if ($payload['action'] === 'pause') {
                $resumeAt = Carbon::parse($payload['resume_at'])->startOfDay();

                $this->plusRepository->createSkip($subscription, [
                    'action' => PlusSubscriptionSkip::ACTION_PAUSE,
                    'scheduled_for' => null,
                    'resume_at' => $resumeAt->toDateString(),
                    'reason' => $payload['reason'] ?? 'Pause for a while',
                    'metadata' => [
                        'source' => 'mobile_api',
                    ],
                ]);

                $subscription = $this->plusRepository->update($subscription, [
                    'status' => PlusSubscription::STATUS_PAUSED,
                    'vacation_mode' => true,
                    'paused_until' => $resumeAt->toDateString(),
                    'next_delivery_at' => $this->calculateNextEligibleDeliveryFromDate($subscription, $resumeAt),
                ]);
            }

            $subscription = $this->plusRepository->loadFull($subscription);

            return [
                'subscription' => $this->transformDetailedSubscription($subscription),
                'upcoming_deliveries' => $this->buildUpcomingDeliveries($subscription),
            ];
        });
    }

    protected function getNormalizedCurrentSubscription(User $user): ?PlusSubscription
    {
        $subscription = $this->plusRepository->findCurrentForUser($user);

        if (!$subscription) {
            return null;
        }

        $subscription = $this->plusRepository->loadFull($subscription);

        if (
            $subscription->status === PlusSubscription::STATUS_PAUSED &&
            $subscription->paused_until &&
            now()->startOfDay()->gt($subscription->paused_until->copy()->endOfDay())
        ) {
            $subscription = $this->plusRepository->update($subscription, [
                'status' => PlusSubscription::STATUS_ACTIVE,
                'vacation_mode' => false,
                'paused_until' => null,
                'next_delivery_at' => $this->calculateNextEligibleDeliveryFromNow($subscription),
            ]);

            $subscription = $this->plusRepository->loadFull($subscription);
        }

        return $subscription;
    }

    protected function buildManageSubscriptionPayload(PlusSubscription $subscription): array
    {
        return [
            'membership_billing' => [
                'plan_status' => config('plus.manage_subscription.status_labels.' . $subscription->status, ucfirst($subscription->status)),
                'renews_at' => $subscription->next_billing_at?->toDateString(),
                'renews_at_label' => $subscription->next_billing_at?->format('d M Y'),
                'auto_renewal' => (bool) $subscription->auto_renew,
                'payment_method' => $subscription->paymentMethod
                    ? $this->transformPaymentMethod($subscription->paymentMethod)
                    : null,
            ],
            'vacation_mode' => [
                'enabled' => (bool) $subscription->vacation_mode,
                'paused_until' => $subscription->paused_until?->toDateString(),
                'paused_until_label' => $subscription->paused_until?->format('d M Y'),
            ],
            'actions' => [
                'can_cancel' => !$subscription->is_canceled,
            ],
        ];
    }

    protected function buildBannerPayload(?PlusSubscription $subscription): array
    {
        if (!$subscription) {
            return [
                'title' => config('plus.banner.title'),
                'subtitle' => config('plus.banner.subtitle'),
                'renewal_badge' => null,
            ];
        }

        if ($subscription->status === PlusSubscription::STATUS_PAUSED) {
            return [
                'title' => config('plus.banner.paused_title'),
                'subtitle' => config('plus.banner.paused_subtitle'),
                'renewal_badge' => $subscription->paused_until
                    ? 'Resume At: ' . $subscription->paused_until->format('d M Y')
                    : null,
            ];
        }

        return [
            'title' => config('plus.banner.active_title'),
            'subtitle' => config('plus.banner.active_subtitle'),
            'renewal_badge' => $subscription->next_billing_at
                ? 'Next Renewal: ' . $subscription->next_billing_at->format('d M Y')
                : null,
        ];
    }

    protected function transformSubscriptionSummary(PlusSubscription $subscription): array
    {
        return [
            'id' => $subscription->id,
            'status' => $subscription->status,
            'monthly_price' => (float) $subscription->monthly_price,
            'currency' => $subscription->currency,
            'frequency' => $subscription->frequency,
            'frequency_label' => $this->makeFrequencyLabel($subscription->frequency, $subscription->delivery_days ?? []),
            'starts_at' => $subscription->starts_at?->toDateString(),
            'next_delivery_at' => $subscription->next_delivery_at?->toISOString(),
            'next_billing_at' => $subscription->next_billing_at?->toISOString(),
            'paused_until' => $subscription->paused_until?->toDateString(),
            'vacation_mode' => (bool) $subscription->vacation_mode,
            'auto_renew' => (bool) $subscription->auto_renew,
            'selected_categories_count' => $subscription->categories->count(),
        ];
    }

    protected function transformDetailedSubscription(PlusSubscription $subscription): array
    {
        return [
            'id' => $subscription->id,
            'status' => $subscription->status,
            'membership_title' => 'El-Sawadi Plus',
            'monthly_price' => (float) $subscription->monthly_price,
            'currency' => $subscription->currency,
            'frequency' => $subscription->frequency,
            'frequency_label' => $this->makeFrequencyLabel($subscription->frequency, $subscription->delivery_days ?? []),
            'delivery_days' => $subscription->delivery_days ?? [],
            'delivery_day_labels' => $this->mapDeliveryDayLabels($subscription->delivery_days ?? []),
            'starts_at' => $subscription->starts_at?->toDateString(),
            'next_delivery_at' => $subscription->next_delivery_at?->toISOString(),
            'next_billing_at' => $subscription->next_billing_at?->toISOString(),
            'paused_until' => $subscription->paused_until?->toDateString(),
            'vacation_mode' => (bool) $subscription->vacation_mode,
            'auto_renew' => (bool) $subscription->auto_renew,
            'notes' => $subscription->notes,
            'address' => $subscription->address ? $this->transformAddress($subscription->address) : null,
            'payment_method' => $subscription->paymentMethod ? $this->transformPaymentMethod($subscription->paymentMethod) : null,
            'selected_categories' => $subscription->categories->map(fn (Category $category) => $this->transformCategory($category))->values()->all(),
        ];
    }

    protected function transformAddress(UserAddress $address): array
    {
        $parts = array_filter([
            $address->address_line_1,
            $address->address_line_2,
            $address->building ? 'Building ' . $address->building : null,
            $address->floor ? 'Floor ' . $address->floor : null,
            $address->apartment ? 'Apartment ' . $address->apartment : null,
            $address->city,
            $address->country,
            $address->postal_code,
        ]);

        return [
            'id' => $address->id,
            'label' => $address->label ?: 'Address',
            'recipient_name' => $address->recipient_name,
            'phone' => $address->phone,
            'full_address' => implode(', ', $parts),
            'is_default' => (bool) $address->is_default,
        ];
    }

    protected function transformPaymentMethod(UserPaymentMethod $paymentMethod): array
    {
        return [
            'id' => $paymentMethod->id,
            'brand' => $paymentMethod->brand,
            'last_four' => $paymentMethod->last_four,
            'masked_label' => trim($paymentMethod->brand . ' •••• ' . $paymentMethod->last_four),
            'holder_name' => $paymentMethod->holder_name,
            'expiry_month' => $paymentMethod->expiry_month,
            'expiry_year' => $paymentMethod->expiry_year,
            'is_default' => (bool) $paymentMethod->is_default,
        ];
    }

    protected function transformCategory(Category $category): array
    {
        $translation = $category->relationLoaded('translation') ? $category->translation : null;

        if (!$translation && $category->relationLoaded('translations')) {
            $translation = $category->translations
                ->firstWhere('locale', app()->getLocale())
                ?? $category->translations->first();
        }

        $name = data_get($translation, 'name')
            ?? data_get($category, 'name')
            ?? $category->code
            ?? ('Category #' . $category->id);

        $description = data_get($translation, 'description')
            ?? data_get($category, 'description')
            ?? $category->notes;

        $image = data_get($translation, 'image') ?? data_get($category, 'image');

        return [
            'id' => $category->id,
            'name' => $name,
            'description' => $description,
            'image' => $image,
            'initials' => $this->makeInitials($name),
        ];
    }

    protected function buildUpcomingDeliveries(PlusSubscription $subscription): array
    {
        if ($subscription->is_canceled) {
            return [];
        }

        $count = (int) config('plus.defaults.preview_occurrences', 3);
        $deliveries = $this->getUpcomingEligibleDeliveries($subscription, $count);

        return collect($deliveries)
            ->map(fn (Carbon $scheduledFor) => $this->makeOccurrencePayload($subscription, $scheduledFor))
            ->values()
            ->all();
    }

    protected function makeOccurrencePayload(PlusSubscription $subscription, Carbon $scheduledFor): array
    {
        $categories = $subscription->categories
            ->map(fn (Category $category) => $this->transformCategory($category))
            ->values()
            ->all();

        $deliveryIdentifier = $this->makeDeliveryIdentifier($subscription, $scheduledFor);

        return [
            'id' => $deliveryIdentifier,
            'delivery_id' => $deliveryIdentifier,
            'order_id' => $deliveryIdentifier,
            'key' => $deliveryIdentifier,
            'title' => $this->buildOccurrenceTitle($subscription),
            'subtitle' => 'Recurring delivery bundle',
            'schedule_label' => $this->makeFrequencyLabel($subscription->frequency, $subscription->delivery_days ?? []),
            'scheduled_for' => $scheduledFor->toISOString(),
            'scheduled_for_label' => $scheduledFor->format('d M Y, h:i A'),
            'selected_categories' => $categories,
            'can_skip' => !$subscription->is_paused && !$subscription->is_canceled,
            'can_edit_bundle' => true,
        ];
    }

    protected function buildOccurrenceTitle(PlusSubscription $subscription): string
    {
        $names = $subscription->categories
            ->map(function (Category $category) {
                $item = $this->transformCategory($category);
                return $item['name'];
            })
            ->take(2)
            ->values()
            ->all();

        if (count($names) === 0) {
            return 'Your recurring order';
        }

        return implode(', ', $names) . ' recurring order';
    }

    protected function calculateNextDeliveryAt(string $frequency, Carbon $startsAt, array $deliveryDays): Carbon
    {
        $hour = (int) config('plus.defaults.delivery_hour', 8);
        $minute = (int) config('plus.defaults.delivery_minute', 0);

        if ($frequency === 'monthly') {
            return $startsAt->copy()->setTime($hour, $minute);
        }

        $cursor = $startsAt->copy()->startOfDay();

        for ($i = 0; $i < 14; $i++) {
            if (in_array($cursor->dayOfWeek, $deliveryDays, true)) {
                return $cursor->copy()->setTime($hour, $minute);
            }

            $cursor->addDay();
        }

        return $startsAt->copy()->setTime($hour, $minute);
    }

    protected function calculateNextEligibleDeliveryFromNow(PlusSubscription $subscription): ?Carbon
    {
        return $this->calculateNextEligibleDeliveryFromDate($subscription, now()->startOfDay());
    }

    protected function calculateNextEligibleDeliveryFromDate(PlusSubscription $subscription, Carbon $fromDate): ?Carbon
    {
        $hour = (int) config('plus.defaults.delivery_hour', 8);
        $minute = (int) config('plus.defaults.delivery_minute', 0);

        $skipDates = $this->plusRepository->getSkipDates($subscription)->values()->all();
        $startCursor = $fromDate->copy()->startOfDay();

        if ($subscription->status === PlusSubscription::STATUS_PAUSED && $subscription->paused_until) {
            $startCursor = $subscription->paused_until->copy()->startOfDay();
        }

        if ($subscription->frequency === 'monthly') {
            $candidate = $startCursor->copy()->setTime($hour, $minute);

            if ($candidate->lt($fromDate)) {
                $candidate->addMonth();
            }

            return $candidate;
        }

        $deliveryDays = $subscription->delivery_days ?? [];
        $cursor = $startCursor->copy();

        for ($i = 0; $i < 366; $i++) {
            if (
                in_array($cursor->dayOfWeek, $deliveryDays, true) &&
                !in_array($cursor->toDateString(), $skipDates, true)
            ) {
                return $cursor->copy()->setTime($hour, $minute);
            }

            $cursor->addDay();
        }

        return null;
    }

    protected function resolveNextEligibleDeliveryDate(PlusSubscription $subscription): ?Carbon
    {
        if ($subscription->next_delivery_at) {
            return $subscription->next_delivery_at->copy();
        }

        return $this->calculateNextEligibleDeliveryFromNow($subscription);
    }

    protected function resolveRequestedDeliveryDate(PlusSubscription $subscription, array $payload): ?Carbon
    {
        $field = array_key_exists('delivery_id', $payload)
            ? 'delivery_id'
            : (array_key_exists('order_id', $payload) ? 'order_id' : 'delivery_id');

        $requestedIdentifier = $payload['delivery_id'] ?? $payload['order_id'] ?? null;

        if (blank($requestedIdentifier)) {
            return $this->resolveNextEligibleDeliveryDate($subscription);
        }

        if (
            !preg_match('/^plus-delivery-(\d+)-(\d{14})$/', $requestedIdentifier, $matches)
        ) {
            throw ValidationException::withMessages([
                $field => ['The selected ' . $field . ' is invalid.'],
            ]);
        }

        if ((int) $matches[1] !== (int) $subscription->id) {
            throw ValidationException::withMessages([
                $field => ['The selected ' . $field . ' does not belong to this subscription.'],
            ]);
        }

        $allowedDeliveries = collect(
            $this->getUpcomingEligibleDeliveries(
                $subscription,
                max((int) config('plus.defaults.preview_occurrences', 3), 12)
            )
        );

        $matchedDelivery = $allowedDeliveries->first(
            fn (Carbon $delivery) => $this->makeDeliveryIdentifier($subscription, $delivery) === $requestedIdentifier
        );

        if (!$matchedDelivery) {
            throw ValidationException::withMessages([
                $field => ['The selected ' . $field . ' is invalid or no longer available.'],
            ]);
        }

        return $matchedDelivery->copy();
    }

    protected function makeDeliveryIdentifier(PlusSubscription $subscription, Carbon $scheduledFor): string
    {
        return 'plus-delivery-' . $subscription->id . '-' . $scheduledFor->format('YmdHis');
    }

    protected function getUpcomingEligibleDeliveries(PlusSubscription $subscription, int $count = 3): array
    {
        $results = [];
        $hour = (int) config('plus.defaults.delivery_hour', 8);
        $minute = (int) config('plus.defaults.delivery_minute', 0);

        $skipDates = $this->plusRepository->getSkipDates($subscription)->values()->all();

        $cursor = $subscription->next_delivery_at
            ? $subscription->next_delivery_at->copy()->startOfDay()
            : ($subscription->paused_until
                ? $subscription->paused_until->copy()->startOfDay()
                : Carbon::parse($subscription->starts_at)->startOfDay());

        if ($subscription->status === PlusSubscription::STATUS_PAUSED && $subscription->paused_until) {
            $cursor = $subscription->paused_until->copy()->startOfDay();
        }

        if ($subscription->frequency === 'monthly') {
            $candidate = $cursor->copy()->setTime($hour, $minute);

            while (count($results) < $count) {
                if (!in_array($candidate->toDateString(), $skipDates, true)) {
                    $results[] = $candidate->copy();
                }

                $candidate->addMonth();
            }

            return $results;
        }

        $deliveryDays = $subscription->delivery_days ?? [];

        while (count($results) < $count) {
            if (
                in_array($cursor->dayOfWeek, $deliveryDays, true) &&
                !in_array($cursor->toDateString(), $skipDates, true)
            ) {
                $results[] = $cursor->copy()->setTime($hour, $minute);
            }

            $cursor->addDay();
        }

        return $results;
    }

    protected function makeFrequencyLabel(string $frequency, array $deliveryDays): string
    {
        if ($frequency === 'monthly') {
            return 'Monthly';
        }

        $labels = $this->mapDeliveryDayLabels($deliveryDays);

        if (count($labels) === 0) {
            return ucfirst($frequency);
        }

        $prefix = $frequency === 'custom' ? 'Custom' : 'Weekly';

        return $prefix . ': ' . implode(', ', $labels);
    }

    protected function mapDeliveryDayLabels(array $deliveryDays): array
    {
        $weekdays = [
            0 => 'Sun',
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
        ];

        return collect($deliveryDays)
            ->map(fn ($day) => $weekdays[(int) $day] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    protected function makeInitials(string $value): string
    {
        $parts = preg_split('/\s+/', trim($value)) ?: [];
        $parts = array_filter($parts);

        if (count($parts) === 0) {
            return 'C';
        }

        return strtoupper(
            mb_substr($parts[0], 0, 1) .
            mb_substr($parts[1] ?? '', 0, 1)
        );
    }
}
