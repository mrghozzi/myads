@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.pts_history') ?? 'PTS History' }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        <!-- SIDEBAR MENU -->
        <div class="widget-box">
            <p class="widget-box-title">{{ __('messages.account_settings') }}</p>
            <div class="widget-box-content padding-none">
                <a href="{{ route('profile.edit') }}" class="button secondary full" style="border-radius: 0; box-shadow: none;">{{ __('messages.edit_profile') }}</a>
                <a href="{{ route('profile.history') }}" class="button primary full" style="border-radius: 0; box-shadow: none;">{{ __('messages.pts_history') ?? 'PTS History' }}</a>
                <a href="{{ route('profile.show', $user->username) }}" class="button secondary full" style="border-radius: 0; box-shadow: none;">{{ __('messages.view_profile') }}</a>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title">{{ __('messages.pts_history') ?? 'PTS History' }}</p>
            <div class="widget-box-content">
                <div class="table-responsive">
                    <table class="table table-hover" style="width: 100%; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 1px solid #eaeaf5;">
                                <th style="padding: 12px;">#ID</th>
                                <th style="padding: 12px;">PTS</th>
                                <th style="padding: 12px;">{{ __('messages.name') ?? 'Name' }}</th>
                                <th style="padding: 12px;">{{ __('messages.date') ?? 'Date' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $item)
                                <tr style="border-bottom: 1px solid #eaeaf5;">
                                    <td style="padding: 12px;">#{{ $item->id }}</td>
                                    <td style="padding: 12px;">{{ $item->o_valuer }}</td>
                                    <td style="padding: 12px;">{{ __("messages.".$item->name) !== "messages.".$item->name ? __("messages.".$item->name) : $item->name }}</td>
                                    <td style="padding: 12px;">{{ is_numeric($item->o_mode) ? \Carbon\Carbon::createFromTimestamp($item->o_mode)->format('Y-m-d H:i') : $item->o_mode }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="padding: 12px; text-align: center;">{{ __('messages.no_history') ?? 'No history found.' }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($history->hasPages())
                    <div style="margin-top: 20px;">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
