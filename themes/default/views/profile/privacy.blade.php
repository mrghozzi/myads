@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.privacy_settings') }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title">{{ __('messages.privacy_settings') }}</p>
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

                <form action="{{ route('profile.privacy.update') }}" method="POST" class="form">
                    @csrf
                    <fieldset {{ !($featureAvailable ?? true) ? 'disabled' : '' }}>

                        @foreach([
                            'profile_visibility' => __('messages.profile_visibility'),
                            'about_visibility' => __('messages.about_visibility'),
                            'photos_visibility' => __('messages.photos_visibility'),
                            'followers_visibility' => __('messages.followers_visibility'),
                            'following_visibility' => __('messages.following_visibility'),
                            'points_history_visibility' => __('messages.points_history_visibility'),
                        ] as $field => $label)
                            <div class="form-row split">
                                <div class="form-item">
                                    <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                                    <select id="{{ $field }}" name="{{ $field }}" class="form-select">
                                        @foreach($visibilityOptions as $option)
                                            <option value="{{ $option }}" {{ $privacySettings->{$field} === $option ? 'selected' : '' }}>
                                                {{ __('messages.visibility_' . $option) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach

                        <div class="form-row split">
                            @foreach([
                                'allow_direct_messages' => __('messages.allow_direct_messages'),
                                'allow_mentions' => __('messages.allow_mentions'),
                                'allow_reposts' => __('messages.allow_reposts'),
                                'show_online_status' => __('messages.show_online_status'),
                            ] as $field => $label)
                                <div class="form-item">
                                    <div class="checkbox-wrap">
                                        <input type="hidden" name="{{ $field }}" value="0">
                                        <input type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1" {{ $privacySettings->{$field} ? 'checked' : '' }}>
                                        <label for="{{ $field }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-row">
                            <div class="form-item">
                                <button type="submit" class="button primary" {{ !($featureAvailable ?? true) ? 'disabled' : '' }}>{{ __('messages.save_changes') }}</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
