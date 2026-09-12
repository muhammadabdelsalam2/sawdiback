<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. إضافة الحذف المؤجل والتراجع لجدول المزارع
        Schema::table('farms', function (Blueprint $table) {
            if (!Schema::hasColumn('farms', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // 2. تحديث جدول الحظائر وتوسعة الأنواع وإضافة الحذف المؤجل
        Schema::table('farm_pens', function (Blueprint $table) {
            if (!Schema::hasColumn('farm_pens', 'deleted_at')) {
                $table->softDeletes();
            }
            // تحويل العمود type إلى string لمرونة استيعاب الأنواع الجديدة (ماعز، بقر، دواجن، أسماك، أرانب، أخرى)
            $table->string('type', 50)->default('other')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farm_pens', function (Blueprint $table) {
            if (Schema::hasColumn('farm_pens', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('farms', function (Blueprint $table) {
            if (Schema::hasColumn('farms', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};