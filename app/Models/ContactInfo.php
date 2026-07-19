<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'email',
        'address',
        'working_hours',
        'description',
        'whatsapp_url',
        'facebook_url',
        'instagram_url',
        'x_url',
    ];

    protected $casts = [
        'address' => 'array',
        'working_hours' => 'array',
        'description' => 'array',
    ];

    public static function singleton(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    public static function defaults(): array
    {
        return [
            'phone' => '+971 000 000 000',
            'email' => 'info@sawady.ae',
            'address' => ['ar' => 'الإمارات العربية المتحدة', 'en' => 'United Arab Emirates'],
            'working_hours' => ['ar' => 'يوميًا من 8 صباحًا إلى 6 مساءً', 'en' => 'Daily, 8 AM to 6 PM'],
            'description' => [
                'ar' => 'تواصل معنا للاستفسار عن المنتجات المتاحة وطلبات التوريد.',
                'en' => 'Contact us for product availability and supply requests.',
            ],
        ];
    }

    public function localized(string $field): ?string
    {
        $value = $this->{$field};
        if (!is_array($value)) {
            return $value;
        }

        $locale = substr(app()->getLocale(), 0, 2);

        return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? null;
    }
}
