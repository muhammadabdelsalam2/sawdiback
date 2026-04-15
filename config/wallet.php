<?php

return [
    'default_currency' => env('WALLET_DEFAULT_CURRENCY', 'AED'),

    'history_limit' => env('WALLET_HISTORY_LIMIT', 10),

    'points' => [
        'conversion' => [
            'points' => 100,
            'amount' => 1.00,
        ],
        'minimum_redeemable_points' => 100,
        'must_be_multiple_of' => 100,
    ],
];
