<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $badges = [
            'complete_profile' => 'fa-check-double',
            'first_post' => 'fa-hand-sparkles',
            'image_storyteller' => 'fa-camera-retro',
            'link_curator' => 'fa-link',
            'conversation_starter' => 'fa-comment-dots',
            'signal_booster' => 'fa-bullhorn',
            'crowd_favorite' => 'fa-fire-alt',
            'community_magnet' => 'fa-users',
        ];

        foreach ($badges as $slug => $icon) {
            DB::table('badges')->where('slug', $slug)->update(['icon' => $icon]);
        }
    }

    public function down(): void
    {
        // Revert to original seed icons if necessary
        $original = [
            'complete_profile' => 'badge',
            'first_post' => 'blog-posts',
            'image_storyteller' => 'photos',
            'link_curator' => 'public',
            'conversation_starter' => 'comment',
            'signal_booster' => 'share',
            'crowd_favorite' => 'thumbs-up',
            'community_magnet' => 'friend',
        ];

        foreach ($original as $slug => $icon) {
            DB::table('badges')->where('slug', $slug)->update(['icon' => $icon]);
        }
    }
};
