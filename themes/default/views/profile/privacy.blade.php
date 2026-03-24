@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.privacy_settings') }}</p>
</div>

@push('head')
    <style>
        .privacy-switch-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .privacy-switch-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 16px 18px;
            border: 1px solid #e7e9f6;
            border-radius: 20px;
            background: linear-gradient(180deg, #ffffff 0%, #f8f9ff 100%);
            box-shadow: 0 16px 34px rgba(94, 92, 154, 0.08);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .privacy-switch-card:hover {
            transform: translateY(-2px);
            border-color: rgba(97, 93, 250, 0.28);
            box-shadow: 0 22px 40px rgba(94, 92, 154, 0.14);
        }

        .privacy-switch-card__meta {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            flex: 1;
        }

        .privacy-switch-card__icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.16) 0%, rgba(27, 200, 219, 0.18) 100%);
            color: #615dfa;
            font-size: 18px;
        }

        .privacy-switch-card__body {
            min-width: 0;
        }

        .privacy-switch-card__title {
            display: block;
            margin: 0 0 4px;
            color: #2b2f4a;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.35;
            cursor: pointer;
        }

        .privacy-switch-card__hint {
            margin: 0;
            color: #8f91a3;
            font-size: 12px;
            line-height: 1.65;
        }

        .privacy-switch-control {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .privacy-switch-control .form-check-input {
            float: none;
            margin: 0;
            width: 3.3rem;
            height: 1.85rem;
            cursor: pointer;
            border: 0;
            box-shadow: none;
            background-color: #d9deef;
        }

        .privacy-switch-control .form-check-input:checked {
            background-color: #615dfa;
        }

        .privacy-switch-control .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(97, 93, 250, 0.18);
        }

        body[data-theme="css_d"] .privacy-switch-card {
            border-color: rgba(255, 255, 255, 0.08);
            background: linear-gradient(180deg, #25243a 0%, #1e1d2f 100%);
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.26);
        }

        body[data-theme="css_d"] .privacy-switch-card:hover {
            border-color: rgba(27, 200, 219, 0.34);
            box-shadow: 0 20px 38px rgba(0, 0, 0, 0.34);
        }

        body[data-theme="css_d"] .privacy-switch-card__icon {
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.24) 0%, rgba(27, 200, 219, 0.22) 100%);
            color: #8d89ff;
        }

        body[data-theme="css_d"] .privacy-switch-card__title {
            color: #f4f4ff;
        }

        body[data-theme="css_d"] .privacy-switch-card__hint {
            color: #a8acc5;
        }

        body[data-theme="css_d"] .privacy-switch-control .form-check-input {
            background-color: #4a4d64;
        }

        @media (max-width: 768px) {
            .privacy-switch-grid {
                grid-template-columns: 1fr;
            }

            .privacy-switch-card {
                padding: 14px 16px;
            }
        }
    </style>
@endpush

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

                        <div class="form-row">
                            <div class="form-item">
                                <div class="privacy-switch-grid">
                                    @foreach([
                                        'allow_direct_messages' => [
                                            'label' => __('messages.allow_direct_messages'),
                                            'hint' => __('messages.allow_direct_messages_hint'),
                                            'icon' => 'fa-envelope',
                                        ],
                                        'allow_mentions' => [
                                            'label' => __('messages.allow_mentions'),
                                            'hint' => __('messages.allow_mentions_hint'),
                                            'icon' => 'fa-at',
                                        ],
                                        'allow_reposts' => [
                                            'label' => __('messages.allow_reposts'),
                                            'hint' => __('messages.allow_reposts_hint'),
                                            'icon' => 'fa-retweet',
                                        ],
                                        'show_online_status' => [
                                            'label' => __('messages.show_online_status'),
                                            'hint' => __('messages.show_online_status_hint'),
                                            'icon' => 'fa-signal',
                                        ],
                                    ] as $field => $config)
                                        <div class="privacy-switch-card">
                                            <div class="privacy-switch-card__meta">
                                                <div class="privacy-switch-card__icon">
                                                    <i class="fa {{ $config['icon'] }}" aria-hidden="true"></i>
                                                </div>
                                                <div class="privacy-switch-card__body">
                                                    <label class="privacy-switch-card__title" for="{{ $field }}">{{ $config['label'] }}</label>
                                                    <p class="privacy-switch-card__hint">{{ $config['hint'] }}</p>
                                                </div>
                                            </div>
                                            <div class="form-check form-switch privacy-switch-control">
                                                <input type="hidden" name="{{ $field }}" value="0">
                                                <input class="form-check-input" type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1" {{ $privacySettings->{$field} ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
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
