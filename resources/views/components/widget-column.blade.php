@if($widgets->isNotEmpty())
    @foreach($widgets as $widget)
        @switch($widget->o_mode)
            @case('widget_html')
                @include('theme::partials.widgets.widget_html', ['widget' => $widget])
                @break
            @case('widget_members')
                @include('theme::partials.widgets.widget_members', ['widget' => $widget])
                @break
            @case('widget_online_members')
                @include('theme::partials.widgets.widget_online_members', ['widget' => $widget])
                @break
            @case('widget_recent_comments')
                @include('theme::partials.widgets.widget_recent_comments', ['widget' => $widget])
                @break
            @case('widget_stats_box')
                @include('theme::partials.widgets.widget_stats_box', ['widget' => $widget])
                @break
            @case('widget_forum_latest')
                @include('theme::partials.widgets.widget_forum_latest', ['widget' => $widget])
                @break
            @case('widget_news_latest')
                @include('theme::partials.widgets.widget_news_latest', ['widget' => $widget])
                @break
            @case('widget_points_leaderboard')
                @include('theme::partials.widgets.widget_points_leaderboard', ['widget' => $widget])
                @break
            @case('widget_store_latest')
                @include('theme::partials.widgets.widget_store_latest', ['widget' => $widget])
                @break
            @case('widget_directory_latest')
                @include('theme::partials.widgets.widget_directory_latest', ['widget' => $widget])
                @break
            @case('widget_orders_latest')
                @include('theme::partials.widgets.widget_orders_latest', ['widget' => $widget])
                @break
            @case('widget_badges_showcase')
                @include('theme::partials.widgets.widget_badges_showcase', ['widget' => $widget])
                @break
            @case('widget_quests_daily')
                @include('theme::partials.widgets.widget_quests_daily', ['widget' => $widget])
                @break
            @case('widget_landing_footer')
                @include('theme::partials.widgets.widget_landing_footer', ['widget' => $widget])
                @break
            @default
                <!-- Unknown widget mode: {{ $widget->o_mode }} -->
        @endswitch
    @endforeach
@elseif($canManageWidgets)
    <div class="widget-box superdesign-admin-widget-prompt" style="
        position: relative;
        background: linear-gradient(145deg, rgba(97, 93, 250, 0.04) 0%, rgba(35, 210, 226, 0.06) 100%);
        border: 2px dashed rgba(97, 93, 250, 0.35);
        border-radius: 16px;
        padding: 26px 20px;
        margin-bottom: 24px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(97, 93, 250, 0.06);
    ">
        <!-- Ambient Glow -->
        <div style="
            position: absolute;
            top: -30px;
            right: -30px;
            width: 90px;
            height: 90px;
            background: rgba(35, 210, 226, 0.15);
            filter: blur(35px);
            border-radius: 50%;
            pointer-events: none;
        "></div>
        <div style="
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 90px;
            height: 90px;
            background: rgba(97, 93, 250, 0.15);
            filter: blur(35px);
            border-radius: 50%;
            pointer-events: none;
        "></div>

        <!-- Admin Indicator Pill -->
        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
            <span style="
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
                color: #ffffff;
                font-size: 11px;
                font-weight: 700;
                padding: 4px 12px;
                border-radius: 20px;
                letter-spacing: 0.3px;
                box-shadow: 0 4px 12px rgba(97, 93, 250, 0.25);
            ">
                <svg style="width: 13px; height: 13px; fill: currentColor;" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {{ __('messages.admin_widget_area') ?? 'تخصيص الودجات (خاص بالمدير)' }}
            </span>
        </div>

        <!-- Location Badge if set -->
        @if($placeName)
            <div style="font-size: 12px; color: var(--text-color-muted, #8f91ac); font-weight: 600; margin-bottom: 12px; display: inline-flex; align-items: center; gap: 4px;">
                <svg style="width: 14px; height: 14px; stroke: #23d2e2; fill: none; stroke-width: 2;" viewBox="0 0 24 24">
                    <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <span>{{ $placeName }}</span>
            </div>
        @endif

        <!-- Icon Emblem -->
        <div style="
            width: 56px;
            height: 56px;
            margin: 0 auto 14px auto;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.12) 0%, rgba(35, 210, 226, 0.12) 100%);
            border: 1px solid rgba(97, 93, 250, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #615dfa;
            box-shadow: 0 6px 16px rgba(97, 93, 250, 0.08);
        ">
            <svg style="width: 26px; height: 26px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                <path d="M6.5 17.5h.01M17.5 6.5h.01"></path>
            </svg>
        </div>

        <!-- Heading -->
        <h4 style="
            font-size: 15px;
            font-weight: 700;
            color: var(--theme-heading-color, inherit);
            margin: 0 0 6px 0;
            line-height: 1.4;
        ">
            {{ __('messages.no_widgets_in_place_title') ?? 'لا توجد ودجات في هذا المكان بعد' }}
        </h4>

        <!-- Description -->
        <p style="
            font-size: 13px;
            color: var(--text-color, #61616f);
            line-height: 1.5;
            margin: 0 0 18px 0;
            opacity: 0.85;
        ">
            {{ __('messages.no_widgets_in_place_desc') ?? 'يمكنك إضافة وتخصيص الودجات في هذا المكان لمستخدمي الموقع بسهولة عبر لوحة الإدارة.' }}
        </p>

        <!-- CTA Action Button -->
        <a href="{{ route('admin.widgets', array_filter(['place' => $resolvedSide])) }}" class="button primary" style="
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 22px;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(97, 93, 250, 0.35);
            transition: all 0.25s ease;
        " onmouseover="this.style.transform='translateY(-2px) scale(1.02)'; this.style.boxShadow='0 8px 25px rgba(97, 93, 250, 0.45)';" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 6px 20px rgba(97, 93, 250, 0.35)';">
            <svg style="width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>{{ __('messages.add_widgets_here') ?? 'إضافة ودجات في هذا المكان' }}</span>
        </a>
    </div>
@endif

