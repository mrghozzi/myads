<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;

class ReactionController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|integer', // status->tp_id or similar depending on type
            'type' => 'required|integer', // e.g. 2 for statuses
        ]);

        $user = Auth::user();
        $sid = $request->input('subject_id');
        $type = $request->input('type');

        $existingLike = Like::where('uid', $user->id)
            ->where('sid', $sid)
            ->where('type', $type)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            return response()->json(['message' => 'Reaction removed', 'reacted' => false]);
        } else {
            $like = new Like();
            $like->uid = $user->id;
            $like->sid = $sid;
            $like->type = $type;
            $like->save();
            return response()->json(['message' => 'Reaction added', 'reacted' => true]);
        }
    }
}
