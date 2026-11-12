@if($history->count() === 0)
    <div class="points-history-empty">
        <div class="points-empty-icon">
            <i class="fa-solid fa-coins"></i>
        </div>
        <h4 class="points-empty-title">{{ ($featureAvailable ?? true) ? __('messages.no_history') : __('messages.upgrade_legacy_mode_notice') }}</h4>
        <p class="points-empty-desc">{{ __('messages.pts_history_desc') }}</p>
    </div>
@else
    <div class="table-responsive points-table-responsive">
        <table class="points-history-table">
            <thead>
                <tr>
                    <th style="width: 85px;">#</th>
                    <th>{{ __('messages.pts_transaction_type') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th style="text-align: {{ is_locale_rtl() ? 'left' : 'right' }};">{{ __('messages.pts_amount') }}</th>
                    <th style="text-align: {{ is_locale_rtl() ? 'left' : 'right' }};">{{ __('messages.pts_running_balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $item)
                    @php
                        $descKey = !empty($item->description_key) ? $item->description_key : ($item->type ?? 'points_awarded');
                        $description = __('messages.' . $descKey);
                        $description = ($description !== 'messages.' . $descKey) ? $description : $descKey;
                        $amount = (float) $item->amount;
                        $isPositive = $amount >= 0;

                        $dateDisplay = '--';
                        if (!empty($item->created_at)) {
                            $dateDisplay = is_numeric($item->created_at)
                                ? \Carbon\Carbon::createFromTimestamp((int) $item->created_at)->format('Y-m-d H:i')
                                : optional($item->created_at)->format('Y-m-d H:i');
                        } elseif (!empty($item->created_at_ts)) {
                            $dateDisplay = \Carbon\Carbon::createFromTimestamp((int) $item->created_at_ts)->format('Y-m-d H:i');
                        }
                    @endphp
                    <tr class="points-history-row">
                        <td class="points-id-cell">
                            <span class="points-id-badge">#{{ $item->id }}</span>
                        </td>
                        <td>
                            <div class="points-history-entry">
                                <span class="points-entry-icon {{ $isPositive ? 'is-positive' : 'is-negative' }}">
                                    @if($isPositive)
                                        <i class="fa-solid fa-arrow-down-left-long" aria-hidden="true"></i>
                                    @else
                                        <i class="fa-solid fa-arrow-up-right-long" aria-hidden="true"></i>
                                    @endif
                                </span>
                                <div class="points-entry-details">
                                    <div class="points-entry-title">
                                        {{ $description }}
                                    </div>
                                    <div class="points-entry-meta">
                                        @if($isPositive)
                                            <span class="points-flow-tag is-positive">
                                                <i class="fa-solid fa-plus"></i> {{ __('messages.pts_positive_badge') }}
                                            </span>
                                        @else
                                            <span class="points-flow-tag is-negative">
                                                <i class="fa-solid fa-minus"></i> {{ __('messages.pts_negative_badge') }}
                                            </span>
                                        @endif

                                        @if(!empty($item->type) && !in_array($item->type, ['legacy', 'reward']))
                                            <span class="points-flow-tag is-type">
                                                {{ strtoupper(str_replace('_', ' ', $item->type)) }}
                                            </span>
                                        @endif

                                        @if(!empty($item->is_legacy))
                                            <span class="points-flow-tag is-legacy">
                                                <i class="fa-solid fa-clock-rotate-left"></i> {{ __('messages.legacy_points') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="points-date-cell">
                            <div class="points-date-wrap">
                                <i class="fa-solid fa-calendar-day"></i>
                                <span>{{ $dateDisplay }}</span>
                            </div>
                        </td>
                        <td class="points-amount-cell" style="text-align: {{ is_locale_rtl() ? 'left' : 'right' }};">
                            <span class="points-amount-badge {{ $isPositive ? 'is-positive' : 'is-negative' }}">
                                {{ $amount > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($amount, 2), '0'), '.') }}
                                <small class="points-unit">{{ __('messages.pts_short') ?? 'PTS' }}</small>
                            </span>
                        </td>
                        <td class="points-balance-cell" style="text-align: {{ is_locale_rtl() ? 'left' : 'right' }};">
                            @if(isset($item->balance_after) && $item->balance_after !== null && $item->balance_after !== '')
                                <span class="points-running-balance-badge">
                                    <i class="fa-solid fa-wallet" style="margin-inline-end: 4px; opacity: 0.7;"></i>
                                    {{ rtrim(rtrim(number_format((float) $item->balance_after, 2), '0'), '.') }}
                                    <small class="points-unit">{{ __('messages.pts_short') ?? 'PTS' }}</small>
                                </span>
                            @else
                                <span class="text-muted" style="opacity: 0.5;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(method_exists($history, 'hasPages') && $history->hasPages())
        <div class="points-pagination-holder" id="pointsPaginationHolder">
            {{ $history->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endif
