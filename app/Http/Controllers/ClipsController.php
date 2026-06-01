<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClipsController extends Controller
{
    public function index()
    {
        $activities = \App\Models\Status::visible()
            ->where('s_type', 14)
            ->orderBy('date', 'desc')
            ->paginate(10);

        app(\App\Services\StatusActivityService::class)->decorateMany($activities);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('theme::clips.partials.clips_list', compact('activities'))->render(),
                'next_page_url' => $activities->nextPageUrl()
            ]);
        }

        return view('theme::clips.index', compact('activities'));
    }

    public function saved()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $activities = \App\Models\Status::visible()
            ->where('s_type', 14)
            ->whereExists(function ($query) use ($user) {
                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                      ->from('saved_statuses')
                      ->whereColumn('saved_statuses.status_id', 'status.id')
                      ->where('saved_statuses.user_id', $user->id);
            })
            ->orderBy('date', 'desc')
            ->paginate(10);

        app(\App\Services\StatusActivityService::class)->decorateMany($activities);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('theme::clips.partials.clips_grid', compact('activities'))->render(),
                'next_page_url' => $activities->nextPageUrl()
            ]);
        }

        return view('theme::clips.saved', compact('activities'));
    }
}
