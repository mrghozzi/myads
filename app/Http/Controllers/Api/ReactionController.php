<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;
use App\Models\Option;
use App\Models\Notification;
use App\Services\NotificationService;
use App\Services\PointLedgerService;
use App\Services\GamificationService;

class ReactionController extends Controller
{
    public function toggle(
        Request $request,
        NotificationService $notifications,
        PointLedgerService $pointLedger,
        GamificationService $gamification
    ) {
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

        $time = time();

        if ($existingLike) {
            $existingOption = Option::where('o_parent', $existingLike->id)
                ->where('o_type', 'data_reaction')
                ->first();

            $currentReaction = $existingOption ? $existingOption->o_valuer : 'like';

            if ($currentReaction === $reactionName) {
                // Same reaction -> remove it
                $time_t = $existingLike->time_t;
                if ($existingOption) $existingOption->delete();
                $existingLike->delete();
                
                // Remove notification and points
                $ownerId = $this->getPostOwnerId($sid, $type);
                if ($ownerId && $ownerId != $user->id) {
                    Notification::where('uid', $ownerId)
                        ->where('time', $time_t)
                        ->where('state', 1)
                        ->delete();

                    $pointLedger->award($ownerId, -1, 'reaction_removed_received', 'reaction_removed_received', 'reaction', (int) $sid);
                    $pointLedger->award($user->id, -2, 'reaction_removed_given', 'reaction_removed_given', 'reaction', (int) $sid);
                }

                return response()->json(['message' => 'Reaction removed', 'reacted' => false]);
            } else {
                // Different reaction -> update it
                if ($existingOption) {
                    $existingOption->o_valuer = $reactionName;
                    $existingOption->name = $reactionName;
                    $existingOption->save();
                } else {
                    Option::create([
                        'name' => $reactionName,
                        'o_type' => 'data_reaction',
                        'o_order' => $user->id,
                        'o_parent' => $existingLike->id,
                        'o_valuer' => $reactionName,
                        'o_mode' => $time
                    ]);
                }
                return response()->json(['message' => 'Reaction updated', 'reacted' => true, 'reaction' => $reactionName]);
            }
        } else {
            $like = new Like();
            $like->uid = $user->id;
            $like->sid = $sid;
            $like->type = $type;
            $like->time_t = $time;
            $like->save();

            Option::create([
                'name' => $reactionName,
                'o_type' => 'data_reaction',
                'o_order' => $user->id,
                'o_parent' => $like->id,
                'o_valuer' => $reactionName,
                'o_mode' => $time
            ]);

            // Add notification and points
            $ownerId = $this->getPostOwnerId($sid, $type);
            if ($ownerId && $ownerId != $user->id) {
                $postUrl = "/portal"; // Generic default
                if ($type == 2) {
                    $postUrl = "/t" . $sid;
                } elseif ($type == 14) {
                    $postUrl = "/reels";
                } elseif ($type == 22) {
                    $postUrl = "/dr" . $sid;
                }

                $message = __('messages.reaction_notification', ['user' => $user->username]);
                $notifications->send($ownerId, $message, $postUrl, $reactionName, $user->id, 'reaction');

                $pointLedger->award($ownerId, 1, 'reaction_received', 'reaction_received', 'reaction', (int) $sid);
                $pointLedger->award($user->id, 2, 'reaction_given', 'reaction_given', 'reaction', (int) $sid);
                $gamification->recordEvent($ownerId, 'reaction_received');
            }

            $gamification->recordEvent($user->id, 'reaction_given');

            return response()->json(['message' => 'Reaction added', 'reacted' => true, 'reaction' => $reactionName]);
        }
    }

    private function getPostOwnerId($postId, $type)
    {
        if ($type == 2) {
            $topic = \App\Models\ForumTopic::find($postId);
            return $topic ? $topic->uid : null;
        } elseif ($type == 22) {
            $site = \App\Models\Directory::find($postId);
            return $site ? $site->uid : null;
        } elseif ($type == 14) {
            $topic = \App\Models\ForumTopic::find($postId);
            return $topic ? $topic->uid : null;
        }
        return null;
    }
}
