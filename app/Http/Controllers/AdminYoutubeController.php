<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\YtVideo;
use Illuminate\Http\Request;

class AdminYoutubeController extends Controller
{
    /**
     * Display a listing of all YouTube campaigns.
     */
    public function index()
    {
        $videos = YtVideo::with('user')->orderBy('id', 'desc')->paginate(20);
        return view('admin::youtube.index', compact('videos'));
    }

    /**
     * Update the status of a specific campaign.
     */
    public function update(Request $request, YtVideo $video)
    {
        $request->validate([
            'status' => 'required|in:active,paused,completed,pending',
        ]);

        $video->update(['status' => $request->status]);

        return back()->with('success', __('Campaign status updated.'));
    }

    /**
     * Remove the specified campaign.
     */
    public function destroy(YtVideo $video)
    {
        // Refund logic could be added here if needed, but typically admin deletion is absolute
        $video->delete();

        return back()->with('success', __('Campaign deleted.'));
    }

    /**
     * Display settings page.
     */
    public function settings()
    {
        return view('admin::youtube.settings');
    }

    /**
     * Update settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'yt_min_duration' => 'required|integer|min:15',
            'yt_cost_per_second' => 'required|numeric|min:0.01',
        ]);

        // Using Option model for settings (as per AGENTS.md)
        Option::updateOrCreate(['name' => 'yt_min_duration'], ['o_valuer' => $request->yt_min_duration]);
        Option::updateOrCreate(['name' => 'yt_cost_per_second'], ['o_valuer' => $request->yt_cost_per_second]);

        return back()->with('success', __('Settings updated successfully.'));
    }
}
