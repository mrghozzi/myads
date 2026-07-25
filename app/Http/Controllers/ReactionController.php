<?php

namespace App\Http\Controllers;

use App\Services\ReactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    public function toggle(
        Request $request,
        ReactionService $reactionService
    ) {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $id = $request->input('id');
        $type = $request->input('type');
        $reaction = $request->input('reaction', 'like');

        if (!$id || !$type) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $result = $reactionService->toggle($user, (int) $id, $type, (string) $reaction);

        if (!$result['success']) {
            return response()->json(['error' => $result['error']], $result['status_code'] ?? 400);
        }

        return response()->json([
            'action' => $result['action'],
            'html' => $result['html']
        ]);
    }
}
