<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Status;
use App\Http\Resources\StatusResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClipsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'latest');
        if (!in_array($tab, ['latest', 'trending', 'following'], true)) {
            $tab = 'latest';
        }

        $directStatusId = $request->query('clip') ?: $request->query('clip_id');

        $query = Status::with(['user', 'repostRecord', 'linkPreviewRecord', 'forumTopic'])
            ->where('s_type', Status::TYPE_CLIPS)
            ->where('statu', 1)
            ->privacyVisible();

        // Targeted clip prioritization (Deep Linking)
        if ($directStatusId) {
            $query->orderByRaw('CASE WHEN status.id = ? THEN 0 ELSE 1 END', [(int) $directStatusId]);
        }

        // Apply Tab Filters
        if ($tab === 'trending') {
            $query->orderByRaw('(
                COALESCE((SELECT vu FROM forum WHERE forum.id = status.tp_id), 0) +
                COALESCE((SELECT reply FROM forum WHERE forum.id = status.tp_id), 0) * 5
            ) DESC, status.date DESC');
        } elseif ($tab === 'following') {
            $user = Auth::user();
            if ($user) {
                $followingIds = Like::where('uid', $user->id)
                    ->where('type', 1)
                    ->pluck('sid');
                $query->whereIn('status.uid', $followingIds);
            } else {
                $query->whereRaw('1 = 0');
            }
            $query->orderBy('status.date', 'desc');
        } else {
            // Latest (default)
            $query->orderBy('status.date', 'desc');
        }

        $perPage = (int) $request->input('per_page', 10);
        $clips = $query->paginate($perPage);

        return StatusResource::collection($clips);
    }

    public function show($status)
    {
        $clip = Status::with(['user', 'repostRecord', 'linkPreviewRecord', 'forumTopic'])
            ->where('s_type', Status::TYPE_CLIPS)
            ->where('statu', 1)
            ->privacyVisible()
            ->where('id', (int) $status)
            ->first();

        if (!$clip) {
            return response()->json(['message' => 'Clip not found'], 404);
        }

        return new StatusResource($clip);
    }

    public function recordView(Request $request)
    {
        $statusId = $request->input('status_id') ?: $request->input('id');
        if (!$statusId) {
            return response()->json(['error' => 'Invalid status ID'], 400);
        }

        $status = Status::where('s_type', Status::TYPE_CLIPS)
            ->where('statu', 1)
            ->privacyVisible()
            ->where('id', (int) $statusId)
            ->first();

        if (!$status) {
            return response()->json(['error' => 'Clip not found'], 404);
        }

        $sessionKey = 'clip_viewed_' . $status->id;
        $userId = Auth::id() ?? $request->ip();
        $cacheKey = 'clip_viewed_' . $status->id . '_' . md5((string) $userId);

        $alreadyViewed = session()->has($sessionKey) || cache()->has($cacheKey);

        if (!$alreadyViewed) {
            session()->put($sessionKey, true);
            cache()->put($cacheKey, true, now()->addHours(6));
            if ($status->tp_id) {
                ForumTopic::where('id', $status->tp_id)->increment('vu');
            }
        }

        $currentViews = $status->tp_id ? (int) ForumTopic::where('id', $status->tp_id)->value('vu') : 0;

        return response()->json([
            'success' => true,
            'status_id' => $status->id,
            'views_count' => $currentViews,
        ]);
    }

    public function saved(Request $request)
    {
        $user = Auth::user();

        $savedStatusIds = DB::table('saved_statuses')
            ->where('user_id', $user->id)
            ->pluck('status_id');

        $query = Status::with(['user', 'repostRecord', 'linkPreviewRecord', 'forumTopic'])
            ->whereIn('id', $savedStatusIds)
            ->where('statu', 1)
            ->privacyVisible();

        $clips = $query->orderBy('date', 'desc')->paginate(10);

        return StatusResource::collection($clips);
    }

    public function save(Status $status)
    {
        $user = Auth::user();

        DB::table('saved_statuses')->updateOrInsert(
            ['user_id' => $user->id, 'status_id' => $status->id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        return response()->json(['message' => 'Reel saved successfully']);
    }

    public function unsave(Status $status)
    {
        $user = Auth::user();

        DB::table('saved_statuses')
            ->where('user_id', $user->id)
            ->where('status_id', $status->id)
            ->delete();

        return response()->json(['message' => 'Reel removed from saved']);
    }
}
