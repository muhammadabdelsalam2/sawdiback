<?php

namespace App\Services\Livestock;

use App\Models\AnimalBirth;
use App\Models\BirthOffspring;
use App\Models\LivestockAnimal;
use App\Models\ReproductionCycle;
use App\Repositories\ReproductionCycleRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManageReproductionCycleService
{
    public function __construct(private readonly ReproductionCycleRepository $cycles)
    {
    }

    public function openCycle(array $data): ReproductionCycle
    {
        return $this->cycles->create([
            'tenant_id' => $data['tenant_id'] ?? null,
            'female_animal_id' => $data['female_animal_id'],
            'heat_date' => $data['heat_date'] ?? null,
            'insemination_date' => null,
            'insemination_type' => $data['insemination_type'] ?? 'natural',
            'male_animal_id' => null,
            'pregnancy_confirmed' => false,
            'pregnancy_check_date' => null,
            'expected_delivery_date' => null,
            'status' => 'open',
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function recordInsemination(ReproductionCycle $cycle, array $data): ReproductionCycle
    {
        $this->assertCurrentState($cycle, ['open'], 'Insemination can only be recorded for open cycles.');

        return $this->cycles->update($cycle, [
            'insemination_date' => $data['insemination_date'],
            'insemination_type' => $data['insemination_type'],
            'male_animal_id' => $data['male_animal_id'] ?? null,
            'notes' => $data['notes'] ?? $cycle->notes,
        ]);
    }

    public function recordPregnancyCheck(ReproductionCycle $cycle, array $data): ReproductionCycle
    {
        $this->assertCurrentState($cycle, ['open'], 'Pregnancy check can only be recorded for open cycles.');

        $confirmed = (bool) $data['pregnancy_confirmed'];

        return $this->cycles->update($cycle, [
            'pregnancy_confirmed' => $confirmed,
            'pregnancy_check_date' => $data['pregnancy_check_date'],
            'expected_delivery_date' => $confirmed ? ($data['expected_delivery_date'] ?? null) : null,
            'status' => $confirmed ? 'pregnant' : 'failed',
            'notes' => $data['notes'] ?? $cycle->notes,
        ]);
    }

    public function recordBirth(ReproductionCycle $cycle, array $data): ReproductionCycle
    {
        $this->assertCurrentState($cycle, ['pregnant'], 'Birth can only be recorded for pregnant cycles.');

        DB::transaction(function () use ($cycle, $data) {
            $birth = AnimalBirth::query()->create([
                'tenant_id' => $data['tenant_id'] ?? $cycle->tenant_id,
                'mother_id' => $cycle->female_animal_id,
                'reproduction_cycle_id' => $cycle->id,
                'birth_date' => $data['birth_date'],
                'complications' => $data['complications'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['offspring'] as $offspring) {
                $animal = LivestockAnimal::query()->create([
                    'tenant_id' => $data['tenant_id'] ?? $cycle->tenant_id,
                    'tag_number' => $offspring['tag_number'],
                    'species_id' => $offspring['species_id'],
                    'breed_id' => $offspring['breed_id'] ?? null,
                    'gender' => $offspring['gender'],
                    'birth_date' => $data['birth_date'],
                    'source_type' => 'born',
                    'purchase_date' => null,
                    'purchase_price' => null,
                    'status' => 'active',
                    'health_status' => 'healthy',
                    'mother_id' => $cycle->female_animal_id,
                    'father_id' => $cycle->male_animal_id,
                    'notes' => $offspring['notes'] ?? null,
                ]);

                BirthOffspring::query()->create([
                    'tenant_id' => $data['tenant_id'] ?? $cycle->tenant_id,
                    'birth_id' => $birth->id,
                    'offspring_animal_id' => $animal->id,
                    'birth_weight' => $offspring['birth_weight'] ?? null,
                    'notes' => $offspring['notes'] ?? null,
                ]);
            }

            $this->cycles->update($cycle, [
                'status' => 'delivered',
                'notes' => $data['notes'] ?? $cycle->notes,
            ]);
        });

        return $cycle->fresh(['femaleAnimal', 'maleAnimal', 'birth.offspring.offspringAnimal']);
    }

    protected function assertCurrentState(ReproductionCycle $cycle, array $allowed, string $message): void
    {
        if (!in_array($cycle->status, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => $message,
            ]);
        }
    }
}
