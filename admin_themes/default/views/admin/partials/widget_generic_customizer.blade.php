@php
    $config = [];
    if (!empty($widget?->o_valuer)) {
        if (is_array($widget->o_valuer)) {
            $config = $widget->o_valuer;
        } else {
            $decoded = json_decode((string) $widget->o_valuer, true);
            $config = is_array($decoded) ? $decoded : [];
        }
    }

    $iconsMap = [
        'widget_members' => 'feather-users',
        'widget_online_members' => 'feather-user-check',
        'widget_forum_latest' => 'feather-message-square',
        'widget_news_latest' => 'feather-file-text',
        'widget_recent_comments' => 'feather-message-circle',
        'widget_stats_box' => 'feather-bar-chart-2',
        'widget_points_leaderboard' => 'feather-award',
        'widget_store_latest' => 'feather-shopping-bag',
        'widget_directory_latest' => 'feather-globe',
        'widget_orders_latest' => 'feather-briefcase',
        'widget_badges_showcase' => 'feather-shield',
        'widget_quests_daily' => 'feather-target',
    ];
    $icon = $iconsMap[$type] ?? 'feather-sliders';
@endphp

<div class="admin-form-grid__full mt-3 pt-3 border-top border-light-subtle" style="grid-column: 1 / -1;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <div style="width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, rgba(97, 93, 250, 0.15) 0%, rgba(35, 210, 226, 0.15) 100%); color: #615dfa; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                <i class="{{ $icon }}"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" style="font-size: 15px; color: var(--admin-text-main, #1e293b);">
                    {{ __('messages.widget_customization') }}
                </h5>
                <span class="text-muted" style="font-size: 12px;">{{ __('messages.customize_options_for') }} <code>{{ $type }}</code></span>
            </div>
        </div>
        <span class="badge bg-light text-primary border" style="font-size: 11px; padding: 6px 12px; border-radius: 20px;">
            <i class="feather-check-circle me-1 text-success"></i> {{ __('messages.customizable_widget') }}
        </span>
    </div>

    <div class="card border border-light-subtle shadow-none" style="border-radius: 14px; background: var(--admin-input-bg, #f8fafc); overflow: hidden;">
        <div class="card-body p-3 p-md-4">

            {{-- 1. WIDGET_MEMBERS (Suggest Members) --}}
            @if($type === 'widget_members')
                @php
                    $limit = (int) ($config['limit'] ?? 5);
                    $orderBy = $config['order_by'] ?? 'random';
                    $verifiedOnly = !empty($config['verified_only']);
                    $showStats = isset($config['show_stats']) ? (bool) $config['show_stats'] : true;
                    $showFollowBtn = isset($config['show_follow_btn']) ? (bool) $config['show_follow_btn'] : true;
                @endphp
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.widget_items_count') }}</label>
                        <select name="widget_config[limit]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([3, 5, 8, 10, 15, 20] as $cnt)
                                <option value="{{ $cnt }}" {{ $limit === $cnt ? 'selected' : '' }}>{{ $cnt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.widget_order_by') }}</label>
                        <select name="widget_config[order_by]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            <option value="random" {{ $orderBy === 'random' ? 'selected' : '' }}>{{ __('messages.order_random') }}</option>
                            <option value="popular" {{ $orderBy === 'popular' ? 'selected' : '' }}>{{ __('messages.order_popular') }}</option>
                            <option value="newest" {{ $orderBy === 'newest' ? 'selected' : '' }}>{{ __('messages.order_newest') }}</option>
                            <option value="active" {{ $orderBy === 'active' ? 'selected' : '' }}>{{ __('messages.order_active') }}</option>
                        </select>
                    </div>
                    <div class="col-12"><hr class="border-light-subtle my-2"></div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-check-circle text-primary me-1"></i> {{ __('messages.verified_members_only') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[verified_only]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[verified_only]" value="1" {{ $verifiedOnly ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-bar-chart-2 text-info me-1"></i> {{ __('messages.show_stats_counters') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_stats]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_stats]" value="1" {{ $showStats ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-user-plus text-success me-1"></i> {{ __('messages.show_follow_button') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_follow_btn]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_follow_btn]" value="1" {{ $showFollowBtn ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 2. WIDGET_ONLINE_MEMBERS (Online Members) --}}
            @elseif($type === 'widget_online_members')
                @php
                    $limit = (int) ($config['limit'] ?? 5);
                    $threshold = (int) ($config['active_threshold'] ?? 15);
                    $showStats = isset($config['show_stats']) ? (bool) $config['show_stats'] : true;
                    $showMessageBtn = isset($config['show_message_btn']) ? (bool) $config['show_message_btn'] : true;
                @endphp
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.widget_items_count') }}</label>
                        <select name="widget_config[limit]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([3, 5, 8, 10, 15, 20] as $cnt)
                                <option value="{{ $cnt }}" {{ $limit === $cnt ? 'selected' : '' }}>{{ $cnt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.active_threshold_minutes') }}</label>
                        <select name="widget_config[active_threshold]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([5 => '5 دقائق', 10 => '10 دقائق', 15 => '15 دقيقة (افتراضي)', 30 => '30 دقيقة', 60 => '60 دقيقة'] as $mins => $label)
                                <option value="{{ $mins }}" {{ $threshold === $mins ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12"><hr class="border-light-subtle my-2"></div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-bar-chart text-info me-1"></i> {{ __('messages.show_stats_counters') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_stats]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_stats]" value="1" {{ $showStats ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-mail text-success me-1"></i> {{ __('messages.show_message_button') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_message_btn]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_message_btn]" value="1" {{ $showMessageBtn ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 3. WIDGET_FORUM_LATEST (Latest Forum Topics) --}}
            @elseif($type === 'widget_forum_latest')
                @php
                    $limit = (int) ($config['limit'] ?? 5);
                    $categoryId = (int) ($config['category_id'] ?? 0);
                    $showAuthor = isset($config['show_author']) ? (bool) $config['show_author'] : true;
                    $showCategory = isset($config['show_category']) ? (bool) $config['show_category'] : true;
                    $showDate = isset($config['show_date']) ? (bool) $config['show_date'] : true;
                    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
                    $buttonText = $config['button_text'] ?? '';
                    $categories = \App\Models\ForumCategory::orderBy('ordercat', 'asc')->get();
                @endphp
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.widget_items_count') }}</label>
                        <select name="widget_config[limit]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([3, 5, 8, 10, 15] as $cnt)
                                <option value="{{ $cnt }}" {{ $limit === $cnt ? 'selected' : '' }}>{{ $cnt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.category_filter') }}</label>
                        <select name="widget_config[category_id]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            <option value="0" {{ $categoryId === 0 ? 'selected' : '' }}>-- {{ __('messages.all_categories') }} --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $categoryId === (int)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.button_custom_text') }}</label>
                        <input type="text" name="widget_config[button_text]" value="{{ $buttonText }}" placeholder="{{ __('messages.see_all') }}" class="form-control admin-form-control" style="border-radius: 10px; font-size: 13px;">
                    </div>
                    <div class="col-12"><hr class="border-light-subtle my-2"></div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-user text-primary me-1"></i> {{ __('messages.show_author_info') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_author]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_author]" value="1" {{ $showAuthor ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-tag text-info me-1"></i> {{ __('messages.show_category_badge') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_category]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_category]" value="1" {{ $showCategory ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-clock text-warning me-1"></i> {{ __('messages.show_relative_date') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_date]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_date]" value="1" {{ $showDate ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-arrow-up-right text-success me-1"></i> {{ __('messages.show_action_button') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_button]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_button]" value="1" {{ $showButton ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 4. WIDGET_NEWS_LATEST (Latest News) --}}
            @elseif($type === 'widget_news_latest')
                @php
                    $limit = (int) ($config['limit'] ?? 3);
                    $showDate = isset($config['show_date']) ? (bool) $config['show_date'] : true;
                    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
                    $buttonText = $config['button_text'] ?? '';
                @endphp
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.widget_items_count') }}</label>
                        <select name="widget_config[limit]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([2, 3, 5, 8, 10] as $cnt)
                                <option value="{{ $cnt }}" {{ $limit === $cnt ? 'selected' : '' }}>{{ $cnt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.button_custom_text') }}</label>
                        <input type="text" name="widget_config[button_text]" value="{{ $buttonText }}" placeholder="{{ __('messages.see_all') }}" class="form-control admin-form-control" style="border-radius: 10px; font-size: 13px;">
                    </div>
                    <div class="col-12"><hr class="border-light-subtle my-2"></div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-clock text-warning me-1"></i> {{ __('messages.show_relative_date') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_date]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_date]" value="1" {{ $showDate ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-arrow-up-right text-success me-1"></i> {{ __('messages.show_action_button') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_button]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_button]" value="1" {{ $showButton ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 5. WIDGET_RECENT_COMMENTS (Recent Comments) --}}
            @elseif($type === 'widget_recent_comments')
                @php
                    $limit = (int) ($config['limit'] ?? 5);
                    $snippetLength = (int) ($config['snippet_length'] ?? 35);
                    $showAvatar = isset($config['show_avatar']) ? (bool) $config['show_avatar'] : true;
                    $showDate = isset($config['show_date']) ? (bool) $config['show_date'] : true;
                @endphp
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.widget_items_count') }}</label>
                        <select name="widget_config[limit]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([3, 5, 8, 10, 15] as $cnt)
                                <option value="{{ $cnt }}" {{ $limit === $cnt ? 'selected' : '' }}>{{ $cnt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.comment_snippet_length') }}</label>
                        <select name="widget_config[snippet_length]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([25 => '25 ' . __('messages.characters'), 35 => '35 ' . __('messages.characters') . ' (افتراضي)', 50 => '50 ' . __('messages.characters'), 75 => '75 ' . __('messages.characters'), 100 => '100 ' . __('messages.characters')] as $len => $label)
                                <option value="{{ $len }}" {{ $snippetLength === $len ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12"><hr class="border-light-subtle my-2"></div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-user text-primary me-1"></i> {{ __('messages.show_author_info') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_avatar]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_avatar]" value="1" {{ $showAvatar ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-clock text-warning me-1"></i> {{ __('messages.show_relative_date') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_date]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_date]" value="1" {{ $showDate ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 6. WIDGET_STATS_BOX (Stats Box) --}}
            @elseif($type === 'widget_stats_box')
                @php
                    $showBanners = isset($config['show_banners']) ? (bool) $config['show_banners'] : true;
                    $showLinks = isset($config['show_links']) ? (bool) $config['show_links'] : true;
                    $showUsers = isset($config['show_users']) ? (bool) $config['show_users'] : true;
                    $showTopics = isset($config['show_topics']) ? (bool) $config['show_topics'] : true;
                    $showOrders = !empty($config['show_orders']);
                    $enableSlider = isset($config['enable_slider']) ? (bool) $config['enable_slider'] : true;
                @endphp
                <div class="row g-2">
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-image text-danger me-1"></i> {{ __('messages.show_banner_stats') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_banners]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_banners]" value="1" {{ $showBanners ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-link-2 text-primary me-1"></i> {{ __('messages.show_link_stats') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_links]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_links]" value="1" {{ $showLinks ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-users text-success me-1"></i> {{ __('messages.show_user_stats') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_users]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_users]" value="1" {{ $showUsers ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-message-square text-info me-1"></i> {{ __('messages.show_topic_stats') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_topics]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_topics]" value="1" {{ $showTopics ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-briefcase text-warning me-1"></i> {{ __('messages.show_order_stats') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_orders]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_orders]" value="1" {{ $showOrders ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-sliders text-secondary me-1"></i> {{ __('messages.slider_mode') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[enable_slider]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[enable_slider]" value="1" {{ $enableSlider ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 7. WIDGET_POINTS_LEADERBOARD (Points Leaderboard) --}}
            @elseif($type === 'widget_points_leaderboard')
                @php
                    $limit = (int) ($config['limit'] ?? 5);
                    $showRanks = isset($config['show_ranks']) ? (bool) $config['show_ranks'] : true;
                    $showPoints = isset($config['show_points']) ? (bool) $config['show_points'] : true;
                    $showButton = !empty($config['show_button']);
                    $buttonText = $config['button_text'] ?? '';
                @endphp
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.widget_items_count') }}</label>
                        <select name="widget_config[limit]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([3, 5, 8, 10, 15] as $cnt)
                                <option value="{{ $cnt }}" {{ $limit === $cnt ? 'selected' : '' }}>{{ $cnt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.button_custom_text') }}</label>
                        <input type="text" name="widget_config[button_text]" value="{{ $buttonText }}" placeholder="{{ __('messages.leaderboard') ?? 'Leaderboard' }}" class="form-control admin-form-control" style="border-radius: 10px; font-size: 13px;">
                    </div>
                    <div class="col-12"><hr class="border-light-subtle my-2"></div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-award text-warning me-1"></i> {{ __('messages.show_rank_badges') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_ranks]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_ranks]" value="1" {{ $showRanks ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-zap text-primary me-1"></i> {{ __('messages.show_points_balance') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_points]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_points]" value="1" {{ $showPoints ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-arrow-up-right text-success me-1"></i> {{ __('messages.show_action_button') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_button]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_button]" value="1" {{ $showButton ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 8. WIDGET_STORE_LATEST (Latest Store Products) --}}
            @elseif($type === 'widget_store_latest')
                @php
                    $limit = (int) ($config['limit'] ?? 5);
                    $showPrice = isset($config['show_price']) ? (bool) $config['show_price'] : true;
                    $showSeller = isset($config['show_seller']) ? (bool) $config['show_seller'] : true;
                    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
                    $buttonText = $config['button_text'] ?? '';
                @endphp
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.widget_items_count') }}</label>
                        <select name="widget_config[limit]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([3, 5, 8, 10, 15] as $cnt)
                                <option value="{{ $cnt }}" {{ $limit === $cnt ? 'selected' : '' }}>{{ $cnt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.button_custom_text') }}</label>
                        <input type="text" name="widget_config[button_text]" value="{{ $buttonText }}" placeholder="{{ __('messages.see_whats_new') }}" class="form-control admin-form-control" style="border-radius: 10px; font-size: 13px;">
                    </div>
                    <div class="col-12"><hr class="border-light-subtle my-2"></div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-tag text-primary me-1"></i> {{ __('messages.show_product_price') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_price]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_price]" value="1" {{ $showPrice ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-user text-info me-1"></i> {{ __('messages.show_seller_name') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_seller]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_seller]" value="1" {{ $showSeller ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-arrow-up-right text-success me-1"></i> {{ __('messages.show_action_button') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_button]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_button]" value="1" {{ $showButton ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 9. WIDGET_DIRECTORY_LATEST (Latest Directory Sites) --}}
            @elseif($type === 'widget_directory_latest')
                @php
                    $limit = (int) ($config['limit'] ?? 5);
                    $categoryId = (int) ($config['category_id'] ?? 0);
                    $showCategory = isset($config['show_category']) ? (bool) $config['show_category'] : true;
                    $showDate = isset($config['show_date']) ? (bool) $config['show_date'] : true;
                    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
                    $buttonText = $config['button_text'] ?? '';
                    $dirCats = \App\Models\DirectoryCategory::all();
                @endphp
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.widget_items_count') }}</label>
                        <select name="widget_config[limit]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([3, 5, 8, 10, 15] as $cnt)
                                <option value="{{ $cnt }}" {{ $limit === $cnt ? 'selected' : '' }}>{{ $cnt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.category_filter') }}</label>
                        <select name="widget_config[category_id]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            <option value="0" {{ $categoryId === 0 ? 'selected' : '' }}>-- {{ __('messages.all_categories') }} --</option>
                            @foreach($dirCats as $dcat)
                                <option value="{{ $dcat->id }}" {{ $categoryId === (int)$dcat->id ? 'selected' : '' }}>{{ $dcat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.button_custom_text') }}</label>
                        <input type="text" name="widget_config[button_text]" value="{{ $buttonText }}" placeholder="{{ __('messages.see_all') }}" class="form-control admin-form-control" style="border-radius: 10px; font-size: 13px;">
                    </div>
                    <div class="col-12"><hr class="border-light-subtle my-2"></div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-folder text-info me-1"></i> {{ __('messages.show_category_badge') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_category]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_category]" value="1" {{ $showCategory ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-clock text-warning me-1"></i> {{ __('messages.show_relative_date') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_date]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_date]" value="1" {{ $showDate ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-arrow-up-right text-success me-1"></i> {{ __('messages.show_action_button') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_button]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_button]" value="1" {{ $showButton ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 10. WIDGET_ORDERS_LATEST (Latest Service Orders) --}}
            @elseif($type === 'widget_orders_latest')
                @php
                    $limit = (int) ($config['limit'] ?? 5);
                    $showBudget = isset($config['show_budget']) ? (bool) $config['show_budget'] : true;
                    $showOffers = isset($config['show_offers']) ? (bool) $config['show_offers'] : true;
                    $showDate = isset($config['show_date']) ? (bool) $config['show_date'] : true;
                    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
                    $buttonText = $config['button_text'] ?? '';
                @endphp
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.widget_items_count') }}</label>
                        <select name="widget_config[limit]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([3, 5, 8, 10, 15] as $cnt)
                                <option value="{{ $cnt }}" {{ $limit === $cnt ? 'selected' : '' }}>{{ $cnt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.button_custom_text') }}</label>
                        <input type="text" name="widget_config[button_text]" value="{{ $buttonText }}" placeholder="{{ __('messages.view_all_orders') }}" class="form-control admin-form-control" style="border-radius: 10px; font-size: 13px;">
                    </div>
                    <div class="col-12"><hr class="border-light-subtle my-2"></div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-dollar-sign text-success me-1"></i> {{ __('messages.show_budget_info') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_budget]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_budget]" value="1" {{ $showBudget ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-inbox text-info me-1"></i> {{ __('messages.show_offers_count') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_offers]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_offers]" value="1" {{ $showOffers ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-clock text-warning me-1"></i> {{ __('messages.show_relative_date') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_date]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_date]" value="1" {{ $showDate ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-arrow-up-right text-primary me-1"></i> {{ __('messages.show_action_button') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_button]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_button]" value="1" {{ $showButton ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 11. WIDGET_BADGES_SHOWCASE (Badges Showcase) --}}
            @elseif($type === 'widget_badges_showcase')
                @php
                    $maxBadges = (int) ($config['max_badges'] ?? 8);
                    $badgeSize = $config['badge_size'] ?? 'medium';
                    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
                    $guestVisibility = !empty($config['guest_visibility']);
                @endphp
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.max_badges_count') }}</label>
                        <select name="widget_config[max_badges]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            @foreach([4, 6, 8, 12, 16, 24] as $cnt)
                                <option value="{{ $cnt }}" {{ $maxBadges === $cnt ? 'selected' : '' }}>{{ $cnt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.badge_icon_size') }}</label>
                        <select name="widget_config[badge_size]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            <option value="small" {{ $badgeSize === 'small' ? 'selected' : '' }}>{{ __('messages.size_small') }}</option>
                            <option value="medium" {{ $badgeSize === 'medium' ? 'selected' : '' }}>{{ __('messages.size_medium') }}</option>
                            <option value="large" {{ $badgeSize === 'large' ? 'selected' : '' }}>{{ __('messages.size_large') }}</option>
                        </select>
                    </div>
                    <div class="col-12"><hr class="border-light-subtle my-2"></div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-arrow-up-right text-success me-1"></i> {{ __('messages.show_action_button') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_button]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_button]" value="1" {{ $showButton ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-eye text-primary me-1"></i> {{ __('messages.guest_visibility_prompt') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[guest_visibility]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[guest_visibility]" value="1" {{ $guestVisibility ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- 12. WIDGET_QUESTS_DAILY (Daily Quests) --}}
            @elseif($type === 'widget_quests_daily')
                @php
                    $period = $config['period'] ?? 'daily';
                    $showProgress = isset($config['show_progress']) ? (bool) $config['show_progress'] : true;
                    $showButtons = isset($config['show_buttons']) ? (bool) $config['show_buttons'] : true;
                @endphp
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="admin-form-label fw-bold mb-1" style="font-size: 12px;">{{ __('messages.quest_period_filter') }}</label>
                        <select name="widget_config[period]" class="form-select admin-form-control" style="border-radius: 10px; font-size: 13px;">
                            <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>{{ __('messages.quests_daily_only') }}</option>
                            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>{{ __('messages.quests_all_active') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between mt-md-4">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-sliders text-primary me-1"></i> {{ __('messages.show_progress_bar') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_progress]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_progress]" value="1" {{ $showProgress ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-12"><hr class="border-light-subtle my-2"></div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                            <span style="font-size: 12px; font-weight: 600;"><i class="feather-external-link text-success me-1"></i> {{ __('messages.show_footer_actions') }}</span>
                            <div class="form-check form-switch m-0">
                                <input type="hidden" name="widget_config[show_buttons]" value="0">
                                <input class="form-check-input" type="checkbox" name="widget_config[show_buttons]" value="1" {{ $showButtons ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            @endif

        </div>
    </div>
</div>
