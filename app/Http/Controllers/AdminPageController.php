<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPageController extends Controller
{
    /**
     * Display a listing of all pages.
     */
    public function index()
    {
        $pages = Page::orderBy('order', 'asc')->orderBy('id', 'desc')->get();
        return view('theme::admin.pages', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        return view('theme::admin.pages_form', [
            'mode' => 'create',
            'page' => null,
        ]);
    }

    /**
     * Store a newly created page.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug|regex:/^[a-z0-9\-]+$/',
            'content' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'widget_left' => 'nullable|boolean',
            'widget_right' => 'nullable|boolean',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
        ]);

        Page::create([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'content' => $request->input('content', ''),
            'status' => $request->input('status', 'published'),
            'widget_left' => $request->has('widget_left') ? 1 : 0,
            'widget_right' => $request->has('widget_right') ? 1 : 0,
            'meta_description' => $request->input('meta_description'),
            'meta_keywords' => $request->input('meta_keywords'),
            'order' => $request->input('order', 0),
        ]);

        return redirect()->route('admin.pages')->with('success', __('messages.page_created'));
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit($id)
    {
        $page = Page::findOrFail($id);

        // Get widgets assigned to this page
        $leftWidgets = Option::where('o_type', 'box_widget')
            ->where('o_parent', $page->getLeftPlaceId())
            ->orderBy('o_order', 'asc')
            ->get();

        $rightWidgets = Option::where('o_type', 'box_widget')
            ->where('o_parent', $page->getRightPlaceId())
            ->orderBy('o_order', 'asc')
            ->get();

        return view('theme::admin.pages_form', [
            'mode' => 'edit',
            'page' => $page,
            'leftWidgets' => $leftWidgets,
            'rightWidgets' => $rightWidgets,
        ]);
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|regex:/^[a-z0-9\-]+$/|unique:pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'status' => 'required|in:published,draft',
            'widget_left' => 'nullable|boolean',
            'widget_right' => 'nullable|boolean',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
        ]);

        $page->update([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'content' => $request->input('content', ''),
            'status' => $request->input('status', 'published'),
            'widget_left' => $request->has('widget_left') ? 1 : 0,
            'widget_right' => $request->has('widget_right') ? 1 : 0,
            'meta_description' => $request->input('meta_description'),
            'meta_keywords' => $request->input('meta_keywords'),
            'order' => $request->input('order', 0),
        ]);

        return redirect()->route('admin.pages')->with('success', __('messages.page_updated'));
    }

    /**
     * Remove the specified page and its associated widgets.
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);

        // Delete all widgets assigned to this page's places
        Option::where('o_type', 'box_widget')
            ->whereIn('o_parent', [$page->getLeftPlaceId(), $page->getRightPlaceId()])
            ->delete();

        $page->delete();

        return redirect()->route('admin.pages')->with('success', __('messages.page_deleted'));
    }

    /**
     * Generate a slug from a title via AJAX.
     */
    public function generateSlug(Request $request)
    {
        $title = $request->input('title', '');
        $slug = Str::slug($title);

        // Ensure uniqueness
        $original = $slug;
        $count = 1;
        while (Page::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count;
            $count++;
        }

        return response()->json(['slug' => $slug]);
    }
}
