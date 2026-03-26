<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $map = [
            // Legacy Quests
            'daily_first_post'              => 'svg-status',
            'daily_three_comments'          => 'svg-comment',
            'daily_five_reactions_given'    => 'svg-thumbs-up',
            'daily_five_visit_exchanges'    => 'svg-timeline',
            'weekly_three_reposts'          => 'svg-share',
            'weekly_ten_reactions_received' => 'svg-thumbs-up',

            // New Premium Quests
            'new-connections'               => 'svg-members',
            'forum-starter'                 => 'svg-forum',
            'web-explorer'                  => 'svg-list-grid-view',
            'tool-collector'                => 'svg-marketplace',
            'service-helper'                => 'svg-ticket',
        ];

        foreach ($map as $slug => $icon) {
            DB::table('quests')->where('slug', $slug)->update(['icon' => $icon]);
        }

        // Fix zero or negative target counts
        DB::table('quests')->where('target_count', '<=', 0)->update(['target_count' => 1]);
        
        // Specific fix for First Post if it was 0
        DB::table('quests')->where('slug', 'daily_first_post')->where('target_count', '<=', 0)->update(['target_count' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for data fixes
    }
};
