<?php

namespace App\Http\Controllers;

use App\Models\YtVideo;
use App\Models\YtViewLog;
use App\Services\PointLedgerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class YoutubeExchangeController extends Controller
{
    /**
     * Display a listing of videos available to watch.
     */
    public function index()
    {
        $userId = Auth::id();

        // Get video IDs watched by user in the last 24 hours
        $watchedVideoIds = YtViewLog::where('user_id', $userId)
            ->where('watched_at', '>=', Carbon::now()->subDay())
            ->pluck('video_id');

        // Fetch available videos
        $videos = YtVideo::where('status', 'active')
            ->where('user_id', '!=', $userId) // Exclude own videos
            ->whereNotIn('id', $watchedVideoIds)
            ->whereRaw('remaining_budget >= reward_points')
            ->inRandomOrder()
            ->paginate(12);

        return view('theme::youtube.exchange', compact('videos'));
    }

    /**
     * Display the video player and start the viewing session.
     */
    public function watch(YtVideo $video)
    {
        $userId = Auth::id();

        // Validate if video is watchable by this user
        if ($video->status !== 'active' || $video->user_id === $userId || $video->remaining_budget < $video->reward_points) {
            return redirect()->route('youtube.exchange.index')->withErrors(['error' => __('This video is currently unavailable.')]);
        }

        $hasWatchedRecently = YtViewLog::where('user_id', $userId)
            ->where('video_id', $video->id)
            ->where('watched_at', '>=', Carbon::now()->subDay())
            ->exists();

        if ($hasWatchedRecently) {
            return redirect()->route('youtube.exchange.index')->withErrors(['error' => __('You have already watched this video today.')]);
        }

        // Generate a secure token to validate the view later
        // Token includes video id, user id, and the precise timestamp the page was loaded
        $timestamp = now()->timestamp;
        $token = Crypt::encryptString(json_encode([
            'video_id' => $video->id,
            'user_id' => $userId,
            'timestamp' => $timestamp
        ]));

        return view('theme::youtube.watch', compact('video', 'token'));
    }

    /**
     * Verify the view and award points.
     */
    public function verify(Request $request, PointLedgerService $pointLedger)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $payload = json_decode(Crypt::decryptString($request->token), true);
            
            if (!$payload || !isset($payload['video_id'], $payload['user_id'], $payload['timestamp'])) {
                return response()->json(['success' => false, 'message' => __('Invalid token payload.')], 400);
            }

            // Verify user
            if ($payload['user_id'] !== Auth::id()) {
                return response()->json(['success' => false, 'message' => __('Token user mismatch.')], 403);
            }

            $video = YtVideo::find($payload['video_id']);
            if (!$video || $video->status !== 'active') {
                return response()->json(['success' => false, 'message' => __('Video unavailable.')], 404);
            }

            // Verify time elapsed
            // current time - load time must be >= required duration
            $timeElapsed = now()->timestamp - $payload['timestamp'];
            
            // Allow a small margin (e.g. 2 seconds) for initial load delays, but strictly enforce the bulk
            if ($timeElapsed < ($video->duration_required - 3)) {
                return response()->json(['success' => false, 'message' => __('View duration not met.')], 400);
            }

            // Check if already watched in the last 24 hours
            $hasWatched = YtViewLog::where('user_id', Auth::id())
                ->where('video_id', $video->id)
                ->where('watched_at', '>=', Carbon::now()->subDay())
                ->exists();

            if ($hasWatched) {
                return response()->json(['success' => false, 'message' => __('Already watched recently.')], 400);
            }

            // Check budget
            if ($video->remaining_budget < $video->reward_points) {
                return response()->json(['success' => false, 'message' => __('Campaign budget depleted.')], 400);
            }

            DB::beginTransaction();

            // Insert log
            YtViewLog::create([
                'user_id' => Auth::id(),
                'video_id' => $video->id,
                'ip_address' => $request->ip(),
                'watched_at' => now(),
            ]);

            // Update budget
            $video->remaining_budget -= $video->reward_points;
            if ($video->remaining_budget < $video->reward_points) {
                $video->status = 'completed';
            }
            $video->save();

            // Award points
            $pointLedger->award(
                Auth::user(),
                $video->reward_points,
                'yt_view_reward',
                'yt_view_reward',
                'yt_video',
                $video->id
            );

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => __('View verified. You have earned :points pts!', ['points' => $video->reward_points]),
                'points' => $video->reward_points
            ]);

        } catch (DecryptException $e) {
            return response()->json(['success' => false, 'message' => __('Invalid or tampered token.')], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => __('An error occurred.')], 500);
        }
    }
}
