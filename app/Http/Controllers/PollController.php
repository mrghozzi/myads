<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    public function vote(Request $request, Poll $poll)
    {
        $user = Auth::user();
        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => __('messages.login_required') ?? 'Login required to vote.'], 401);
            }
            return redirect()->route('login');
        }

        if ($poll->is_expired) {
            $msg = __('messages.poll_ended') ?? 'This poll has ended.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        if ($poll->hasUserVoted($user->id)) {
            $msg = __('messages.poll_already_voted') ?? 'You have already voted in this poll.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $validated = $request->validate([
            'poll_option_id' => 'required|integer|exists:poll_options,id',
        ]);

        $option = PollOption::where('id', $validated['poll_option_id'])
            ->where('poll_id', $poll->id)
            ->firstOrFail();

        DB::transaction(function () use ($poll, $option, $user, $request) {
            PollVote::create([
                'poll_id' => $poll->id,
                'poll_option_id' => $option->id,
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
            ]);

            $option->increment('votes_count');
        });

        // Award points for participating in polls if gamification is active
        try {
            app(GamificationService::class)->awardPoints($user, 1, 'poll_vote', $poll->id);
        } catch (\Throwable) {
            // Non-critical, continue
        }

        // Refresh poll and options
        $poll->refresh();
        $poll->load('options');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.poll_voted_successfully') ?? 'Your vote has been recorded!',
                'total_votes' => $poll->total_votes,
                'user_voted_option_id' => $option->id,
                'options' => $poll->options->map(fn ($opt) => [
                    'id' => $opt->id,
                    'option_text' => $opt->option_text,
                    'votes_count' => $opt->votes_count,
                    'percentage' => $opt->percentage,
                ]),
            ]);
        }

        return back()->with('success', __('messages.poll_voted_successfully') ?? 'Your vote has been recorded!');
    }
}

