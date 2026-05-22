<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReelsController extends Controller
{
    public function index()
    {
        return view('theme::reels.index');
    }

    public function saved()
    {
        return view('theme::reels.saved');
    }
}
