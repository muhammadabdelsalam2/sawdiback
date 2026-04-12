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

    'review' => [
        'loaded' => 'Review screen data loaded successfully.',
        'submitted' => 'Review submitted successfully.',
        'not_allowed' => 'Only delivered orders can be reviewed.',
        'duplicate' => 'This order was already reviewed.',
    ],
];
