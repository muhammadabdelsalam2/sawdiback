<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    // Show the landing page
    public function index()
    {
        return view('landing.index'); // Blade file
    }
}
