<?php

namespace App\Http\Controllers;

use App\Services\UserPrivacyService;
use App\Models\User;
use Illuminate\Http\Request;

class MentionController extends Controller
{
    public function users(Request $request, UserPrivacyService $privacy)
    {
        $query = trim((string) $request->query('q', ''));
        if ($query === '') {
            return response()->json([]);
        }

        $viewer = $request->user();

        $users = User::query()
            ->where('username', 'like', $query . '%')
            ->orderBy('username')
            ->get(['id', 'username', 'img']);

        return response()->json(
            $users
                ->filter(fn (User $user) => $privacy->canMention($user, $viewer))
                ->take(8)
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'avatar' => $user->img ? asset($user->img) : theme_asset('img/avatar/default.png'),
                    'url' => route('profile.show', $user->username),
                ])
                ->values()
        );
    }
}
