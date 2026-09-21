<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Status;
use App\Models\Group;
use App\Http\Resources\StatusResource;
use App\Services\StatusPostService;
use App\Services\LinkPreviewService;
use App\Services\StatusActivityService;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    public function __construct(
        protected StatusPostService $statusPostService
    ) {}

    public function composerOptions(Request $request)
    {
        $user = Auth::user();
        
        $groups = [];
        // Check if user has groups() relation
        if (method_exists($user, 'groups')) {
            $groups = $user->groups()->get()->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'slug' => $group->slug,
                    'image_url' => $group->image_url ? asset($group->image_url) : null,
                ];
            });
        }

        $directoryCategories = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('cat_dir')) {
            $directoryCategories = \Illuminate\Support\Facades\DB::table('cat_dir')->select('id', 'name')->get();
        }

        return response()->json([
            'groups' => $groups,
            'directory_categories' => $directoryCategories,
            'supported_kinds' => ['text', 'gallery', 'link', 'repost', 'video', 'audio', 'file', 'music', 'clips'],
        ]);
    }

    public function linkPreview(Request $request, LinkPreviewService $linkPreviewService, \App\Services\SecurityPolicyService $securityPolicy)
    {
        $request->validate([
            'link_url' => 'required|string|max:2048',
        ]);

        if ($violation = $securityPolicy->urlViolation($request->input('link_url'), 'posts')) {
            return response()->json(['message' => $violation], 422);
        }

        return response()->json($linkPreviewService->fetch($request->input('link_url')));
    }

    public function show(Status $status)
    {
        $status->load(['user', 'linkPreviewRecord', 'repostRecord.originalStatus.user', 'forumTopic.attachments']);
        $resource = (new StatusResource($status))->toArray(request());

        if ((int) $status->s_type === 10 || $status->post_kind === 'video') {
            $viewer = auth('sanctum')->user();
            $topic = $status->forumTopic;

            $suggestedVideos = Status::visible()
                ->where('id', '!=', $status->id)
                ->whereIn('s_type', [10, 2, 100])
                ->where(function ($q) {
                    $q->where('s_type', 10)
                      ->orWhere(function ($sub) {
                          $sub->whereIn('s_type', [2, 100])
                              ->whereHas('forumTopic', function ($ft) {
                                  $ft->whereHas('attachments', function ($att) {
                                      $att->where('mime_type', 'like', 'video/%')
                                          ->orWhere('original_name', 'like', '%.mp4')
                                          ->orWhere('original_name', 'like', '%.webm')
                                          ->orWhere('original_name', 'like', '%.mov')
                                          ->orWhere('original_name', 'like', '%.mkv');
                                  });
                              });
                      });
                })
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            $resource['suggested_videos'] = StatusResource::collection($suggestedVideos)->toArray(request());
            $resource['is_following'] = ($viewer && $topic && $topic->user)
                ? \App\Models\Like::where('uid', $viewer->id)->where('sid', $topic->user->id)->where('type', 1)->exists()
                : false;
            $resource['is_saved'] = $viewer
                ? \Illuminate\Support\Facades\DB::table('saved_statuses')->where('user_id', $viewer->id)->where('status_id', $status->id)->exists()
                : false;
        }

        return response()->json($resource);
    }

    public function store(Request $request)
    {
        try {
            $status = $this->statusPostService->create($request, Auth::user());
            return response()->json([
                'message' => 'Status created successfully',
                'status' => new StatusResource($status)
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first() ?? __('messages.error_occurred');
            return response()->json([
                'message' => $firstError,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage() ?: __('messages.error_occurred')], 422);
        }
    }

    public function update(Request $request, Status $status)
    {
        try {
            $updatedStatus = $this->statusPostService->update($request, $status, Auth::user());
            return response()->json([
                'message' => 'Status updated successfully',
                'status' => new StatusResource($updatedStatus)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first() ?? __('messages.error_occurred');
            return response()->json([
                'message' => $firstError,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => $e->getMessage() ?: __('messages.error_occurred')], 422);
        }
    }

    public function destroy(Status $status)
    {
        try {
            $this->statusPostService->delete($status, Auth::user());
            return response()->json(['message' => 'Status deleted successfully']);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => __('messages.error_occurred')], 403);
        }
    }

    /**
     * Toggle saved / bookmarked state for a status.
     */
    public function toggleSave(Request $request, ?Status $status = null)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $statusId = $status ? $status->id : (int) $request->input('status_id');
        if (!$statusId) {
            return response()->json(['message' => 'Status ID is required'], 422);
        }

        $targetStatus = $status ?: Status::findOrFail($statusId);

        $existing = DB::table('saved_statuses')
            ->where('user_id', $user->id)
            ->where('status_id', $targetStatus->id)
            ->first();

        if ($existing) {
            DB::table('saved_statuses')
                ->where('user_id', $user->id)
                ->where('status_id', $targetStatus->id)
                ->delete();

            $saved = false;
            $message = __('messages.post_unsaved_success') ?? 'Post removed from saved items';
        } else {
            DB::table('saved_statuses')->insert([
                'user_id' => $user->id,
                'status_id' => $targetStatus->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $saved = true;
            $message = __('messages.post_saved_success') ?? 'Post saved successfully';
        }

        $totalSaved = DB::table('saved_statuses')
            ->where('status_id', $targetStatus->id)
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
     * Fetch saved / bookmarked statuses for the authenticated user.
     */
    public function savedStatuses(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $statuses = Status::visible()
            ->join('saved_statuses', 'status.id', '=', 'saved_statuses.status_id')
            ->where('saved_statuses.user_id', $user->id)
            ->orderBy('saved_statuses.id', 'desc')
            ->select('status.*')
            ->paginate(15);

        app(StatusActivityService::class)->decorateMany($statuses);

        return StatusResource::collection($statuses);
    }
}
