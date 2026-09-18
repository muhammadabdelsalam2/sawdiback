<?php

namespace App\Http\Requests\CropsFeed;

use Illuminate\Validation\Rule;

class FeedConsumptionStoreRequest extends BaseCropsFeedRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'feed_type_id' => [
                'required',
                'integer',
                Rule::exists('feed_types', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            // القطاع أو القسم الإنتاجي المستهدف
            'target_section' => [
                'nullable',
                'string',
                Rule::in([
                    'poultry_broiler', // دواجن - لاحم
                    'poultry_layer',   // دواجن - بياض
                    'breeding',        // سلالات وأمهات
                    'goats',           // الماعز
                    'rabbits',         // الأرانب
                    'fish',            // الأسماك
                    'other',           // أخرى
                ]),
            ],
            // الحظيرة المستهدفة (اختياري)
            'pen_id' => [
                'nullable',
                'integer',
                Rule::exists('farm_pens', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            // الحيوان الفردي (اختياري)
            'animal_id' => [
                'nullable',
                'integer',
                Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            // اسم المجموعة أو الدفعة
            'group_name' => [
                'nullable',
                'string',
                'max:255',
                // إذا لم يتم تحديد قطاع أو حظيرة أو حيوان، يجب كتابة اسم للمجموعة
                'required_without_all:target_section,pen_id,animal_id',
            ],
            'consumption_date' => ['required', 'date'],
            'quantity'         => ['required', 'numeric', 'min:0.01'],
            'unit_cost'        => ['nullable', 'numeric', 'min:0'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }
}
