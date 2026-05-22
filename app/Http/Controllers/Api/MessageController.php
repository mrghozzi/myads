<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Message;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ConversationResource;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get the latest message for each conversation
        // This is a simplified query; MYADS typically has specific logic in MessageController@index
        // Using subquery to find the latest message per partner
        $latestMessages = Message::select('messages.*')
            ->whereRaw('id_msg IN (
                SELECT MAX(id_msg)
                FROM messages
                WHERE us_env = ? OR us_rec = ?
                GROUP BY CASE
                    WHEN us_env = ? THEN us_rec
                    ELSE us_env
                END
            )', [$userId, $userId, $userId])
            ->with(['sender', 'receiver'])
            ->orderBy('time', 'desc')
            ->paginate(20);

        return ConversationResource::collection($latestMessages);
    }

    public function show($identifier)
    {
        $partner = User::resolvePublicIdentifier($identifier);
        if (!$partner) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $userId = Auth::id();

        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($q) use ($userId, $partner) {
                $q->where('us_env', $userId)->where('us_rec', $partner->id);
            })
            ->orWhere(function ($q) use ($userId, $partner) {
                $q->where('us_env', $partner->id)->where('us_rec', $userId);
            })
            ->orderBy('time', 'desc')
            ->paginate(30);

        // Mark unread messages as read
        Message::where('us_env', $partner->id)
            ->where('us_rec', $userId)
            ->where('state', 0)
            ->update(['state' => 1]);

        return MessageResource::collection($messages);
    }

    public function store(Request $request, $identifier)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        $partner = User::resolvePublicIdentifier($identifier);
        if (!$partner) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user = Auth::user();

        $message = new Message();
        $message->name = $user->username; // Legacy support
        $message->us_env = $user->id;
        $message->us_rec = $partner->id;
        $message->text = $request->input('text'); // This uses the setTextAttribute mutator to encrypt if enabled
        $message->time = time();
        $message->state = 0;
        $message->save();

        return response()->json([
            'message' => 'Message sent',
            'data' => new MessageResource($message)
        ], 201);
    }

    public function markAsRead($identifier)
    {
        $partner = User::resolvePublicIdentifier($identifier);
        if (!$partner) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $userId = Auth::id();

        Message::where('us_env', $partner->id)
            ->where('us_rec', $userId)
            ->where('state', 0)
            ->update(['state' => 1]);

        return response()->json(['message' => 'Messages marked as read']);
    }
}
