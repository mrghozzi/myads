<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    public function toggle(
        Request $request,
        ReactionService $reactionService
    ) {
        $request->validate([
            'subject_id' => 'required|integer',
            'type' => 'required',
            'reaction_name' => 'nullable|string',
        ]);

        $user = Auth::user();
        $sid = (int) $request->input('subject_id');
        $type = $request->input('type');
        $reactionName = $request->input('reaction_name', 'like');

        $result = $reactionService->toggle($user, $sid, $type, (string) $reactionName);

        if (!$result['success']) {
            return response()->json([
                'message' => $result['error']
            ], $result['status_code'] ?? 400);
        }

        return response()->json([
            'message' => $result['message'],
            'action' => $result['action'],
            'reacted' => $result['reacted'],
            'reaction' => $result['reaction'],
        ]);
    }
}
