@if($deals->count() > 0)
    <table class="custom-ads-table">
        <thead>
            <tr>
                <th>{{ __('messages.custom_ads_placement') }}</th>
                <th>{{ __('messages.custom_ads_parties') }}</th>
                <th>{{ __('messages.custom_ads_payment') }}</th>
                <th>{{ __('messages.stats') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deals as $deal)
                <tr>
                    <td>
                        <strong>{{ $deal->placement?->name }}</strong>
                        <div class="custom-ads-muted">#{{ $deal->id }} · {{ $deal->source }}</div>
                    </td>
                    <td>
                        <div>{{ __('messages.publisher') }}: {{ $deal->publisher?->username ?? $deal->placement?->user?->username }}</div>
                        <div>{{ __('messages.custom_ads_advertiser') }}: {{ $deal->advertiser?->username }}</div>
                    </td>
                    <td>
                        @if($deal->payment_type === \App\Models\CustomAdDeal::PAYMENT_PTS_DAILY)
                            <span class="custom-ads-pill">{{ number_format((float) $deal->daily_pts, 2) }} PTS/{{ __('messages.day') }}</span>
                            <span class="custom-ads-pill">{{ number_format((float) $deal->remainingReservedPts(), 2) }} {{ __('messages.custom_ads_remaining') }}</span>
                        @else
                            <span class="custom-ads-pill">{{ __('messages.custom_ads_external') }}</span>
                            @if($deal->external_amount)
                                <span class="custom-ads-pill">{{ number_format((float) $deal->external_amount, 2) }} {{ $deal->external_currency }}</span>
                            @endif
                        @endif
                    </td>
                    <td>
                        <span class="custom-ads-pill"><i class="fa fa-eye"></i>{{ $deal->impressions }}</span>
                        <span class="custom-ads-pill"><i class="fa fa-mouse-pointer"></i>{{ $deal->clicks }}</span>
                        <span class="custom-ads-pill">CTR {{ $deal->ctr() }}%</span>
                    </td>
                    <td><span class="custom-ads-status {{ $deal->status }}">{{ $deal->status }}</span></td>
                    <td><a href="{{ route('ads.custom.deals.show', $deal) }}" class="button tertiary">{{ __('messages.details') }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="custom-ads-card">
        <p class="custom-ads-muted">{{ __('messages.custom_ads_no_deals') }}</p>
    </div>
@endif
