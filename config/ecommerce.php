<?php

return [
    'currency' => 'AED',
    'vat_rate' => 0.05,
    'shipping_fee' => 15.00,
    'estimated_delivery_days' => 3,
    'review' => [
        'min_rating' => 1,
        'max_rating' => 5,
        'max_images' => 5,
        'reasons' => [
            1 => ['Late delivery', 'Wrong items', 'Poor packaging', 'Damaged products'],
            2 => ['Delivery delay', 'Quality issues', 'Missing items'],
            3 => ['Average experience', 'Could be better', 'Neutral'],
            4 => ['Good quality', 'On-time delivery', 'Nice packaging'],
            5 => ['Excellent quality', 'Fast delivery', 'Great service'],
        ],
    ],
    'support' => [
        'phone' => '+971-000-000-000',
        'email' => 'support@elsawady.com',
        'whatsapp' => '+971-000-000-000',
    ],
    'suggested_products_limit' => 6,
];
