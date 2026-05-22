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
            'subject_id' => 'required|integer', 
            'type' => 'required|integer', 
            'reaction_name' => 'nullable|string', 
        ]);

        $user = Auth::user();
        $sid = $request->input('subject_id');
        $type = $request->input('type');
        $reactionName = $request->input('reaction_name', 'like');

        $existingLike = Like::where('uid', $user->id)
            ->where('sid', $sid)
            ->where('type', $type)
            ->first();

        if ($existingLike) {
            $existingOption = \App\Models\Option::where('o_parent', $existingLike->id)
                ->where('o_type', 'data_reaction')
                ->first();

            $currentReaction = $existingOption ? $existingOption->o_valuer : 'like';

            if ($currentReaction === $reactionName) {
                // Same reaction -> remove it
                if ($existingOption) $existingOption->delete();
                $existingLike->delete();
                return response()->json(['message' => 'Reaction removed', 'reacted' => false]);
            } else {
                // Different reaction -> update it
                if ($existingOption) {
                    $existingOption->o_valuer = $reactionName;
                    $existingOption->save();
                } else {
                    \App\Models\Option::create([
                        'o_type' => 'data_reaction',
                        'o_parent' => $existingLike->id,
                        'o_valuer' => $reactionName,
                        'o_mode' => 'active'
                    ]);
                }
                return response()->json(['message' => 'Reaction updated', 'reacted' => true, 'reaction' => $reactionName]);
            }
        } else {
            $like = new Like();
            $like->uid = $user->id;
            $like->sid = $sid;
            $like->type = $type;
            $like->save();

            if ($reactionName !== 'like') {
                \App\Models\Option::create([
                    'o_type' => 'data_reaction',
                    'o_parent' => $like->id,
                    'o_valuer' => $reactionName,
                    'o_mode' => 'active'
                ]);
            }

            return response()->json(['message' => 'Reaction added', 'reacted' => true, 'reaction' => $reactionName]);
        }
    }
}
