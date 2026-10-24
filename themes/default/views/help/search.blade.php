@extends('theme::layouts.master')

@section('title', $pageTitle ?? (__('help.search_results_for', ['query' => $query]) . ' - ' . $siteName))

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

        .help-search-header-box {
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.1) 0%, rgba(35, 210, 226, 0.08) 100%);
            border: 1px solid var(--myads-surface-border, rgba(97, 93, 250, 0.15));
            border-radius: var(--myads-radius-xl, 24px);
            padding: 36px 32px;
            margin-bottom: 36px;
        }

        .help-search-input-wrap {
            display: flex;
            align-items: center;
            background: var(--myads-surface, #ffffff);
            border: 2px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-pill, 9999px);
            padding: 6px 10px 6px 20px;
            box-shadow: 0 10px 30px rgba(97, 93, 250, 0.1);
            max-width: 600px;
            margin-top: 20px;
        }

        [dir="rtl"] .help-search-input-wrap {
            padding: 6px 20px 6px 10px;
        }

        .help-search-input-wrap:focus-within {
            border-color: var(--myads-primary, #615dfa);
        }

        .help-search-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 1rem;
            color: var(--myads-text-main, #1e293b);
            padding: 8px;
        }

        .help-search-btn {
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .help-search-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
        }

        .help-search-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 18px);
            padding: 24px;
            margin-bottom: 18px;
            text-decoration: none;
            color: var(--myads-text-main, #1e293b);
            display: block;
            transition: all 0.25s ease;
        }

        .help-search-card:hover {
            border-color: var(--myads-primary, #615dfa);
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(97, 93, 250, 0.09);
            color: var(--myads-text-main, #1e293b);
        }

        @media (max-width: 860px) {
            .help-search-layout { grid-template-columns: 1fr; }
        }
    </style>

    <!-- Breadcrumbs -->
    <div class="help-breadcrumbs">
        <a href="{{ url('/') }}"><i class="fa-solid fa-house"></i> {{ __('help.home') }}</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <a href="{{ route('help.index') }}">{{ __('help.help_center') }}</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <span style="color: var(--myads-primary, #615dfa); font-weight: 700;">
            {{ __('help.search_results_for', ['query' => $query]) }}
        </span>
    </div>

    <!-- Search Header Box -->
    <div class="help-search-header-box">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--myads-text-main, #1e293b); margin-bottom: 6px;">
            {{ __('help.search_results_for', ['query' => $query]) }}
        </h1>
        <p style="color: var(--myads-text-muted, #64748b); font-size: 0.95rem; margin: 0;">
            {{ __('help.search_count', ['count' => count($results)]) }}
        </p>

        <form action="{{ route('help.search') }}" method="GET">
            <div class="help-search-input-wrap">
                <i class="fa-solid fa-magnifying-glass" style="color: var(--myads-primary, #615dfa); margin: 0 8px;"></i>
                <input type="text"
                       name="q"
                       class="help-search-input"
                       value="{{ $query }}"
                       placeholder="{{ __('help.search_placeholder', ['site' => $siteName]) }}"
                       autocomplete="off">
                <button type="submit" class="help-search-btn">{{ __('messages.search') }}</button>
            </div>
        </form>
    </div>

    <div class="help-search-layout">
        <!-- Categories Sidebar -->
        <div style="background: var(--myads-surface, #ffffff); border: 1px solid var(--myads-surface-border, #e2e8f0); border-radius: 18px; padding: 20px 16px; height: fit-content;">
            <div style="font-size: 0.95rem; font-weight: 800; padding: 8px 12px 14px; border-bottom: 1px solid var(--myads-surface-border, #f1f5f9); margin-bottom: 10px;">
                <i class="fa-solid fa-layer-group" style="color: var(--myads-primary, #615dfa); margin-right: 6px;"></i>
                {{ __('help.all_categories') }}
            </div>
            @foreach($categories as $cat)
                <a href="{{ route('help.category', $cat['slug']) }}"
                   style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; border-radius: 12px; color: var(--myads-text-main, #1e293b); text-decoration: none; font-size: 0.9rem; font-weight: 600; margin-bottom: 4px; transition: background 0.2s;"
                   onmouseover="this.style.background='rgba(97,93,250,0.08)'"
                   onmouseout="this.style.background='transparent'">
                    <span>
                        <i class="{{ $cat['icon'] }}" style="margin-right: 6px; width: 18px;"></i>
                        {{ $cat['title'] }}
                    </span>
                    <span style="background: rgba(97,93,250,0.1); color: var(--myads-primary, #615dfa); padding: 2px 8px; border-radius: 9999px; font-size: 0.75rem;">
                        {{ $cat['article_count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        <!-- Results List -->
        <div>
            @forelse($results as $art)
                <a href="{{ route('help.article', ['category' => $art['category'], 'article' => $art['slug']]) }}" class="help-search-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--myads-primary, #615dfa); background: rgba(97,93,250,0.08); padding: 3px 10px; border-radius: 6px;">
                            <i class="{{ $art['icon'] }}" style="margin-right: 4px;"></i>
                            {{ $art['category_data']['title'] ?? '' }}
                        </span>
                        <span style="font-size: 0.8rem; color: var(--myads-text-muted, #64748b);">
                            <i class="fa-regular fa-clock"></i>
                            {{ __('help.read_time', ['minutes' => $art['read_time']]) }}
                        </span>
                    </div>

                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 8px; color: var(--myads-text-main, #1e293b);">
                        {{ $art['title'] }}
                    </h3>

                    <p style="color: var(--myads-text-muted, #64748b); font-size: 0.92rem; line-height: 1.6; margin-bottom: 14px;">
                        {{ $art['summary'] }}
                    </p>

                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--myads-surface-border, #f1f5f9); padding-top: 12px; font-size: 0.82rem;">
                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                            @foreach($art['tags'] as $tag)
                                <span style="background: rgba(97,93,250,0.06); color: var(--myads-primary, #615dfa); padding: 2px 8px; border-radius: 4px; font-weight: 600;">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                        <span style="color: var(--myads-primary, #615dfa); font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                            {{ __('help.table_of_contents') }}
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </div>
                </a>
            @empty
                <div style="text-align: center; padding: 60px 20px; background: var(--myads-surface, #fff); border-radius: 18px; border: 1px dashed var(--myads-surface-border, #cbd5e1);">
                    <i class="fa-solid fa-magnifying-glass" style="font-size: 3rem; color: var(--myads-text-muted, #94a3b8); margin-bottom: 16px;"></i>
                    <h3 style="font-size: 1.25rem; color: var(--myads-text-main, #1e293b); font-weight: 800; margin-bottom: 8px;">
                        {{ __('help.no_results', ['query' => $query]) }}
                    </h3>
                    <p style="color: var(--myads-text-muted, #64748b); font-size: 0.95rem; max-width: 480px; margin: 0 auto 20px;">
                        {{ __('help.try_other_keywords') }}
                    </p>
                    <a href="{{ route('help.index') }}" class="help-search-btn" style="text-decoration: none; display: inline-block;">
                        {{ __('help.back_to_help') }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
