<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Status;
use App\Http\Resources\StatusResource;

class StatusController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $user = Auth::user();

        // Very basic status creation for Phase 1
        $status = new Status();
        $status->uid = $user->id;
        $status->tp_id = $user->id; // Using user id as topic id for basic status (similar to original logic)
        $status->s_type = 100; // General status
        $status->txt = $request->input('text');
        $status->date = time();
        $status->statu = 1; // Active
        $status->save();

        // Note: For a real production app, we would also parse mentions, hashtags, and handle file uploads (images/videos)
        // using StatusActivityService or StatusService as done in Web StatusController.

        return response()->json([
            'message' => 'Status created successfully',
            'status' => new StatusResource($status)
        ], 201);
    }

    public function destroy(Status $status)
    {
        if (Auth::id() !== $status->uid && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $status->delete();
        return response()->json(['message' => 'Status deleted successfully']);
    }
}
