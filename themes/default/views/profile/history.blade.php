@extends('theme::layouts.master')

@section('content')
    @php
        $posTotal = isset($positiveTotal) ? (float) $positiveTotal : collect($history->items())->sum(fn($item) => max(0, (float) $item->amount));
        $negTotal = isset($negativeTotal) ? abs((float) $negativeTotal) : abs(collect($history->items())->sum(fn($item) => min(0, (float) $item->amount)));
        $totalCount = isset($totalTransactions) ? (int) $totalTransactions : (method_exists($history, 'total') ? $history->total() : $history->count());
        $netBalance = $posTotal - $negTotal;
        $currentFilter = $activeFilter ?? request('filter', 'all');
        $currentSearch = $activeSearch ?? request('search', '');
    @endphp

    <div class="section-banner">
        <p class="section-banner-title">{{ __('messages.pts_history') }}</p>
    </div>

    <div class="grid grid-3-9 mobile-prefer-content">
        <div class="grid-column">
            @include('theme::profile.settings_nav')
        </div>

        <div class="grid-column">
            <div class="points-superdesign-shell">
                <!-- Modern Superdesign Hero Header -->
                <div class="points-hero-card">
                    <div class="points-hero-inner">
                        <div class="points-hero-text">
                            <div class="points-hero-badge">
                                <i class="fa-solid fa-coins"></i>
                                <span>{{ __('messages.pts_history_title') }}</span>
                            </div>
                            <h3 class="points-hero-title">{{ __('messages.pts_history') }}</h3>
                            <p class="points-hero-subtitle">{{ __('messages.pts_history_desc') }}</p>

                            <!-- Quick Wallet Actions -->
                            <div class="points-hero-actions">
                                <a href="{{ route('profile.history.export', ['filter' => $currentFilter, 'search' => $currentSearch]) }}"
                                    id="ptsExportBtn" class="points-hero-action-btn is-export" download>
                                    <i class="fa-solid fa-file-arrow-down"></i>
                                    <span>{{ __('messages.pts_export_statement') }}</span>
                                </a>
                                <a href="{{ route('dashboard') }}#quick-transfer"
                                    class="points-hero-action-btn is-transfer">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    <span>{{ __('messages.pts_quick_transfer') }}</span>
                                </a>
                                <a href="{{ route('visits.surf') }}" class="points-hero-action-btn is-earn">
                                    <i class="fa-solid fa-bolt"></i>
                                    <span>{{ __('messages.pts_earn_more') }}</span>
                                </a>
                            </div>
                        </div>

                        <!-- Live KPI Counters -->
                        <div class="points-kpi-group">
                            <div class="points-kpi-pill is-deposit">
                                <span class="kpi-label">{{ __('messages.pts_total_earned') }}</span>
                                <span class="kpi-value is-positive" id="positivePointsTotal">
                                    +{{ rtrim(rtrim(number_format($posTotal, 2), '0'), '.') }}
                                </span>
                            </div>
                            <div class="points-kpi-pill is-deduct">
                                <span class="kpi-label">{{ __('messages.pts_total_spent') }}</span>
                                <span class="kpi-value is-negative" id="negativePointsTotal">
                                    -{{ rtrim(rtrim(number_format($negTotal, 2), '0'), '.') }}
                                </span>
                            </div>
                            <div class="points-kpi-pill is-net">
                                <span class="kpi-label">{{ __('messages.pts_net_balance') }}</span>
                                <span class="kpi-value {{ $netBalance >= 0 ? 'is-positive' : 'is-negative' }}"
                                    id="netPointsBalance">
                                    {{ $netBalance > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($netBalance, 2), '0'), '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Abstract Visual Ornaments -->
                    <div class="points-hero-orb orb-1"></div>
                    <div class="points-hero-orb orb-2"></div>
                    <i class="fa-solid fa-receipt points-hero-watermark"></i>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger" role="alert" style="border-radius: 14px; margin-bottom: 20px;">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success" role="alert" style="border-radius: 14px; margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(!empty($upgradeNotice))
                    @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
                @endif

                <!-- Main Points Ledger Card with AJAX Dynamic Wrapper -->
                <div class="widget-box points-ledger-card position-relative">
                    <div class="widget-box-header">
                        <p class="widget-box-title">
                            <i class="fa-solid fa-list-check"
                                style="margin-inline-end: 8px; color: var(--primary-color, #23d2e2);"></i>
                            {{ __('messages.pts_history_title') }}
                        </p>
                        <span class="badge points-counter-badge" id="pointsTotalTransactions">
                            <span id="pointsTotalCountNum">{{ $totalCount }}</span> {{ __('messages.pts_transaction_id') }}
                        </span>
                    </div>

                    <!-- Interactive Filter & Search Bar -->
                    <div class="points-filter-bar">
                        <div class="points-chips-group" id="ptsFilterChips">
                            <button type="button" class="pts-filter-chip {{ $currentFilter === 'all' ? 'is-active' : '' }}"
                                data-filter="all">
                                <i class="fa-solid fa-layer-group"></i>
                                <span>{{ __('messages.pts_filter_all') }}</span>
                            </button>
                            <button type="button"
                                class="pts-filter-chip {{ $currentFilter === 'credits' ? 'is-active' : '' }}"
                                data-filter="credits">
                                <i class="fa-solid fa-arrow-down-left"></i>
                                <span>{{ __('messages.pts_filter_credits') }}</span>
                            </button>
                            <button type="button"
                                class="pts-filter-chip {{ $currentFilter === 'debits' ? 'is-active' : '' }}"
                                data-filter="debits">
                                <i class="fa-solid fa-arrow-up-right"></i>
                                <span>{{ __('messages.pts_filter_debits') }}</span>
                            </button>
                            <button type="button"
                                class="pts-filter-chip {{ $currentFilter === 'transfers' ? 'is-active' : '' }}"
                                data-filter="transfers">
                                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                <span>{{ __('messages.pts_filter_transfers') }}</span>
                            </button>
                            <button type="button" class="pts-filter-chip {{ $currentFilter === 'ads' ? 'is-active' : '' }}"
                                data-filter="ads">
                                <i class="fa-solid fa-rectangle-ad"></i>
                                <span>{{ __('messages.pts_filter_ads') }}</span>
                            </button>
                            <button type="button"
                                class="pts-filter-chip {{ $currentFilter === 'rewards' ? 'is-active' : '' }}"
                                data-filter="rewards">
                                <i class="fa-solid fa-gift"></i>
                                <span>{{ __('messages.pts_filter_rewards') }}</span>
                            </button>
                        </div>

                        <div class="points-search-wrap">
                            <i class="fa-solid fa-magnifying-glass points-search-icon"></i>
                            <input type="text" id="ptsSearchInput" class="points-search-input"
                                placeholder="{{ __('messages.pts_search_placeholder') }}" value="{{ $currentSearch }}"
                                autocomplete="off">
                            <button type="button" id="ptsSearchClear" class="points-search-clear"
                                style="{{ !empty($currentSearch) ? 'display: flex;' : 'display: none;' }}" title="Clear">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <div class="widget-box-content points-ledger-content">
                        <!-- Loading Overlay for AJAX Pagination & Filtering -->
                        <div id="pointsLoadingOverlay" class="points-loading-overlay" style="display: none;">
                            <div class="points-spinner">
                                <div class="spinner-ring"></div>
                                <span>{{ __('messages.loading') ?? 'Loading...' }}</span>
                            </div>
                        </div>

                        <!-- Dynamic Partial Container -->
                        <div id="pointsDynamicContent">
                            @include('theme::profile.partials.history_list', [
                                'history' => $history,
                                'featureAvailable' => $featureAvailable ?? true,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Superdesign Points History Styling */
        .points-superdesign-shell {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .points-hero-card {
            background: linear-gradient(135deg, #615dfa 0%, #4640de 50%, #2e28a5 100%);
            padding: 34px 38px;
            border-radius: 24px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 14px 34px rgba(97, 93, 250, 0.22);
        }

        .points-hero-inner {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 24px;
        }

        .points-hero-text {
            max-width: 560px;
        }

        .points-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 14px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
            border: 1px solid rgba(255, 255, 255, 0.22);
        }

        .points-hero-title {
            font-weight: 800;
            font-size: 26px;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
            color: #ffffff !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .points-hero-subtitle {
            color: rgba(255, 255, 255, 0.95) !important;
            font-size: 14.5px;
            line-height: 1.6;
            margin: 0;
            font-weight: 500;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Hero Quick Actions */
        .points-hero-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .points-hero-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.16);
        }

        .points-hero-action-btn:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.28);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            color: #ffffff !important;
        }

        .points-hero-action-btn.is-export {
            background: rgba(16, 185, 129, 0.28);
            border-color: rgba(16, 185, 129, 0.45);
        }

        .points-hero-action-btn.is-export:hover {
            background: rgba(16, 185, 129, 0.42);
        }

        .points-hero-action-btn.is-earn {
            background: rgba(245, 158, 11, 0.28);
            border-color: rgba(245, 158, 11, 0.45);
        }

        .points-hero-action-btn.is-earn:hover {
            background: rgba(245, 158, 11, 0.42);
        }

        .points-kpi-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .points-kpi-pill {
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.28);
            padding: 12px 18px;
            border-radius: 18px;
            min-width: 120px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transition: transform 0.25s ease, background 0.25s ease;
        }

        .points-kpi-pill:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.24);
        }

        .points-kpi-pill .kpi-label {
            display: block;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.95) !important;
            font-weight: 700;
            margin-bottom: 4px;
            white-space: nowrap;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
        }

        .points-kpi-pill .kpi-value {
            display: block;
            font-size: 20px;
            font-weight: 800;
            line-height: 1.1;
            color: #ffffff;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .points-kpi-pill .kpi-value.is-positive {
            color: #6ee7b7;
        }

        .points-kpi-pill .kpi-value.is-negative {
            color: #fca5a5;
        }

        .points-hero-orb {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .points-hero-orb.orb-1 {
            top: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            background: rgba(255, 255, 255, 0.1);
        }

        .points-hero-orb.orb-2 {
            bottom: -40px;
            left: 14%;
            width: 130px;
            height: 130px;
            background: rgba(255, 255, 255, 0.05);
        }

        .points-hero-watermark {
            position: absolute;
            right: 25px;
            bottom: -28px;
            font-size: 140px;
            color: rgba(255, 255, 255, 0.07);
            transform: rotate(-10deg);
            pointer-events: none;
        }

        /* Ledger Card Styling */
        .points-ledger-card {
            border-radius: 24px;
            border: 1px solid var(--border-color, #eaeaf5);
            background: var(--widget-box-bg, #fff);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .points-ledger-card .widget-box-header {
            padding: 22px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color, #ebebeb);
        }

        .points-ledger-card .widget-box-header .widget-box-title {
            margin: 0;
            font-size: 17px;
            font-weight: 800;
            color: var(--text-color, #283c50);
            display: flex;
            align-items: center;
        }

        .points-counter-badge {
            background: var(--dark-light-color, #f4f6fa);
            color: var(--text-color-alt, #6b7280);
            border: 1px solid var(--border-color, #ebebeb);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

    /* Interactive Filter & Search Bar - Compact & Streamlined */
    .points-filter-bar {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        padding: 10px 20px !important;
        border-bottom: 1px solid var(--border-color, #ebebeb) !important;
        background: var(--dark-light-color, #fafbfe) !important;
        flex-wrap: wrap !important;
        min-height: 50px !important;
    }

    .points-chips-group {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        flex-wrap: wrap !important;
        flex: 1 1 auto !important;
        margin: 0 !important;
    }

    .pts-filter-chip {
        width: auto !important;
        max-width: max-content !important;
        flex: 0 0 auto !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        padding: 5px 13px !important;
        height: 32px !important;
        min-height: 32px !important;
        border-radius: 20px !important;
        border: 1px solid var(--border-color, #e2e8f0) !important;
        background: var(--widget-box-bg, #ffffff) !important;
        color: var(--text-color-alt, #64748b) !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        white-space: nowrap !important;
        line-height: 1 !important;
        box-shadow: none !important;
        margin: 0 !important;
    }

    .pts-filter-chip:hover {
        border-color: var(--primary-color, #615dfa) !important;
        color: var(--primary-color, #615dfa) !important;
        transform: translateY(-1px);
    }

    .pts-filter-chip.is-active {
        background: var(--primary-color, #615dfa) !important;
        border-color: var(--primary-color, #615dfa) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(97, 93, 250, 0.25) !important;
    }

    .points-search-wrap {
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        width: 220px !important;
        max-width: 240px !important;
        flex: 0 0 220px !important;
        margin: 0 !important;
    }

    .points-search-icon {
        position: absolute;
        left: 10px;
        color: var(--text-color-alt, #94a3b8);
        font-size: 12px;
        pointer-events: none;
    }

    [dir="rtl"] .points-search-icon {
        left: auto;
        right: 10px;
    }

    .points-search-input {
        width: 100% !important;
        height: 32px !important;
        min-height: 32px !important;
        padding: 4px 28px !important;
        border-radius: 16px !important;
        border: 1px solid var(--border-color, #e2e8f0) !important;
        background: var(--widget-box-bg, #ffffff) !important;
        color: var(--text-color, #1e293b) !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        outline: none !important;
        transition: border-color 0.2s, box-shadow 0.2s !important;
    }

    .points-search-input:focus {
        border-color: var(--primary-color, #615dfa) !important;
        box-shadow: 0 0 0 3px rgba(97, 93, 250, 0.15) !important;
    }

    .points-search-clear {
        position: absolute;
        right: 8px;
        background: none;
        border: none;
        color: var(--text-color-alt, #94a3b8);
        cursor: pointer;
        font-size: 11px;
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background 0.15s, color 0.15s;
    }

    [dir="rtl"] .points-search-clear {
        right: auto;
        left: 8px;
    }

        .points-search-clear:hover {
            background: var(--dark-light-color, #e2e8f0);
            color: var(--text-color, #1e293b);
        }

        .points-ledger-content {
            padding: 24px 28px 30px;
        }

        /* Table & Row Styling */
        .points-table-responsive {
            border-radius: 16px;
            border: 1px solid var(--border-color, #f0f2f8);
            overflow-x: auto;
        }

        .points-history-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--widget-box-bg, #fff);
        }

        .points-history-table th {
            background: var(--dark-light-color, #f8faff);
            color: var(--text-color-alt, #7b819d);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color, #eaedf5);
        }

        .points-history-table td {
            padding: 18px 20px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color, #f0f2f8);
            transition: background 0.15s ease;
        }

        .points-history-row:hover td {
            background: rgba(97, 93, 250, 0.02);
        }

        .points-id-badge {
            display: inline-block;
            font-family: monospace;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-color-alt, #7b819d);
            background: var(--dark-light-color, #f4f6fa);
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid var(--border-color, #e5e7eb);
        }

        .points-history-entry {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .points-entry-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-grid;
            place-items: center;
            flex-shrink: 0;
            font-size: 16px;
            transition: transform 0.2s ease;
        }

        .points-history-row:hover .points-entry-icon {
            transform: scale(1.08);
        }

        .points-entry-icon.is-positive {
            background: #eefdf3;
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .points-entry-icon.is-negative {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .points-entry-details {
            min-width: 0;
        }

        .points-entry-title {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--text-color, #283c50);
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .points-entry-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .points-flow-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .points-flow-tag.is-positive {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .points-flow-tag.is-negative {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .points-flow-tag.is-type {
            background: rgba(97, 93, 250, 0.08);
            color: var(--primary-color, #615dfa);
            font-size: 10px;
            letter-spacing: 0.04em;
        }

        .points-flow-tag.is-legacy {
            background: var(--dark-light-color, #f3f4f6);
            color: var(--text-color-alt, #6b7280);
        }

        .points-date-wrap {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-color-alt, #7b819d);
        }

        .points-date-wrap i {
            color: var(--primary-color, #615dfa);
            opacity: 0.7;
        }

        .points-amount-badge {
            display: inline-flex;
            align-items: baseline;
            gap: 4px;
            font-size: 17px;
            font-weight: 800;
            font-family: inherit;
        }

        .points-amount-badge.is-positive {
            color: #10b981;
        }

        .points-amount-badge.is-negative {
            color: #ef4444;
        }

        .points-running-balance-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 14px;
            font-weight: 700;
            color: var(--primary-color, #615dfa);
            background: rgba(97, 93, 250, 0.08);
            padding: 5px 12px;
            border-radius: 10px;
            border: 1px solid rgba(97, 93, 250, 0.18);
            font-family: inherit;
        }

        .points-unit {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            opacity: 0.8;
        }

        /* Empty state */
        .points-history-empty {
            padding: 56px 20px;
            text-align: center;
        }

        .points-empty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--dark-light-color, #f4f6fa);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            color: var(--border-color, #ccc);
            margin-bottom: 16px;
        }

        .points-empty-title {
            font-size: 17.5px;
            font-weight: 800;
            color: var(--text-color, #283c50);
            margin-bottom: 6px;
        }

        .points-empty-desc {
            color: var(--text-color-alt, #888);
            font-size: 14px;
            max-width: 440px;
            margin: 0 auto;
        }

        /* Pagination controls */
        .points-pagination-holder {
            margin-top: 26px;
            display: flex;
            justify-content: center;
        }

        .points-pagination-holder .pagination {
            display: flex;
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 0;
            align-items: center;
        }

        .points-pagination-holder .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 14px;
            border-radius: 12px;
            background: var(--widget-box-bg, #fff);
            border: 1px solid var(--border-color, #ebebeb);
            color: var(--text-color, #333);
            font-weight: 700;
            font-size: 13.5px;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        .points-pagination-holder .page-item.active .page-link {
            background: var(--primary-color, #615dfa);
            border-color: var(--primary-color, #615dfa);
            color: #fff;
            box-shadow: 0 4px 14px rgba(97, 93, 250, 0.35);
        }

        .points-pagination-holder .page-item:not(.active):not(.disabled) .page-link:hover {
            background: var(--dark-light-color, #f4f6fa);
            border-color: var(--primary-color, #615dfa);
            color: var(--primary-color, #615dfa);
            transform: translateY(-2px);
        }

        .points-pagination-holder .page-item.disabled .page-link {
            opacity: 0.45;
            cursor: not-allowed;
        }

        /* Loading overlay */
        .points-loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: opacity 0.2s ease;
        }

        .points-spinner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-color, #333);
        }

        .spinner-ring {
            width: 38px;
            height: 38px;
            border: 3.5px solid rgba(97, 93, 250, 0.2);
            border-top-color: var(--primary-color, #615dfa);
            border-radius: 50%;
            animation: pointsSpin 0.75s linear infinite;
        }

        @keyframes pointsSpin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Dark Mode Theme Parity */
        html[data-theme="css_d"] .points-loading-overlay {
            background: rgba(29, 35, 51, 0.78);
        }

        html[data-theme="css_d"] .points-filter-bar {
            background: rgba(255, 255, 255, 0.02);
        }

        html[data-theme="css_d"] .pts-filter-chip {
            background: #1f2738;
            border-color: #2b364d;
            color: #94a3b8;
        }

        .points-filter-bar {
            padding: 8px 14px !important;
            gap: 8px !important;
        }

        .points-chips-group {
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important;
            padding-bottom: 2px !important;
            width: 100% !important;
        }

        .points-chips-group::-webkit-scrollbar {
            display: none !important;
        }

        .points-search-wrap {
            width: 100% !important;
            max-width: 100% !important;
            flex: 1 1 100% !important;
        }

        html[data-theme="css_d"] .points-running-balance-badge {
            background: rgba(97, 93, 250, 0.16);
            border-color: rgba(97, 93, 250, 0.3);
            color: #a5b4fc;
        }

        /* RTL Support */
        [dir="rtl"] .points-entry-details {
            text-align: right;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .points-hero-card {
                padding: 26px 20px;
                border-radius: 20px;
            }

            .points-hero-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .points-hero-actions {
                width: 100%;
            }

            .points-hero-action-btn {
                flex: 1 1 calc(50% - 6px);
                justify-content: center;
            }

            .points-kpi-group {
                width: 100%;
            }

            .points-kpi-pill {
                flex: 1 1 calc(33.333% - 8px);
                min-width: 90px;
                padding: 10px 12px;
            }

            .points-kpi-pill .kpi-value {
                font-size: 17px;
            }

            .points-filter-bar {
                padding: 14px 16px;
                flex-direction: column;
                align-items: stretch;
            }

            .points-search-wrap {
                max-width: none;
                width: 100%;
            }

            .points-ledger-content {
                padding: 16px 14px 24px;
            }

            .points-history-table th,
            .points-history-table td {
                padding: 12px 14px;
            }

            .points-entry-icon {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dynamicContainer = document.getElementById('pointsDynamicContent');
                const loadingOverlay = document.getElementById('pointsLoadingOverlay');
                const positiveElem = document.getElementById('positivePointsTotal');
                const negativeElem = document.getElementById('negativePointsTotal');
                const totalCountElem = document.getElementById('pointsTotalCountNum');
                const netBalanceElem = document.getElementById('netPointsBalance');
                const searchInput = document.getElementById('ptsSearchInput');
                const searchClear = document.getElementById('ptsSearchClear');
                const filterChipsHolder = document.getElementById('ptsFilterChips');
                const exportBtn = document.getElementById('ptsExportBtn');

                let currentFilter = @json($currentFilter);
                let currentSearch = @json($currentSearch);
                let searchDebounceTimer = null;

                function showLoading() {
                    if (loadingOverlay) loadingOverlay.style.display = 'flex';
                }

                function hideLoading() {
                    if (loadingOverlay) loadingOverlay.style.display = 'none';
                }

                function updateExportUrl() {
                    if (!exportBtn) return;
                    const exportBase = @json(route('profile.history.export'));
                    const params = new URLSearchParams();
                    if (currentFilter && currentFilter !== 'all') params.set('filter', currentFilter);
                    if (currentSearch && currentSearch.trim() !== '') params.set('search', currentSearch.trim());
                    const qs = params.toString();
                    exportBtn.href = exportBase + (qs ? '?' + qs : '');
                }

                function loadHistory(url, pushState = true) {
                    showLoading();

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Network error');
                            return response.json();
                        })
                        .then(data => {
                            if (data.html !== undefined && dynamicContainer) {
                                dynamicContainer.innerHTML = data.html;
                            }

                            if (data.positive_total !== undefined && positiveElem) {
                                positiveElem.textContent = '+' + data.positive_total;
                            }

                            if (data.negative_total !== undefined && negativeElem) {
                                negativeElem.textContent = '-' + data.negative_total;
                            }

                            if (data.total_count !== undefined && totalCountElem) {
                                totalCountElem.textContent = data.total_count;
                            }

                            if (data.positive_total !== undefined && data.negative_total !== undefined && netBalanceElem) {
                                const pos = parseFloat(data.positive_total) || 0;
                                const neg = parseFloat(data.negative_total) || 0;
                                const net = pos - neg;
                                netBalanceElem.textContent = (net > 0 ? '+' : '') + (Math.round(net * 100) / 100);
                                netBalanceElem.className = 'kpi-value ' + (net >= 0 ? 'is-positive' : 'is-negative');
                            }

                            if (pushState) {
                                window.history.pushState({ path: url }, '', url);
                            }

                            updateExportUrl();
                        })
                        .catch(err => {
                            console.error('AJAX History error:', err);
                            window.location.href = url;
                        })
                        .finally(() => {
                            hideLoading();
                        });
                }

                function triggerHistoryQuery(page = 1) {
                    const base = @json(route('profile.history'));
                    const params = new URLSearchParams();
                    if (currentFilter && currentFilter !== 'all') params.set('filter', currentFilter);
                    if (currentSearch && currentSearch.trim() !== '') params.set('search', currentSearch.trim());
                    if (page > 1) params.set('page', page);

                    const qs = params.toString();
                    const url = base + (qs ? '?' + qs : '');
                    loadHistory(url, true);
                }

                // Filter Chips click
                if (filterChipsHolder) {
                    filterChipsHolder.addEventListener('click', function (e) {
                        const btn = e.target.closest('.pts-filter-chip');
                        if (!btn) return;

                        const selected = btn.getAttribute('data-filter') || 'all';
                        if (selected === currentFilter) return;

                        currentFilter = selected;
                        filterChipsHolder.querySelectorAll('.pts-filter-chip').forEach(c => c.classList.remove('is-active'));
                        btn.classList.add('is-active');

                        triggerHistoryQuery(1);
                    });
                }

                // Search Input with Debounce
                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        const val = this.value;
                        currentSearch = val;

                        if (searchClear) {
                            searchClear.style.display = val.trim().length > 0 ? 'inline-flex' : 'none';
                        }

                        clearTimeout(searchDebounceTimer);
                        searchDebounceTimer = setTimeout(() => {
                            triggerHistoryQuery(1);
                        }, 350);
                    });
                }

                // Search Clear Button
                if (searchClear) {
                    searchClear.addEventListener('click', function () {
                        if (searchInput) {
                            searchInput.value = '';
                            currentSearch = '';
                            searchInput.focus();
                        }
                        searchClear.style.display = 'none';
                        triggerHistoryQuery(1);
                    });
                }

                // Delegate pagination click
                document.addEventListener('click', function (e) {
                    const link = e.target.closest('#pointsPaginationHolder .page-link');
                    if (!link) return;

                    const href = link.getAttribute('href');
                    if (href && href !== '#' && !link.closest('.disabled') && !link.closest('.active')) {
                        e.preventDefault();
                        loadHistory(href, true);
                        if (dynamicContainer) {
                            window.scrollTo({ top: dynamicContainer.offsetTop - 120, behavior: 'smooth' });
                        }
                    }
                });

                // Handle back / forward browser navigation
                window.addEventListener('popstate', function (e) {
                    if (e.state && e.state.path) {
                        loadHistory(e.state.path, false);
                    } else {
                        loadHistory(window.location.href, false);
                    }
                });
            });
        </script>
    @endpush
@endsection