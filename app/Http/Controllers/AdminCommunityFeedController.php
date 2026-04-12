<?php

namespace App\Http\Controllers;

use App\Support\CommunityFeedSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminCommunityFeedController extends Controller
{
    public function settings()
    {
        $settings = CommunityFeedSettings::all();

        return view('admin::admin.community_feed_settings', compact('settings'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'freshness_base_score' => 'required|numeric|min:0|max:999999',
            'freshness_decay_exponent' => 'required|numeric|min:0.01|max:10',
            'freshness_suppression_after_hours' => 'required|integer|min:1|max:720',
            'freshness_suppression_multiplier' => 'required|numeric|min:0|max:1',
            'view_weight' => 'required|numeric|min:0|max:999',
            'max_views_score' => 'required|numeric|min:0|max:999999',
            'total_reaction_weight' => 'required|numeric|min:0|max:999',
            'total_comment_weight' => 'required|numeric|min:0|max:999',
            'total_repost_weight' => 'required|numeric|min:0|max:999',
            'recent_reaction_weight' => 'required|numeric|min:0|max:999',
            'recent_comment_weight' => 'required|numeric|min:0|max:999',
            'recent_repost_weight' => 'required|numeric|min:0|max:999',
            'rapid_reaction_weight' => 'required|numeric|min:0|max:999',
            'rapid_comment_weight' => 'required|numeric|min:0|max:999',
            'rapid_repost_weight' => 'required|numeric|min:0|max:999',
            'following_boost' => 'required|numeric|min:0|max:999999',
            'author_affinity_boost' => 'required|numeric|min:0|max:999999',
            'content_affinity_boost' => 'required|numeric|min:0|max:999999',
            'social_proof_boost' => 'required|numeric|min:0|max:999999',
            'rapid_window_hours' => 'required|integer|min:1|max:72',
            'trend_window_hours' => 'required|integer|min:1|max:336',
            'rescue_max_age_hours' => 'required|integer|min:1|max:720',
            'rescue_min_recent_reactions' => 'required|integer|min:0|max:999999',
            'rescue_min_recent_comments' => 'required|integer|min:0|max:999999',
            'rescue_min_recent_reposts' => 'required|integer|min:0|max:999999',
            'repeat_author_penalty' => 'required|numeric|min:0|max:999999',
            'repeat_type_penalty' => 'required|numeric|min:0|max:999999',
            'fresh_candidate_hours' => 'required|integer|min:1|max:720',
            'fresh_candidate_limit' => 'required|integer|min:1|max:2000',
            'rescue_candidate_limit' => 'required|integer|min:1|max:2000',
            'cache_ttl_seconds' => 'required|integer|min:0|max:86400',
        ]);

        CommunityFeedSettings::save($validated);

        return redirect()->route('admin.community.feed.settings')
            ->with('success', __('messages.community_feed_settings_saved'));
    }
}
