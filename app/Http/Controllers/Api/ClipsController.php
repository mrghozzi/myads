<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Status;
use App\Http\Resources\StatusResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClipsController extends Controller
{
    public function index(Request $request)
    {
        $query = Status::with(['user', 'repostRecord', 'linkPreviewRecord'])
            ->where('s_type', Status::TYPE_CLIPS)
            ->where('statu', 1)
            ->privacyVisible();

        // Basic ordering for now, descending by date
        $clips = $query->orderBy('date', 'desc')->paginate(10);

        return StatusResource::collection($clips);
    }

    public function saved(Request $request)
    {
        $user = Auth::user();

        $savedStatusIds = DB::table('saved_statuses')
            ->where('user_id', $user->id)
            ->pluck('status_id');

        $query = Status::with(['user', 'repostRecord', 'linkPreviewRecord'])
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
