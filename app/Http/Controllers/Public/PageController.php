<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    //
    public function terms($locale)
    {
        app()->setLocale($locale);
        return view('public.legal', [
            'title' => __('auth.terms_title'),
            'content' => $locale == 'ar' ? $this->getArabicTerms() : $this->getEnglishTerms()
        ]);
    }

    public function privacy($locale)
    {
        app()->setLocale($locale);
        return view('public.legal', [
            'title' => __('auth.privacy_title'),
            'content' => $locale == 'ar' ? $this->getArabicPrivacy() : $this->getEnglishPrivacy()
        ]);
    }

    private function getEnglishTerms()
    {
        return "<h3>1. Acceptance</h3><p>By using EL-Sawady ERP, you agree to these terms...</p>";
    }

    private function getArabicTerms()
    {
        return "<h3>1. القبول</h3><p>باستخدامك لنظام الصوادي، فإنك توافق على هذه الشروط...</p>";
    }

    private function getEnglishPrivacy()
    {
        return "<h3>Data Collection</h3><p>We value your privacy and only collect necessary business data...</p>";
    }

    private function getArabicPrivacy()
    {
        return "<h3>جمع البيانات</h3><p>نحن نقدر خصوصيتك ونقوم فقط بجمع بيانات العمل الضرورية...</p>";
    }
}
