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
            'social' => 'Social & Community',
            'tools' => 'Productivity & Tools',
            'content' => 'Content & Media',
            'marketplace' => 'E-Commerce & Orders',
            'bots' => 'Automations & Bots',
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
