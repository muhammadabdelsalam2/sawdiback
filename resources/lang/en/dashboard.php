<?php

return [
    'sidebar' => [
        'dashboard' => 'Dashboard',
        'livestock' => 'Livestock Management',
        'animal_registry' => 'Animal Registry',
        'health_vax' => 'Health & Vax',
        'breeding_cycles' => 'Breeding Cycles',
        'production' => 'Production',
        'crops_feed' => 'Crops & Feed',
        'inventory' => 'Inventory',
        'sales_distribution' => 'Sales & Distribution',
        'procurement' => 'Procurement',
        'finance' => 'Finance',
        'hr_management' => 'HR Management',
        'maintenance' => 'Maintenance',
        'system_settings' => 'System Settings',
        'subscriptions' => 'Subscriptions ',
        'plans' => 'Plans',
        'features' => 'Features',

        // ✅ Added for Settings submenu (MVP)
        'countries' => 'Countries',
        'cities' => 'Cities',
        'theme' => 'Theme',

        'logout' => 'Logout',

        // System Settings
        'settings' => [
            'permissionsManagement' => 'Permissions Management'
        ],
    ],

    'overview_title' => 'Dashboard Overview',
    'overview_desc' => 'Al-Sawadi Farm Management System',
    'last_30_days' => 'Last 30 Days',

    'stats' => [
        'total_livestock' => 'Total Livestock',
        'heads' => 'Heads',
        'new_born' => 'New Born',
        'daily_milk_yield' => 'Daily Milk Yield',
        'liters' => 'Liters',
        'vs_last_month' => 'Vs. Last Month',
        'feed_inventory' => 'Feed Inventory',
        'tons' => 'Tons',
        'low_stock_alert' => 'Low Stock Alert',
        'average_profit_daily' => 'Average Profit (Daily)',
        'aed' => 'AED',
    ],

    'charts' => [
        'daily_production_performance' => 'Daily Production Performance',
        'actual_vs_target' => 'Actual Yield vs. Daily Target (Liters)',
        'herd_composition_status' => 'Herd Composition Status',
        'distribution_by_stage' => 'Distribution by lifecycle stage',
        'profitability_cost_center' => 'Profitability by Cost Center',
        'net_profit_margin' => 'Net profit & margin analysis across departments',
    ],

    'alerts' => [
        'critical_alerts' => 'Critical Alerts',
        'urgent_health_stock' => 'Urgent health & stock issues',
        'low_feed_stock' => 'Low Feed Stock (Corn Mix)',
        'warehouse_c_only_2_tons' => 'Warehouse C • Only 2 Tons left',
        'vaccination_overdue' => 'Vaccination Overdue (FMD)',
        'group_b_3_days_late' => 'Group B (Heifers) • 3 Days late',
        'machinery_maintenance' => 'Machinery Maintenance D...',
        'tractor_oil_filter_change' => 'Tractor T-5 • Oil & Filter Change',
        'high_scc_detected' => 'High SCC Detected',
        'tank2_quality_risk' => 'Tank 2 • Quality Risk (>400k)',
        'urgent' => 'Urgent',
        'warning' => 'Warning',
        'active_sales_orders' => 'Active Sales Orders',
        'pending_approvals_processing' => 'Pending approvals & processing',
        'todays_operations' => "Today's Operations",
        'visits_maintenance_logistics' => 'Visits, Maintenance & Logistics',
    ],

    'buttons' => [
        'view_all' => 'View All',
    ],

    'last_updated' => 'Last Updated',
    // Empty State / No Plan
    'no_plan_title' => 'You don’t have an active plan',
    'no_plan_desc' => 'Please subscribe to one of our plans to access your dashboard and manage your livestock efficiently.',
    'go_to_subscription' => 'Go to Subscription',
    'no_plan_features_title' => 'With a subscription you can:',
    'no_plan_feature_1' => 'Track your livestock daily',
    'no_plan_feature_2' => 'Monitor milk production and feed',
    'no_plan_feature_3' => 'Get critical alerts and notifications',
    'no_plan_feature_4' => 'Generate reports and insights',

    // Dashboard stats (example reuse)


];
