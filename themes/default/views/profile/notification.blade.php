@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.notification_settings') }}</p>
</div>

@push('head')
    <style>
        .notification-switch-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .notification-switch-card {
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

        .notification-switch-card:hover {
            transform: translateY(-2px);
            border-color: rgba(97, 93, 250, 0.28);
            box-shadow: 0 22px 40px rgba(94, 92, 154, 0.14);
        }

        .notification-switch-card__meta {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            flex: 1;
        }

        .notification-switch-card__icon {
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

        .notification-switch-card__body {
            min-width: 0;
        }

        .notification-switch-card__title {
            display: block;
            margin: 0 0 4px;
            color: #2b2f4a;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.35;
            cursor: pointer;
        }

        .notification-switch-card__hint {
            margin: 0;
            color: #8f91a3;
            font-size: 12px;
            line-height: 1.65;
        }

        .notification-switch-control {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .notification-switch-control .form-check-input {
            float: none;
            margin: 0;
            width: 3.3rem;
            height: 1.85rem;
            cursor: pointer;
            border: 0;
            box-shadow: none;
            background-color: #d9deef;
        }

        .notification-switch-control .form-check-input:checked {
            background-color: #615dfa;
        }

        .notification-switch-control .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(97, 93, 250, 0.18);
        }

        body[data-theme="css_d"] .notification-switch-card {
            border-color: rgba(255, 255, 255, 0.08);
            background: linear-gradient(180deg, #25243a 0%, #1e1d2f 100%);
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.26);
        }

        body[data-theme="css_d"] .notification-switch-card:hover {
            border-color: rgba(27, 200, 219, 0.34);
            box-shadow: 0 20px 38px rgba(0, 0, 0, 0.34);
        }

        body[data-theme="css_d"] .notification-switch-card__icon {
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.24) 0%, rgba(27, 200, 219, 0.22) 100%);
            color: #8d89ff;
        }

        body[data-theme="css_d"] .notification-switch-card__title {
            color: #f4f4ff;
        }

        body[data-theme="css_d"] .notification-switch-card__hint {
            color: #a8acc5;
        }

        body[data-theme="css_d"] .notification-switch-control .form-check-input {
            background-color: #4a4d64;
        }

        @media (max-width: 768px) {
            .notification-switch-grid {
                grid-template-columns: 1fr;
            }

            .notification-switch-card {
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
            <p class="widget-box-title">{{ __('messages.notification_settings') }}</p>
            <div class="widget-box-content">
                @if(session('success'))
                    <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">{{ session('success') }}</div>
                @endif

                <p class="mb-4">{{ __('messages.notification_settings_intro') }}</p>

                <form action="{{ route('profile.notifications.update') }}" method="POST" class="form">
                    @csrf
                    <div class="form-row">
                        <div class="form-item">
                            <div class="notification-switch-grid">
                                @foreach([
                                    'email_new_follower' => [
                                        'label' => __('messages.notif_new_follower'),
                                        'hint' => __('messages.notif_new_follower_hint'),
                                        'icon' => 'fa-user-plus',
                                    ],
                                    'email_new_comment' => [
                                        'label' => __('messages.notif_new_comment'),
                                        'hint' => __('messages.notif_new_comment_hint'),
                                        'icon' => 'fa-comment',
                                    ],
                                    'email_new_message' => [
                                        'label' => __('messages.notif_new_message'),
                                        'hint' => __('messages.notif_new_message_hint'),
                                        'icon' => 'fa-envelope',
                                    ],
                                    'email_mention' => [
                                        'label' => __('messages.notif_mention'),
                                        'hint' => __('messages.notif_mention_hint'),
                                        'icon' => 'fa-at',
                                    ],
                                    'email_repost' => [
                                        'label' => __('messages.notif_repost'),
                                        'hint' => __('messages.notif_repost_hint'),
                                        'icon' => 'fa-retweet',
                                    ],
                                    'email_reaction' => [
                                        'label' => __('messages.notif_reaction'),
                                        'hint' => __('messages.notif_reaction_hint'),
                                        'icon' => 'fa-heart',
                                    ],
                                    'email_forum_reply' => [
                                        'label' => __('messages.notif_forum_reply'),
                                        'hint' => __('messages.notif_forum_reply_hint'),
                                        'icon' => 'fa-comments',
                                    ],
                                    'email_marketplace_update' => [
                                        'label' => __('messages.notif_marketplace_update'),
                                        'hint' => __('messages.notif_marketplace_update_hint'),
                                        'icon' => 'fa-shopping-bag',
                                    ],
                                ] as $field => $config)
                                    <div class="notification-switch-card">
                                        <div class="notification-switch-card__meta">
                                            <div class="notification-switch-card__icon">
                                                <i class="fa {{ $config['icon'] }}" aria-hidden="true"></i>
                                            </div>
                                            <div class="notification-switch-card__body">
                                                <label class="notification-switch-card__title" for="{{ $field }}">{{ $config['label'] }}</label>
                                                <p class="notification-switch-card__hint">{{ $config['hint'] }}</p>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch notification-switch-control">
                                            <input type="hidden" name="{{ $field }}" value="0">
                                            <input class="form-check-input" type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1" {{ $settings->{$field} ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-row mt-4">
                        <div class="form-item">
                            <button type="submit" class="button primary">{{ __('messages.save_changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
