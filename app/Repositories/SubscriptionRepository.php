<?php
namespace App\Repositories;

use App\Models\Subscription;
use Illuminate\Support\Collection;

class SubscriptionRepository
{
    protected Subscription $model;

    public function __construct(Subscription $subscription)
    {
        $this->model = $subscription;
    }

    public function allByTenant(string $tenantId): Collection
    {
        return $this->model->where('tenant_id', $tenantId)->orderBy('starts_at', 'desc')->get();
    }

    public function find(int $id): ?Subscription
    {
        return $this->model->find($id);
    }

    public function create(array $data): Subscription
    {
        return $this->model->create($data);
    }

    public function update(Subscription $subscription, array $data): bool
    {
        return $subscription->update($data);
    }

    public function delete(Subscription $subscription): bool
    {
        return $subscription->delete();
    }
}
