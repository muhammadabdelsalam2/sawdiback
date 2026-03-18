<?php

return [
    'pricing' => [
        'monthly_price' => 50.00,
        'currency' => 'AED',
    ],

    'defaults' => [
        'frequency' => 'weekly',
        'delivery_days' => [6, 2], // Saturday, Tuesday
        'auto_renew' => true,
        'preview_occurrences' => 3,
        'delivery_hour' => 8,
        'delivery_minute' => 0,
    ],

    'banner' => [
        'title' => 'Join Al-Sawadi Plus',
        'subtitle' => 'Experience the true taste of comfort and savings.',
        'active_title' => 'Welcome to Al-Sawadi Plus Family',
        'active_subtitle' => 'Membership Active. Enjoy free delivery & 2x points on all scheduled orders.',
        'paused_title' => 'Your Plus Subscription is Paused',
        'paused_subtitle' => 'Your recurring deliveries are temporarily paused until your selected resume date.',
    ],

    'benefits' => [
        [
            'key' => 'free_unlimited_delivery',
            'title' => 'Free Unlimited Delivery',
            'description' => 'Get all your orders delivered at no extra cost, no minimum purchase required.',
            'icon' => 'delivery',
        ],
        [
            'key' => 'price_lock_guarantee',
            'title' => 'Price Lock Guarantee',
            'description' => 'Protect yourself against market inflation with locked-in prices on your favorites.',
            'icon' => 'shield',
        ],
        [
            'key' => 'priority_delivery_slots',
            'title' => 'Priority Delivery Slots',
            'description' => 'Choose your preferred time with exclusive access to premium delivery windows.',
            'icon' => 'star',
        ],
        [
            'key' => 'double_loyalty_points',
            'title' => 'Double Loyalty Points',
            'description' => 'Earn 2x points on every purchase and unlock exclusive rewards faster.',
            'icon' => 'gift',
        ],
    ],

    'how_it_works' => [
        [
            'step' => 1,
            'title' => 'Shop Your Favorites',
            'description' => 'Browse our fresh products and add everything you need to your cart. Choose from dairy, meat, vegetables, and more.',
        ],
        [
            'step' => 2,
            'title' => 'Activate Schedule',
            'description' => 'Turn on recurring checkout to receive your favorites automatically. Adjust frequency anytime.',
        ],
        [
            'step' => 3,
            'title' => 'We Deliver Regularly',
            'description' => 'Sit back and relax. We’ll deliver your order on schedule. Pause or skip deliveries whenever you need.',
        ],
    ],

    'frequency_options' => [
        [
            'value' => 'weekly',
            'label' => 'Weekly',
            'description' => 'Recurring delivery every selected weekday.',
        ],
        [
            'value' => 'monthly',
            'label' => 'Monthly',
            'description' => 'Recurring delivery once every month.',
        ],
        [
            'value' => 'custom',
            'label' => 'Custom',
            'description' => 'Recurring delivery on your custom selected weekdays.',
        ],
    ],

    'manage_subscription' => [
        'status_labels' => [
            'active' => 'Active',
            'paused' => 'Paused',
            'canceled' => 'Canceled',
        ],
    ],
];
