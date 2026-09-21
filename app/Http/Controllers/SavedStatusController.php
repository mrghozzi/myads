<?php

namespace App\Http\Controllers;

use App\Models\SavedStatus;
use App\Models\Status;
use App\Services\StatusActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SavedStatusController extends Controller
{
    public function __construct(
        private readonly StatusActivityService $activityService
    ) {
    }

    /**
     * Toggle bookmark/saved status for a given status ID.
     */
    public function toggle(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => __('messages.unauthorized') ?? 'Unauthorized'], 401);
        }

        $request->validate([
            'status_id' => 'required|integer|exists:status,id',
        ]);

        $statusId = (int) $request->input('status_id');
        $status = Status::findOrFail($statusId);

        $existing = DB::table('saved_statuses')
            ->where('user_id', $user->id)
            ->where('status_id', $status->id)
            ->first();

        if ($existing) {
            DB::table('saved_statuses')
                ->where('user_id', $user->id)
                ->where('status_id', $status->id)
                ->delete();

            $saved = false;
            $message = __('messages.post_unsaved_success');
        } else {
            DB::table('saved_statuses')->insert([
                'user_id' => $user->id,
                'status_id' => $status->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $saved = true;
            $message = __('messages.post_saved_success');
        }

        $totalSaved = DB::table('saved_statuses')
            ->where('status_id', $status->id)
            ->count();

        return response()->json([
            'success' => true,
            'saved' => $saved,
            'action' => $saved ? 'added' : 'removed',
            'count' => $totalSaved,
            'message' => $message,
        ]);
    }

    /**
     * Display the authenticated user's saved statuses.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $statuses = Status::visible()
            ->join('saved_statuses', 'status.id', '=', 'saved_statuses.status_id')
            ->where('saved_statuses.user_id', $user->id)
            ->orderBy('saved_statuses.id', 'desc')
            ->select('status.*')
            ->paginate(15);

        $this->activityService->decorateMany($statuses);

        $this->seo([
            'scope_key' => 'saved_posts',
            'resource_title' => __('messages.saved_posts'),
            'description' => __('messages.saved_posts_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.saved_posts'), 'url' => route('bookmarks.index')],
            ],
        ]);

        return view('theme::saved', compact('statuses'));
    }
}
