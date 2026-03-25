@php
    $activeQuests = [];
    if (auth()->check()) {
        $activeQuests = \App\Models\Quest::where('is_active', true)
            ->where('period', 'daily')
            ->orderBy('sort_order', 'asc')
            ->get();
            
        $today = now()->format('Y-m-d');
        $progressMap = \App\Models\QuestProgress::where('user_id', auth()->id())
            ->where('period_key', $today)
            ->get()
            ->keyBy('quest_id');
    }
@endphp

<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name ?? __('messages.daily_quests') }}</p>
    <div class="widget-box-content">
        @auth
            <div class="quest-list">
                @forelse($activeQuests as $quest)
                    @php
                        $progress = $progressMap->get($quest->id);
                        $current = $progress ? $progress->progress : 0;
                        $target = (int) $quest->target_count;
                        $percent = min(100, ($current / $target) * 100);
                        $isCompleted = $progress && $progress->completed_at;
                    @endphp
                    <div class="quest-item" style="margin-bottom: 16px;">
                        <div class="quest-item-info" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <p class="quest-item-title bold" style="font-size: 13px; color: #3e3f5e;">
                                <i class="{{ $quest->icon ?? 'fa fa-bolt' }}" style="color: #615dfa; margin-right: 4px;"></i>
                                {{ __('messages.' . $quest->name_key) }}
                            </p>
                            <p class="quest-item-text" style="font-size: 11px; color: #8f919d;">{{ $current }}/{{ $target }}</p>
                        </div>
                        <div class="progress-bar-wrap" style="height: 6px; background: #eaeaf5; border-radius: 10px; position: relative; overflow: hidden;">
                            <div class="progress-bar" style="width: {{ $percent }}%; height: 100%; background: {{ $isCompleted ? '#10b981' : '#615dfa' }}; transition: width 0.3s ease;"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-center">{{ __('messages.no_active_quests') ?? 'No active quests today.' }}</p>
                @endforelse
            </div>
            <a class="widget-box-button button small white" href="{{ route('profile.show', ['username' => auth()->user()->username, 'tab' => 'history']) }}" style="margin-top: 8px;">{{ __('messages.view_points_history') ?? 'View Points History' }}</a>
        @else
            <p class="text-center" style="font-size: 13px; color: #8f919d;">{{ __('messages.login_to_start_quests') ?? 'Login to start earning points from daily quests!' }}</p>
            <a class="widget-box-button button small primary" href="{{ route('login') }}" style="margin-top: 16px;">{{ __('messages.login') }}</a>
        @endauth
    </div>
</div>
