<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use App\Models\QuestProgress;
use App\Services\GamificationService;
use App\Services\SeoManager;
use Illuminate\Http\Request;

class QuestController extends Controller
{
    public function __construct(
        private readonly GamificationService $gamification,
        private readonly SeoManager $seo
    ) {
    }

    public function index()
    {
        $userId = auth()->id();
        $quests = Quest::where('is_active', true)->orderBy('sort_order')->get();

        $questData = $quests->map(function ($quest) use ($userId) {
            $periodKey = $this->periodKeyFor($quest->period);
            $progressRecord = QuestProgress::where('user_id', $userId)
                ->where('quest_id', $quest->id)
                ->where('period_key', $periodKey)
                ->first();

            $currentProgress = $progressRecord ? $progressRecord->progress : 0;
            $isCompleted = $progressRecord && $progressRecord->completed_at !== null;
            $percent = min(100, ($currentProgress / max(1, $quest->target_count)) * 100);

            return [
                'model' => $quest,
                'current' => $currentProgress,
                'target' => $quest->target_count,
                'percent' => $percent,
                'is_completed' => $isCompleted,
                'reward' => $quest->reward_points,
                'period' => $quest->period,
            ];
        });

        $this->seo->setContext([
            'scope_key' => 'quests',
            'resource_title' => __('messages.quests'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.quests'), 'url' => route('quests.index')],
            ],
        ]);

        return view('theme::quests', [
            'quests' => $questData,
        ]);
    }

    private function periodKeyFor(string $period): string
    {
        return $period === 'weekly' ? now()->format('o-\WW') : now()->format('Y-m-d');
    }
}
