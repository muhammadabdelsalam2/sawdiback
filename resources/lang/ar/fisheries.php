<?php

return [
    'titles' => [
        'fisheries_batches' => 'أحواض ودورات الأسماك',
        'batch_details'     => 'تفاصيل دورة الحوض السمكي',
        'add_batch'         => 'إضافة حوض جديد',
        'edit_batch'        => 'تعديل بيانات الحوض',
    ],

    'actions' => [
        'add_batch'        => 'إضافة حوض جديد',
        'edit_batch'       => 'تعديل بيانات الحوض',
        'record_feed'      => 'تسجيل استهلاك العلف',
        'record_mortality' => 'تسجيل نفوق الأسماك',
        'record_harvest'   => 'تسجيل حصاد وتسويق الأسماك',
        'view'             => 'عرض',
        'edit'             => 'تعديل',
        'delete'           => 'حذف',
        'save'             => 'حفظ',
        'cancel'           => 'إلغاء',
        'back'             => 'رجوع',
        'filter'           => 'تصفية',
        'reset'            => 'إلغاء',
        'search'           => 'ابحث باسم الحوض، الكود، أو السلالة...',
    ],

    'fields' => [
        'batch_code'       => 'كود الدفعة',
        'pond_name'        => 'اسم / رقم الحوض',
        'farm_id'          => 'المزرعة',
        'fish_type'        => 'نوع السمك',
        'initial_count'    => 'العدد الأولي للزريعة',
        'current_count'    => 'العدد الحي الحالي',
        'mortality_count'  => 'إجمالي النافق',
        'initial_weight_g' => 'متوسط الوزن الأولي (جرام)',
        'current_weight_g' => 'متوسط الوزن الحالي (جرام)',
        'target_weight_g'  => 'الوزن المستهدف (جرام)',
        'feed_consumed_kg' => 'استهلاك العلف',
        'total_cost'       => 'إجمالي التكلفة',
        'started_at'       => 'تاريخ البدء',
        'harvested_at'     => 'تاريخ الحصاد',
        'status'           => 'الحالة',
        'notes'            => 'الملاحظات',
        'actions'          => 'الإجراءات',
    ],

    'options' => [
        'active'          => 'نشط',
        'harvested'       => 'تم الحصاد',
        'paused'          => 'متوقف مؤقتاً',
        'partial_harvest' => 'حصاد جزئي (تخفيف)',
        'total_harvest'   => 'حصاد كلي (إنهاء الدورة)',
    ],

    'units' => [
        'kg'  => 'كجم',
        'ton' => 'طن',
        'g'   => 'جرام',
    ],

    'empty' => [
        'no_batches' => 'لا توجد أحواض سمكية مسجلة حتى الآن.',
    ],
];
