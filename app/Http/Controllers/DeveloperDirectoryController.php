<?php

namespace App\Http\Controllers;

use App\Models\DeveloperApp;
use App\Services\DeveloperScopeCatalog;
use Illuminate\Http\Request;

class DeveloperDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DeveloperApp::where('status', 'active')
            ->where('public_listed', true)
            ->with('user');

        if ($request->filled('q')) {
            $search = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $apps = $query->orderBy('id', 'desc')->paginate(12);

        $categories = [
            'social' => __('messages.dev_cat_social'),
            'tools' => __('messages.dev_cat_tools'),
            'content' => __('messages.dev_cat_content'),
            'marketplace' => __('messages.dev_cat_marketplace'),
            'bots' => __('messages.dev_cat_bots'),
        ];

        return view('theme::developer.directory.index', compact('apps', 'categories'));
    }

    public function show(DeveloperApp $app)
    {
        if ($app->status !== 'active') {
            abort(404);
        }

        $scopes = DeveloperScopeCatalog::getScopes((array) ($app->requested_scopes ?? []));

        return view('theme::developer.directory.show', compact('app', 'scopes'));
    }
}
