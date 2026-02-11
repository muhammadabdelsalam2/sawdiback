<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        // يمكنك الوصول للـ locale الحالي من session أو route
        $locale = session('locale_full', 'en-SA');

        return view('dashboard.index', compact('locale'));
    }
}
