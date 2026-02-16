<?php

namespace App\Services\Livestock;

use App\Models\AnimalBirth;
use App\Models\AnimalWeightLog;
use App\Models\LivestockAnimal;
use App\Repositories\LivestockAnimalRepository;
use Illuminate\Support\Facades\DB;

class RegisterAnimalService
{
    public function __construct(private readonly LivestockAnimalRepository $animals)
    {
    }

    public function execute(array $data): LivestockAnimal
    {
        return DB::transaction(function () use ($data) {
            $animal = $this->animals->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'tag_number' => $data['tag_number'],
                'species_id' => $data['species_id'],
                'breed_id' => $data['breed_id'] ?? null,
                'gender' => $data['gender'],
                'birth_date' => $data['birth_date'] ?? null,
                'source_type' => $data['source_type'],
                'purchase_date' => $data['purchase_date'] ?? null,
                'purchase_price' => $data['purchase_price'] ?? null,
                'status' => $data['status'] ?? 'active',
                'health_status' => $data['health_status'] ?? 'healthy',
                'mother_id' => $data['mother_id'] ?? null,
                'father_id' => $data['father_id'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            if ($animal->source_type === 'born' && !empty($animal->mother_id) && !empty($data['capture_birth_event'])) {
                AnimalBirth::query()->create([
                    'tenant_id' => $animal->tenant_id,
                    'mother_id' => $animal->mother_id,
                    'reproduction_cycle_id' => $data['reproduction_cycle_id'] ?? null,
                    'birth_date' => $animal->birth_date ?? now()->toDateString(),
                    'complications' => null,
                    'notes' => 'Auto-created from animal onboarding',
                ]);
            }

            if (!empty($data['initial_weight'])) {
                AnimalWeightLog::query()->create([
                    'tenant_id' => $animal->tenant_id,
                    'animal_id' => $animal->id,
                    'recorded_at' => $data['initial_weight_recorded_at'] ?? now(),
                    'weight' => $data['initial_weight'],
                    'notes' => 'Initial onboarding weight',
                ]);
            }

            return $animal->fresh(['species', 'breed', 'mother', 'father']);
        });
    }
}
