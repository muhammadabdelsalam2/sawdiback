<?php
return [
    'category' => [
        'name' => 'Category',
        'success' => ':resource returned successfully.',
        'created' => ':resource created successfully.',
        'updated' => ':resource updated successfully.',
    ],

    'product' => [
        'success' => 'Products Returned Successfully',
        'message' => 'Product details fetched successfully',
        'best_selling_loaded' => 'Best selling products fetched successfully.',
        'favorite_added' => 'Product added to favorites successfully.',
        'favorite_failed' => 'Failed to add product to favorites.',
        'already_favorite' => 'Product already in favorites.',
        'not_found' => 'Product not found.',
        'favorite_removed' => 'Product removed from favorites successfully.',
        ],

    'cart' => [
        'success' => 'Cart loaded successfully.',
        'updated' => 'Cart updated successfully.',
        'empty' => 'Cart is empty.',
    ],

    'checkout' => [
        'summary' => 'Checkout summary loaded successfully.',
        'placed' => 'Order placed successfully.',
        'address_required' => 'Shipping address is required.',
    ],

    'order' => [
        'details' => 'Order details loaded successfully.',
        'tracking' => 'Order tracking loaded successfully.',
        'not_found' => 'Order not found.',
    ],
    'address' => [
        'not_found' => 'Address not found.',
        'loaded' => 'Address loaded successfully.',
        'default_updated' => 'Default address updated.',
    ],

    'review' => [
        'loaded' => 'Review screen data loaded successfully.',
        'submitted' => 'Review submitted successfully.',
        'not_allowed' => 'Only delivered orders can be reviewed.',
        'duplicate' => 'This order was already reviewed.',
    ],
];
