<?php

return [
    'titles' => [
        'fisheries_batches' => 'Fish Ponds & Batches',
        'batch_details'     => 'Fish Pond Details',
        'add_batch'         => 'Add New Pond',
        'edit_batch'        => 'Edit Pond Data',
    ],

    'actions' => [
        'add_batch'        => 'Add New Pond',
        'edit_batch'       => 'Edit Pond Data',
        'record_feed'      => 'Record Feed Consumption',
        'record_mortality' => 'Record Fish Mortality',
        'record_harvest'   => 'Record Fish Harvest',
        'view'             => 'View',
        'edit'             => 'Edit',
        'delete'           => 'Delete',
        'save'             => 'Save',
        'cancel'           => 'Cancel',
        'back'             => 'Back',
        'filter'           => 'Filter',
        'reset'            => 'Reset',
        'search'           => 'Search by pond, code, or type...',
    ],

    'fields' => [
        'batch_code'       => 'Batch Code',
        'pond_name'        => 'Pond Name / No.',
        'farm_id'          => 'Farm',
        'fish_type'        => 'Fish Type',
        'initial_count'    => 'Initial Fingerlings Count',
        'current_count'    => 'Current Live Count',
        'mortality_count'  => 'Total Mortality',
        'initial_weight_g' => 'Initial Avg Weight (g)',
        'current_weight_g' => 'Current Avg Weight (g)',
        'target_weight_g'  => 'Target Weight (g)',
        'feed_consumed_kg' => 'Feed Consumed',
        'total_cost'       => 'Total Cost',
        'started_at'       => 'Start Date',
        'harvested_at'     => 'Harvest Date',
        'status'           => 'Status',
        'notes'            => 'Notes',
        'actions'          => 'Actions',
    ],

    'options' => [
        'active'          => 'Active',
        'harvested'       => 'Harvested',
        'paused'          => 'Paused',
        'partial_harvest' => 'Partial Harvest',
        'total_harvest'   => 'Total Harvest',
    ],

    'units' => [
        'kg'  => 'kg',
        'ton' => 'ton',
        'g'   => 'g',
    ],

    'empty' => [
        'no_batches' => 'No fish ponds recorded yet.',
    ],
];
