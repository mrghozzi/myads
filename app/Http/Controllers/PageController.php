<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function privacy()
    {
        $this->seo([
            'scope_key' => 'privacy_page',
            'resource_title' => __('messages.privacy_policy'),
            'description' => __('messages.seo_privacy_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.privacy_policy'), 'url' => route('privacy')],
            ],
        ]);

        return view('theme::pages.privacy');
    }

    public function terms()
    {
        $this->seo([
            'scope_key' => 'terms_page',
            'resource_title' => __('messages.terms_conditions'),
            'description' => __('messages.seo_terms_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.terms_conditions'), 'url' => route('terms')],
            ],
        ]);

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

        $this->seo([
            'scope_key' => 'page_show',
            'content_type' => 'page',
            'content_id' => $page->id,
            'resource_title' => $page->title,
            'description' => $page->meta_description ?: Str::limit(strip_tags((string) $page->content), 170),
            'keywords' => $page->meta_keywords,
            'lastmod' => $page->updated_at,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => $page->title, 'url' => route('page.show', $page->slug)],
            ],
        ]);

        return view('theme::pages.show', compact('page'));
    }
}
