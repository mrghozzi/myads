@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.badges') }}</p>
    <p class="section-banner-text">{{ __('messages.badge_showcase_help') }}</p>
</div>

<style>
    .badge-settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
    }
    .badge-settings-card {
        border: 1px solid #eaeaf5;
        border-radius: 16px;
        padding: 18px;
        background: linear-gradient(180deg, #fff 0%, #f9faff 100%);
    }
    .badge-settings-icon {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        display: grid;
        place-items: center;
        background: rgba(97, 93, 250, 0.12);
        color: #615dfa;
        font-size: 20px;
        margin-bottom: 14px;
    }
</style>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title">{{ __('messages.badges') }}</p>
            <div class="widget-box-content">
                @if(session('success'))
                    <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger" role="alert" style="margin-bottom: 20px;">{{ session('error') }}</div>
                @endif

                @if(!empty($upgradeNotice))
                    @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
                @endif

                <form action="{{ route('profile.badges.update') }}" method="POST">
                    @csrf
                    <fieldset {{ !($featureAvailable ?? true) ? 'disabled' : '' }}>
                        <p class="user-status-text" style="margin-bottom: 16px;">{{ __('messages.badge_showcase_limit') }}</p>

                        <div class="badge-settings-grid">
                            @forelse($earnedBadges as $earned)
                                @php $badge = $earned->badge; @endphp
                                @if($badge)
                                    <label class="badge-settings-card">
                                        <div class="badge-settings-icon">
                                            <i class="fa fa-trophy" aria-hidden="true"></i>
                                        </div>
                                        <p class="user-status-title">{{ __('messages.' . $badge->name_key) }}</p>
                                        <p class="user-status-text small">{{ __('messages.' . $badge->description_key) }}</p>
                                        <div class="checkbox-wrap" style="margin-top: 12px;">
                                            <input type="checkbox" name="badge_ids[]" value="{{ $badge->id }}" {{ in_array($badge->id, $showcaseIds, true) ? 'checked' : '' }}>
                                            <label>{{ __('messages.show_on_profile') }}</label>
                                        </div>
                                    </label>
                                @endif
                            @empty
                                <div class="widget-box" style="grid-column: 1 / -1;">
                                    <div class="widget-box-content">
                                        <p class="text-center">{{ ($featureAvailable ?? true) ? __('messages.no_badges_unlocked') : __('messages.upgrade_legacy_mode_notice') }}</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div style="margin-top: 20px;">
                            <button type="submit" class="button primary" {{ !($featureAvailable ?? true) ? 'disabled' : '' }}>{{ __('messages.save_changes') }}</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
