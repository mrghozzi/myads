@extends('theme::layouts.master')

@section('title', $pageTitle ?? ($articleData['title'] . ' - ' . __('help.help_center') . ' - ' . $siteName))

@section('content')
<div class="content-grid" style="padding-bottom: 80px;">
    <style>
        .help-breadcrumbs {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.88rem;
            color: var(--myads-text-muted, #64748b);
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .help-breadcrumbs a {
            color: var(--myads-text-muted, #64748b);
            text-decoration: none;
            transition: color 0.2s;
        }

        .help-breadcrumbs a:hover {
            color: var(--myads-primary, #615dfa);
        }

        .help-article-container {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 32px;
        }

        .help-article-main {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-xl, 24px);
            padding: 40px;
            box-shadow: var(--myads-shadow-sm, 0 4px 14px rgba(0,0,0,0.03));
        }

        .help-article-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            background: rgba(97, 93, 250, 0.1);
            color: var(--myads-primary, #615dfa);
            margin-bottom: 14px;
        }

        .help-article-headline {
            font-size: 2.1rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            line-height: 1.3;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
        }

        .help-article-meta-bar {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 24px;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--myads-surface-border, #f1f5f9);
            font-size: 0.88rem;
            color: var(--myads-text-muted, #64748b);
            flex-wrap: wrap;
        }

        .help-summary-box {
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.08) 0%, rgba(35, 210, 226, 0.06) 100%);
            border-right: 4px solid var(--myads-primary, #615dfa);
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 36px;
            font-size: 1.05rem;
            line-height: 1.7;
            color: var(--myads-text-main, #1e293b);
            font-weight: 500;
        }

        [dir="ltr"] .help-summary-box {
            border-right: none;
            border-left: 4px solid var(--myads-primary, #615dfa);
        }

        /* Action Shortcut Button */
        .help-shortcut-card {
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.12), rgba(35, 210, 226, 0.12));
            border: 1px solid rgba(97, 93, 250, 0.25);
            border-radius: 16px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 36px;
            flex-wrap: wrap;
        }

        .help-shortcut-text {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--myads-text-main, #1e293b);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-shortcut-action {
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff !important;
            padding: 10px 22px;
            border-radius: 9999px;
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(97, 93, 250, 0.35);
            transition: all 0.2s ease;
        }

        .btn-shortcut-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(97, 93, 250, 0.5);
        }

        /* Step Cards */
        .help-steps-container {
            margin-bottom: 36px;
        }

        .help-step-item {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            background: var(--myads-surface-elevated, #f8faff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: 16px;
            padding: 20px 22px;
            transition: all 0.2s ease;
        }

        [data-theme="css_d"] .help-step-item {
            background: rgba(33, 40, 59, 0.5);
        }

        .help-step-item:hover {
            border-color: var(--myads-primary, #615dfa);
            box-shadow: 0 6px 18px rgba(97, 93, 250, 0.08);
        }

        .help-step-number {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(97, 93, 250, 0.25);
        }

        .help-step-content {
            font-size: 0.98rem;
            color: var(--myads-text-main, #1e293b);
            line-height: 1.7;
            padding-top: 6px;
            flex: 1;
        }

        /* Pro Tip & Note */
        .help-callout-tip {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .help-callout-tip i {
            color: #10b981;
            font-size: 1.4rem;
            margin-top: 2px;
        }

        .help-callout-note {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.25);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 36px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .help-callout-note i {
            color: #f59e0b;
            font-size: 1.4rem;
            margin-top: 2px;
        }

        /* Rating / Feedback Widget */
        .help-rating-widget {
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.08) 0%, rgba(35, 210, 226, 0.06) 100%);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 20px);
            padding: 28px 24px;
            text-align: center;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .help-rating-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            margin-bottom: 8px;
        }

        .help-rating-subtitle {
            font-size: 0.9rem;
            color: var(--myads-text-muted, #64748b);
            margin-bottom: 20px;
        }

        .help-rating-buttons {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        .btn-vote {
            padding: 10px 24px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--myads-surface, #ffffff);
            color: var(--myads-text-main, #1e293b);
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }

        .btn-vote:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .btn-vote.vote-yes:hover,
        .btn-vote.vote-yes.active {
            border-color: #10b981;
            background: #10b981;
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);
        }

        .btn-vote.vote-no:hover,
        .btn-vote.vote-no.active {
            border-color: #ef4444;
            background: #ef4444;
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(239, 68, 68, 0.35);
        }

        .help-rating-stats-bar {
            max-width: 420px;
            margin: 0 auto;
            background: var(--myads-surface-border, #e2e8f0);
            height: 8px;
            border-radius: 9999px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .help-rating-progress {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #23d2e2);
            transition: width 0.6s ease;
        }

        .help-rating-feedback-msg {
            display: none;
            padding: 10px 16px;
            border-radius: 12px;
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            font-weight: 700;
            font-size: 0.9rem;
            margin-top: 14px;
        }

        /* Nav Footer (Prev/Next) */
        .help-nav-footer {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid var(--myads-surface-border, #f1f5f9);
        }

        .help-nav-card {
            background: var(--myads-surface-elevated, #f8faff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: 16px;
            padding: 18px 20px;
            text-decoration: none;
            color: var(--myads-text-main, #1e293b);
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
        }

        [data-theme="css_d"] .help-nav-card {
            background: rgba(33, 40, 59, 0.4);
        }

        .help-nav-card:hover {
            border-color: var(--myads-primary, #615dfa);
            transform: translateY(-2px);
            color: var(--myads-text-main, #1e293b);
        }

        .help-nav-direction {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--myads-primary, #615dfa);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .help-nav-title {
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.4;
        }

        /* Sidebar Widgets */
        .help-side-widget {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 18px);
            padding: 22px;
            margin-bottom: 24px;
        }

        .help-side-widget-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--myads-surface-border, #f1f5f9);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .help-toc-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .help-toc-item {
            margin-bottom: 10px;
        }

        .help-toc-link {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.88rem;
            color: var(--myads-text-muted, #64748b);
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .help-toc-link:hover {
            background: rgba(97, 93, 250, 0.08);
            color: var(--myads-primary, #615dfa);
        }

        @media (max-width: 992px) {
            .help-article-container { grid-template-columns: 1fr; }
            .help-article-main { padding: 24px 18px; }
            .help-article-headline { font-size: 1.6rem; }
            .help-nav-footer { grid-template-columns: 1fr; }
        }
    </style>

    <!-- Breadcrumbs -->
    <div class="help-breadcrumbs">
        <a href="{{ url('/') }}"><i class="fa-solid fa-house"></i> {{ __('help.home') }}</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <a href="{{ route('help.index') }}">{{ __('help.help_center') }}</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <a href="{{ route('help.category', $categoryData['slug']) }}">{{ $categoryData['title'] }}</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <span style="color: var(--myads-primary, #615dfa); font-weight: 700;">{{ $articleData['title'] }}</span>
    </div>

    <div class="help-article-container">
        <!-- Main Article Content -->
        <article class="help-article-main">
            <div class="help-article-hero-badge">
                <i class="{{ $categoryData['icon'] }}"></i>
                {{ $categoryData['title'] }}
            </div>

            <h1 class="help-article-headline">{{ $articleData['title'] }}</h1>

            <div class="help-article-meta-bar">
                <span>
                    <i class="fa-regular fa-clock"></i>
                    {{ __('help.read_time', ['minutes' => $articleData['read_time']]) }}
                </span>
                <span>
                    <i class="fa-solid fa-tag"></i>
                    {{ $articleData['badge'] }}
                </span>
                <span id="rating-pill-display" style="color: #10b981; font-weight: 700;">
                    <i class="fa-solid fa-thumbs-up"></i>
                    <span id="rating-ratio-text">
                        @if(($articleData['rating']['total'] ?? 0) > 0)
                            {{ __('help.helpful_ratio', ['percent' => $articleData['rating']['percentage'], 'count' => $articleData['rating']['total']]) }}
                        @else
                            {{ __('help.no_ratings_yet') }}
                        @endif
                    </span>
                </span>
            </div>

            <!-- Summary Box -->
            <div class="help-summary-box">
                {{ $articleData['summary'] }}
            </div>

            <!-- Action Shortcut Card -->
            @if(!empty($articleData['route_link']))
                <div class="help-shortcut-card">
                    <div class="help-shortcut-text">
                        <i class="fa-solid fa-compass" style="font-size: 1.3rem; color: var(--myads-primary, #615dfa);"></i>
                        <span>{{ $articleData['route_label'] ?? __('help.open_feature') }}</span>
                    </div>
                    <a href="{{ $articleData['route_link'] }}" class="btn-shortcut-action">
                        <span>{{ __('help.open_feature') }}</span>
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            @endif

            <!-- Step by Step Instructions -->
            <div class="help-steps-container">
                <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--myads-text-main, #1e293b); margin-bottom: 20px;">
                    <i class="fa-solid fa-list-check" style="color: var(--myads-primary, #615dfa); margin-right: 8px;"></i>
                    {{ __('help.table_of_contents') }}
                </h2>

                @foreach($articleData['steps'] as $idx => $step)
                    <div class="help-step-item">
                        <div class="help-step-number">{{ $idx + 1 }}</div>
                        <div class="help-step-content">{{ $step }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Pro Tip Callout -->
            @if(!empty($articleData['pro_tip']))
                <div class="help-callout-tip">
                    <i class="fa-solid fa-lightbulb"></i>
                    <div>
                        <strong style="display: block; font-size: 1.05rem; color: #10b981; margin-bottom: 4px;">
                            {{ __('help.pro_tip') }}
                        </strong>
                        <p style="margin: 0; color: var(--myads-text-main, #1e293b); font-size: 0.95rem; line-height: 1.6;">
                            {{ $articleData['pro_tip'] }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Important Note Callout -->
            @if(!empty($articleData['note']))
                <div class="help-callout-note">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>
                        <strong style="display: block; font-size: 1.05rem; color: #f59e0b; margin-bottom: 4px;">
                            {{ __('help.important_note') }}
                        </strong>
                        <p style="margin: 0; color: var(--myads-text-main, #1e293b); font-size: 0.95rem; line-height: 1.6;">
                            {{ $articleData['note'] }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Rating & Helpfulness Widget -->
            <div class="help-rating-widget" id="help-rating-box">
                <h3 class="help-rating-title">{{ __('help.was_helpful') }}</h3>
                <p class="help-rating-subtitle">
                    {{ __('help.feedback_thank_you', ['site' => $siteName]) }}
                </p>

                <div class="help-rating-buttons">
                    <button type="button"
                            class="btn-vote vote-yes {{ ($articleData['rating']['user_voted'] ?? '') === 'yes' ? 'active' : '' }}"
                            id="btn-vote-yes"
                            onclick="submitHelpVote(true)">
                        <i class="fa-solid fa-thumbs-up"></i>
                        <span>{{ __('help.yes_helpful') }}</span>
                    </button>
                    <button type="button"
                            class="btn-vote vote-no {{ ($articleData['rating']['user_voted'] ?? '') === 'no' ? 'active' : '' }}"
                            id="btn-vote-no"
                            onclick="submitHelpVote(false)">
                        <i class="fa-solid fa-thumbs-down"></i>
                        <span>{{ __('help.no_not_helpful') }}</span>
                    </button>
                </div>

                <div class="help-rating-stats-bar">
                    <div class="help-rating-progress"
                         id="rating-progress-bar"
                         style="width: {{ $articleData['rating']['percentage'] ?? 100 }}%;"></div>
                </div>

                <div id="vote-feedback-toast" class="help-rating-feedback-msg"></div>
            </div>

            <!-- Navigation: Previous & Next -->
            <div class="help-nav-footer">
                @if($articleData['prev_article'])
                    <a href="{{ route('help.article', ['category' => $articleData['prev_article']['category'], 'article' => $articleData['prev_article']['slug']]) }}" class="help-nav-card">
                        <span class="help-nav-direction">
                            <i class="fa-solid fa-arrow-left"></i>
                            {{ __('pagination.previous') }}
                        </span>
                        <span class="help-nav-title">{{ $articleData['prev_article']['title'] }}</span>
                    </a>
                @else
                    <div></div>
                @endif

                @if($articleData['next_article'])
                    <a href="{{ route('help.article', ['category' => $articleData['next_article']['category'], 'article' => $articleData['next_article']['slug']]) }}" class="help-nav-card" style="text-align: right;">
                        <span class="help-nav-direction" style="justify-content: flex-end;">
                            {{ __('pagination.next') }}
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>
                        <span class="help-nav-title">{{ $articleData['next_article']['title'] }}</span>
                    </a>
                @endif
            </div>
        </article>

        <!-- Sidebar Widgets -->
        <aside>
            <!-- In This Category -->
            <div class="help-side-widget">
                <div class="help-side-widget-title">
                    <i class="fa-solid fa-folder-open" style="color: var(--myads-primary, #615dfa);"></i>
                    {{ $categoryData['title'] }}
                </div>
                <ul class="help-toc-list">
                    @foreach($helpService->getArticles($categoryData['slug']) as $sibling)
                        <li class="help-toc-item">
                            <a href="{{ route('help.article', ['category' => $sibling['category'], 'article' => $sibling['slug']]) }}"
                               class="help-toc-link {{ $sibling['slug'] === $articleData['slug'] ? 'active' : '' }}"
                               style="{{ $sibling['slug'] === $articleData['slug'] ? 'color: var(--myads-primary, #615dfa); font-weight: 700; background: rgba(97,93,250,0.08);' : '' }}">
                                <i class="fa-regular fa-file-lines" style="font-size: 0.85rem;"></i>
                                <span style="flex: 1;">{{ $sibling['title'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Categories Quick Switch -->
            <div class="help-side-widget">
                <div class="help-side-widget-title">
                    <i class="fa-solid fa-layer-group" style="color: var(--myads-primary, #615dfa);"></i>
                    {{ __('help.all_categories') }}
                </div>
                <ul class="help-toc-list">
                    @foreach($categories as $cat)
                        <li class="help-toc-item">
                            <a href="{{ route('help.category', $cat['slug']) }}" class="help-toc-link">
                                <i class="{{ $cat['icon'] }}" style="font-size: 0.85rem; width: 16px;"></i>
                                <span style="flex: 1;">{{ $cat['title'] }}</span>
                                <span style="font-size: 0.75rem; background: rgba(97,93,250,0.08); padding: 2px 7px; border-radius: 9999px;">
                                    {{ $cat['article_count'] }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Help Support Box -->
            <div class="help-side-widget" style="background: linear-gradient(135deg, rgba(97, 93, 250, 0.1), rgba(35, 210, 226, 0.1)); border-color: rgba(97, 93, 250, 0.25);">
                <div style="font-weight: 800; font-size: 1.1rem; color: var(--myads-text-main, #1e293b); margin-bottom: 6px;">
                    <i class="fa-solid fa-circle-question" style="color: var(--myads-primary, #615dfa); margin-right: 6px;"></i>
                    {{ __('help.need_more_help') }}
                </div>
                <p style="font-size: 0.85rem; color: var(--myads-text-muted, #64748b); line-height: 1.5; margin-bottom: 16px;">
                    {{ __('help.need_more_help_desc', ['site' => $siteName]) }}
                </p>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('support.index') }}" class="btn-shortcut-action" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-comments"></i>
                        {{ __('help.ask_community') }}
                    </a>
                    <a href="{{ route('tickets.index') }}" class="btn-shortcut-action" style="width: 100%; justify-content: center; background: linear-gradient(135deg, #615dfa, #23d2e2); color: #fff; border-color: transparent;">
                        <i class="fa-solid fa-headset"></i>
                        {{ __('help.contact_support') }}
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>

<!-- AJAX Voting Script -->
<script>
function submitHelpVote(isHelpful) {
    const slug = '{{ $articleData['slug'] }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const btnYes = document.getElementById('btn-vote-yes');
    const btnNo = document.getElementById('btn-vote-no');
    const toast = document.getElementById('vote-feedback-toast');
    const progressBar = document.getElementById('rating-progress-bar');
    const ratioText = document.getElementById('rating-ratio-text');

    fetch('{{ route('help.vote') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            article_slug: slug,
            is_helpful: isHelpful
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.stats) {
            // Update button active states
            if (isHelpful) {
                btnYes.classList.add('active');
                btnNo.classList.remove('active');
            } else {
                btnNo.classList.add('active');
                btnYes.classList.remove('active');
            }

            // Update progress bar
            if (progressBar) {
                progressBar.style.width = data.stats.percentage + '%';
            }

            // Update ratio text
            if (ratioText) {
                ratioText.textContent = data.stats.percentage + '% ' + '({{ __('help.helpful_ratio', ['percent' => ':p', 'count' => ':c']) }}'
                    .replace(':p', data.stats.percentage)
                    .replace(':c', data.stats.total) + ')';
            }

            // Display toast feedback
            if (toast) {
                toast.textContent = data.message;
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 5000);
            }
        }
    })
    .catch(err => {
        console.error('Rating error:', err);
    });
}
</script>
@endsection
