<?php

namespace App\Services;

use App\Models\Feature;
use App\Models\Plan;
use App\Repositories\Contracts\PlanRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
class PlanService
{
    protected PlanRepositoryInterface $planRepository;

    public function __construct(PlanRepositoryInterface $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    /**
     * Create a new plan with features JSON
     */
    public function createPlan(array $data): Plan
    {
        // تحقق من features بناء على config
        $featuresInput = $data['features'] ?? [];
        $data['features'] = $this->resolvedFeatures($featuresInput);

        return $this->planRepository->create($data);
    }
    /**
     * Get paginated plans
     */
    public function getPlans(int $perPage = 15)
    {
        return $this->planRepository->paginate($perPage);
    }

    /**
     * Get a plan by id
     */
    public function getPlan(int $id)
    {
        return $this->planRepository->findById($id);
    }



    /**
     * Update plan with new features JSON
     */
    public function updatePlan(Plan $plan, array $data): Plan
    {
        if (isset($data['feature_keys'])) {
            $data['features'] = $this->buildFeaturesJson($data['feature_keys']);
        }

        return $this->planRepository->update($plan, $data);
    }

    /**
     * Delete a plan
     */
    public function deletePlan(int $id): bool
    {
        return $this->planRepository->delete($id);
    }

    /**
     * Resolve features with defaults and DB values
     */
    public function resolvedFeatures(array $featuresJson): array
    {
        $definitions = config('plan_features');
        $values = $featuresJson ?? [];

        $resolved = [];

        foreach ($definitions as $key => $def) {
            $resolved[$key] = [
                'label' => $def['label'] ?? $key,
                'type' => $def['type'] ?? 'boolean',
                'enabled' => $values[$key]['enabled'] ?? false,
                'value' => $values[$key]['value'] ?? $def['default'] ?? null,
            ];
        }

        return $resolved;
    }

    /**
     * Build features JSON from array of keys
     */
    protected function buildFeaturesJson(array $keys): array
    {
        $definitions = config('plan_features');
        $features = [];

        foreach ($keys as $key) {
            if (!isset($definitions[$key]))
                continue;

            $def = $definitions[$key];

            $features[$key] = [
                'value' => $def['default'] ?? null,
                'enabled' => true
            ];
        }

        return $features;
    }

    /**
     * Update plan features based on input and validate types.
     *
     * @param Plan $plan
     * @param array $featuresInput
     * @return Plan
     *
     * @throws ValidationException
     */
    public function updateFeatures(Plan $plan, array $featuresInput): Plan
    {
        if ($this->usesDatabaseFeatureIds($featuresInput)) {
            return $this->updateDatabaseFeatures($plan, $featuresInput);
        }

        $resolvedFeatures = [];
        $definitions = config('plan_features');

        foreach ($featuresInput as $key => $payload) {
            $def = $definitions[$key] ?? null;

            if (!$def) {
                continue; // تجاهل أي feature غير معرفة
            }

            $value = $payload['value'] ?? $def['default'] ?? null;

            // تحقق من النوع
            if ($def['type'] === 'boolean') {
                if (!in_array($value, [0, 1, true, false, '0', '1', null], true)) {
                    throw ValidationException::withMessages([
                        'features.' . $key => __('plan.messages.feature_type_mismatch', [
                            'key' => $key,
                            'type' => __('plan.types.boolean'),
                        ]),
                    ]);
                }
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } elseif ($def['type'] === 'numeric') {
                if (!is_numeric($value)) {
                    throw ValidationException::withMessages([
                        'features.' . $key => __('plan.messages.feature_type_mismatch', [
                            'key' => $key,
                            'type' => __('plan.types.numeric'),
                        ]),
                    ]);
                }
                $value = $value + 0;
            }

            $resolvedFeatures[$key] = [
                'enabled' => (bool) ($payload['enabled'] ?? false),
                'value' => $value,
            ];
        }

        $plan->features = $resolvedFeatures;
        $plan->save();

        return $plan;
    }

    protected function usesDatabaseFeatureIds(array $featuresInput): bool
    {
        if ($featuresInput === []) {
            return false;
        }

        foreach (array_keys($featuresInput) as $key) {
            if (!is_numeric($key)) {
                return false;
            }
        }

        return true;
    }

    protected function updateDatabaseFeatures(Plan $plan, array $featuresInput): Plan
    {
        $featureIds = array_map('intval', array_keys($featuresInput));
        $features = Feature::query()
            ->whereIn('id', $featureIds)
            ->get()
            ->keyBy('id');

        $sync = [];
        $featuresJson = [];

        foreach ($featuresInput as $featureId => $payload) {
            $feature = $features->get((int) $featureId);

            if (!$feature) {
                continue;
            }

            $value = $payload['value'] ?? null;

            if ($feature->type === 'boolean') {
                $value = $value === '' ? null : $value;
            } elseif ($feature->type === 'number' && $value !== null && $value !== '' && !is_numeric($value)) {
                throw ValidationException::withMessages([
                    'features.' . $featureId => __('plan.messages.feature_type_mismatch', [
                        'key' => $feature->key,
                        'type' => __('plan.types.numeric'),
                    ]),
                ]);
            }

            $enabled = (bool) ($payload['enabled'] ?? false);

            $sync[$feature->id] = [
                'enabled' => $enabled,
                'value' => $value === '' ? null : $value,
            ];

            $featuresJson[$feature->key] = [
                'enabled' => $enabled,
                'value' => $value === '' ? null : $value,
            ];
        }

        $plan->featureDefinitions()->sync($sync);
        $plan->features = $featuresJson;
        $plan->save();

        return $plan;
    }

}
