<?php

namespace App\Observers;

use App\Models\AnimalStatusHistory;
use App\Models\LivestockAnimal;

class LivestockAnimalObserver
{
    public function updated(LivestockAnimal $animal): void
    {
        if (!$animal->wasChanged('status')) {
            return;
        }

        AnimalStatusHistory::withoutGlobalScopes()->create([
            'tenant_id' => $animal->tenant_id,
            'animal_id' => $animal->id,
            'old_status' => $animal->getOriginal('status'),
            'new_status' => $animal->status,
            'change_reason' => request('reason') ?? request('change_reason'),
            'changed_at' => now(),
        ]);
    }
}
