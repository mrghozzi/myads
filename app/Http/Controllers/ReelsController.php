<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReelsController extends Controller
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
                'html' => view('theme::reels.partials.reels_list', compact('activities'))->render(),
                'next_page_url' => $activities->nextPageUrl()
            ]);
        }

        return view('theme::reels.index', compact('activities'));
    }

    public function saved()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $activities = \App\Models\Status::visible()
            ->where('s_type', 14)
            ->whereHas('savedReels', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('date', 'desc')
            ->paginate(10);

        app(\App\Services\StatusActivityService::class)->decorateMany($activities);

        if (request()->ajax()) {
            return response()->json([
                'html' => view('theme::reels.partials.reels_grid', compact('activities'))->render(),
                'next_page_url' => $activities->nextPageUrl()
            ]);
        }

        return view('theme::reels.saved', compact('activities'));
    }
}
