<?php

namespace App\Http\Controllers;

use App\Models\YtVideo;
use App\Services\PointLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class YoutubeAdvertiserController extends Controller
{
    /**
     * Display a listing of the advertiser's campaigns.
     */
    public function index()
    {
        $videos = YtVideo::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(10);
        return view('theme::youtube.advertiser', compact('videos'));
    }

    /**
     * Store a newly created YouTube campaign.
     */
    public function store(Request $request, PointLedgerService $pointLedger)
    {
        $request->validate([
            'youtube_url' => 'required|url',
            'duration_required' => 'required|integer|min:15|max:600', // e.g. 15s to 10 mins
            'total_budget' => 'required|numeric|min:1',
            'reward_points' => 'required|numeric|min:0.01',
        ]);

        // Ensure reward doesn't exceed total budget initially
        if ($request->reward_points > $request->total_budget) {
            return back()->withErrors(['reward_points' => __('Reward points cannot exceed total budget.')])->withInput();
        }

        // Extract YouTube ID
        $url = $request->youtube_url;
        $youtubeId = null;
        if (preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            $youtubeId = $matches[1];
        } else {
            return back()->withErrors(['youtube_url' => __('Invalid YouTube URL.')])->withInput();
        }

        $user = Auth::user();

        // Check if user has enough points
        if ($user->pts < $request->total_budget) {
            return back()->withErrors(['total_budget' => __('You do not have enough points.')])->withInput();
        }

        try {
            DB::beginTransaction();

            // Deduct points
            $pointLedger->award(
                $user,
                -$request->total_budget,
                'yt_campaign_creation',
                'yt_campaign_creation',
                'yt_video'
            );

            // Create Video
            $video = YtVideo::create([
                'user_id' => $user->id,
                'youtube_id' => $youtubeId,
                'duration_required' => $request->duration_required,
                'reward_points' => $request->reward_points,
                'total_budget' => $request->total_budget,
                'remaining_budget' => $request->total_budget,
                'status' => 'active',
                'thumbnail_url' => "https://img.youtube.com/vi/{$youtubeId}/0.jpg",
                'title' => 'YouTube Video ' . $youtubeId, // In a real scenario, you'd fetch the title via API or user input
            ]);

            DB::commit();

            return redirect()->back()->with('success', __('Campaign created successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => __('An error occurred while creating the campaign.')])->withInput();
        }
    }

    /**
     * Pause the specified campaign.
     */
    public function pause(YtVideo $video)
    {
        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        $video->update(['status' => 'paused']);
        return back()->with('success', __('Campaign paused.'));
    }

    /**
     * Resume the specified campaign.
     */
    public function resume(YtVideo $video)
    {
        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        if ($video->remaining_budget < $video->reward_points) {
            return back()->withErrors(['error' => __('Insufficient budget to resume.')]);
        }

        $video->update(['status' => 'active']);
        return back()->with('success', __('Campaign resumed.'));
    }
}
