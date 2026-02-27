<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function privacy()
    {
        return view('theme::pages.privacy');
    }

    public function terms()
    {
        return view('theme::pages.terms');
    }
}
