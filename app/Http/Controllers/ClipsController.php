<?php

namespace App\Http\Controllers;

use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Status;
use App\Services\StatusActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClipsController extends Controller
{
    public function index(Request $request, $directStatusId = null)
    {
        $tab = $request->input('tab', 'latest');
        if (!in_array($tab, ['latest', 'trending', 'following'], true)) {
            $tab = 'latest';
        }

        $directStatusId = $directStatusId ?: $request->query('clip');
        $targetedStatus = null;
        if ($directStatusId) {
            $targetedStatus = Status::visible()
                ->where('s_type', Status::TYPE_CLIPS)
                ->where('id', (int) $directStatusId)
                ->first();
        }

        $query = Status::visible()->where('status.s_type', Status::TYPE_CLIPS);

        // Targeted clip prioritization (Deep Linking)
        if ($targetedStatus) {
            $query->orderByRaw('CASE WHEN status.id = ? THEN 0 ELSE 1 END', [$targetedStatus->id]);
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

        $activities = $query->paginate(10)->appends($request->only(['tab', 'clip']));

        app(StatusActivityService::class)->decorateMany($activities);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('theme::clips.partials.clips_list', compact('activities'))->render(),
                'next_page_url' => $activities->nextPageUrl()
            ]);
        }

        return view('theme::clips.index', compact('activities', 'tab', 'targetedStatus'));
    }

    public function show(Request $request, $status)
    {
        $clipStatus = Status::visible()
            ->where('s_type', Status::TYPE_CLIPS)
            ->where('id', (int) $status)
            ->first();

        if (!$clipStatus) {
            return redirect()->route('clips.index');
        }

        return $this->index($request, (int) $clipStatus->id);
    }

    public function recordView(Request $request)
    {
        $statusId = $request->input('status_id');
        if (!$statusId) {
            return response()->json(['error' => 'Invalid status ID'], 400);
        }

        $status = Status::visible()
            ->where('s_type', Status::TYPE_CLIPS)
            ->where('id', (int) $statusId)
            ->first();

        if (!$status) {
            return response()->json(['error' => 'Clip not found'], 404);
        }

        $sessionKey = 'clip_viewed_' . $status->id;
        if (!session()->has($sessionKey)) {
            session()->put($sessionKey, true);
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
        if (!$user) {
            return redirect()->route('login');
        }

        $activities = Status::visible()
            ->where('s_type', Status::TYPE_CLIPS)
            ->whereExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                      ->from('saved_statuses')
                      ->whereColumn('saved_statuses.status_id', 'status.id')
                      ->where('saved_statuses.user_id', $user->id);
            })
            ->orderBy('date', 'desc')
            ->paginate(10);

        app(StatusActivityService::class)->decorateMany($activities);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('theme::clips.partials.clips_grid', compact('activities'))->render(),
                'next_page_url' => $activities->nextPageUrl()
            ]);
        }

        return view('theme::clips.saved', compact('activities'));
    }

    public function toggleSave(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $statusId = $request->input('status_id');
        $status = Status::findOrFail($statusId);

        $exists = DB::table('saved_statuses')
            ->where('user_id', $user->id)
            ->where('status_id', $status->id)
            ->exists();

        if ($exists) {
            DB::table('saved_statuses')
                ->where('user_id', $user->id)
                ->where('status_id', $status->id)
                ->delete();
            $action = 'removed';
        } else {
            DB::table('saved_statuses')->insert([
                'user_id' => $user->id,
                'status_id' => $status->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $action = 'added';
        }

        return response()->json(['success' => true, 'action' => $action]);
    }
}
