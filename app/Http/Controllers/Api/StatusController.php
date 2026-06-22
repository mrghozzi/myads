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
        return new StatusResource($status->load(['user', 'linkPreviewRecord', 'repostRecord.originalStatus.user']));
    }

    public function store(Request $request)
    {
        try {
            $status = $this->statusPostService->create($request, Auth::user());
            return response()->json([
                'message' => 'Status created successfully',
                'status' => new StatusResource($status)
            ], 201);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => __('messages.error_occurred')], 422);
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
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => __('messages.error_occurred')], 422);
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
}
