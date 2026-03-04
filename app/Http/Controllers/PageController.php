<?php

namespace App\Http\Controllers;

use App\Models\Page;

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

    /**
     * Display a dynamic page by its slug.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('theme::pages.show', compact('page'));
    }
}
