<?php

use App\Models\AnimalSpecies;
use App\Models\AnimalBreed;
use App\Models\Vaccine;
use App\Models\FeedType;
use App\Models\Category;

$speciesTranslations = [
    'CATTLE' => ['en' => 'Cattle', 'ar' => 'أبقار'],
    'SHEEP'  => ['en' => 'Sheep', 'ar' => 'أغنام'],
    'GOAT'   => ['en' => 'Goat', 'ar' => 'ماعز'],
    'CAMEL'  => ['en' => 'Camel', 'ar' => 'إبل'],
    'HORSE'  => ['en' => 'Horse', 'ar' => 'خيل'],
];

foreach ($speciesTranslations as $code => $trans) {
    AnimalSpecies::where('code', $code)->update(['name_translations' => $trans]);
}

$feedTranslations = [
    'concentrate' => ['en' => 'Concentrate', 'ar' => 'مركز'],
    'roughage'    => ['en' => 'Roughage', 'ar' => 'خشن'],
    'supplement'  => ['en' => 'Supplement', 'ar' => 'مكمل'],
];

// For FeedType we might need to match by name or add a code later
// For now let's just do a basic one
FeedType::all()->each(function($ft) {
    if (!$ft->name_translations) {
        $ft->update(['name_translations' => ['en' => $ft->name, 'ar' => $ft->name]]);
    }
});

// Breeds
AnimalBreed::all()->each(function($b) {
    if (!$b->name_translations) {
        $b->update(['name_translations' => ['en' => $b->name, 'ar' => $b->name]]);
    }
});

echo "Translations updated.\n";
