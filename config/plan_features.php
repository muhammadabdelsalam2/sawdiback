<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User & Access Management
    |--------------------------------------------------------------------------
    */

    'max_users' => [
        'label' => 'Max Users',
        'type' => 'numeric',
        'default' => 1,
        'description' => 'Maximum number of system users',
    ],

    'roles_management' => [
        'label' => 'Roles & Permissions',
        'type' => 'boolean',
        'default' => false,
        'description' => 'Custom roles and permission management',
    ],

    /*
    |--------------------------------------------------------------------------
    | Farm & Field Management
    |--------------------------------------------------------------------------
    */

    'max_farms' => [
        'label' => 'Max Farms',
        'type' => 'numeric',
        'default' => 1,
        'description' => 'Number of farms allowed',
    ],

    'max_fields' => [
        'label' => 'Max Fields',
        'type' => 'numeric',
        'default' => 5,
        'description' => 'Number of agricultural fields',
    ],

    'crop_management' => [
        'label' => 'Crop Management',
        'type' => 'boolean',
        'default' => true,
        'description' => 'Manage crops and crop cycles',
    ],

    /*
    |--------------------------------------------------------------------------
    | Livestock Management
    |--------------------------------------------------------------------------
    */

    'livestock_management' => [
        'label' => 'Livestock Management',
        'type' => 'boolean',
        'default' => false,
        'description' => 'Manage animals, breeding, and health records',
    ],

    'max_livestock' => [
        'label' => 'Max Livestock',
        'type' => 'numeric',
        'default' => 0,
        'description' => 'Maximum number of animals',
    ],

    /*
    |--------------------------------------------------------------------------
    | Inventory & Warehouse
    |--------------------------------------------------------------------------
    */

    'inventory_management' => [
        'label' => 'Inventory Management',
        'type' => 'boolean',
        'default' => true,
        'description' => 'Manage seeds, fertilizers, tools, and supplies',
    ],

    'warehouse_locations' => [
        'label' => 'Multiple Warehouses',
        'type' => 'boolean',
        'default' => false,
        'description' => 'Support multiple warehouse locations',
    ],

    /*
    |--------------------------------------------------------------------------
    | Financial & Accounting
    |--------------------------------------------------------------------------
    */

    'expense_tracking' => [
        'label' => 'Expense Tracking',
        'type' => 'boolean',
        'default' => true,
        'description' => 'Track farm expenses',
    ],

    'income_tracking' => [
        'label' => 'Income Tracking',
        'type' => 'boolean',
        'default' => true,
        'description' => 'Track sales and income',
    ],

    'advanced_financial_reports' => [
        'label' => 'Advanced Financial Reports',
        'type' => 'boolean',
        'default' => false,
        'description' => 'Profit, loss, and financial analytics',
    ],

    /*
    |--------------------------------------------------------------------------
    | Reports & Analytics
    |--------------------------------------------------------------------------
    */

    'basic_reports' => [
        'label' => 'Basic Reports',
        'type' => 'boolean',
        'default' => true,
        'description' => 'Basic operational reports',
    ],

    'advanced_reports' => [
        'label' => 'Advanced Reports',
        'type' => 'boolean',
        'default' => false,
        'description' => 'Advanced analytics and insights',
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage & Documents
    |--------------------------------------------------------------------------
    */

    'storage_limit' => [
        'label' => 'Storage Limit (GB)',
        'type' => 'numeric',
        'default' => 5,
        'description' => 'Maximum file storage size in GB',
    ],

    'document_management' => [
        'label' => 'Document Management',
        'type' => 'boolean',
        'default' => true,
        'description' => 'Upload and manage documents',
    ],

    /*
    |--------------------------------------------------------------------------
    | Integrations & Automation
    |--------------------------------------------------------------------------
    */

    'api_access' => [
        'label' => 'API Access',
        'type' => 'boolean',
        'default' => false,
        'description' => 'Access system APIs',
    ],

    'iot_integration' => [
        'label' => 'IoT Sensors Integration',
        'type' => 'boolean',
        'default' => false,
        'description' => 'Integrate IoT devices (weather, soil sensors)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Support & Customization
    |--------------------------------------------------------------------------
    */

    'priority_support' => [
        'label' => 'Priority Support',
        'type' => 'boolean',
        'default' => false,
        'description' => '24/7 priority technical support',
    ],

    'custom_domain' => [
        'label' => 'Custom Domain',
        'type' => 'boolean',
        'default' => false,
        'description' => 'Use your own domain',
    ],

];
