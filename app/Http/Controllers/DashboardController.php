<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class DashboardController extends Controller
{
   
    public function index(Request $request)
    {
        $locale = session('locale_full', 'en-SA');

        return view('dashboard.index', compact('locale'));
    }

    public function superAdminIndex(Request $request)
    {
        $locale = session('locale_full', 'en-SA');

        return view('dashboard.superadmin', compact('locale'));
    }

    public function accessManagement(Request $request)
    {
        $locale = session('locale_full', 'en-SA');

        return view('dashboard.access-management', compact('locale'));
    }
}
