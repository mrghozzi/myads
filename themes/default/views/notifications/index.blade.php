@extends('theme::layouts.master')

@section('title', __('messages.notifications'))

@section('content')
@php
    $notificationCenterText = app()->getLocale() === 'ar'
        ? [
            'banner_title' => 'مركز الإشعارات',
            'banner_subtitle' => 'كل تحديثات حسابك في مكان واحد.',
            'unread_label' => 'غير مقروءة',
            'summary_help' => 'راجع الإشعارات الجديدة أو علّمها كلها كمقروءة من هنا.',
            'empty_title' => 'لا توجد إشعارات جديدة.',
            'empty_text' => 'عند وصول أي تحديث جديد سيظهر هنا مباشرة.',
            'feed_subtitle' => 'آخر الأنشطة المرتبطة بحسابك ومحتواك.',
        ]
        : [
            'banner_title' => 'Notification Center',
            'banner_subtitle' => 'All your account updates in one place.',
            'unread_label' => 'Unread',
            'summary_help' => 'Review new notifications or mark them all as read from here.',
            'empty_title' => 'No new notifications.',
            'empty_text' => 'New activity will appear here as soon as it arrives.',
            'feed_subtitle' => 'Latest activity related to your account and content.',
        ];

    $formatNotificationCount = static fn (int $count): string => $count > 99 ? '99+' : (string) $count;
@endphp

<div class="section-banner notification-center-banner" style="background: url({{ theme_asset('img/banner/profile.png') }}) no-repeat 50%;">
    <p class="section-banner-title">{{ $notificationCenterText['banner_title'] }}</p>
    <p class="section-banner-text">{{ $notificationCenterText['banner_subtitle'] }}</p>
</div>

<div class="grid grid-3-9 notification-center-grid notification-center-shell">
    <div class="grid-column">
        <div class="widget-box notification-summary-card">
            <div class="widget-box-settings">
                <div class="post-peek-header">
                    <p class="widget-box-title">{{ __('messages.info') }}</p>
                </div>
            </div>

            <div class="widget-box-content">
                <div class="notification-summary-head">
                    <div class="notification-summary-icon">
                        <svg class="icon-notification">
                            <use xlink:href="#svg-notification"></use>
                        </svg>
                    </div>

                    <div class="notification-summary-copy">
                        <p class="notification-summary-heading">{{ __('messages.notifications') }}</p>
                        <p class="notification-summary-copy-text">{{ $notificationCenterText['summary_help'] }}</p>
                    </div>
                </div>

                <div class="notification-summary-stat">
                    <strong class="notification-summary-stat-value" data-notification-summary-count>{{ $unreadNotificationCount }}</strong>
                    <span class="notification-summary-stat-label">{{ $notificationCenterText['unread_label'] }}</span>
                </div>

                <button
                    type="button"
                    class="button secondary notification-summary-button"
                    data-mark-all-notifications
                    @if($unreadNotificationCount === 0) hidden @endif
                    onclick="markAllNotificationsAsRead(this)"
                >
                    <i class="fa fa-check-double"></i>
                    {{ __('messages.mark_all_read') ?? 'Mark all as read' }}
                </button>

                <p class="notification-summary-note">{{ __('messages.notifications_marked_read') }}</p>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box notification-feed-card">
            <div class="widget-box-settings notification-feed-header">
                <div>
                    <p class="widget-box-title">{{ __('messages.notifications') }}</p>
                    <p class="notification-feed-subtitle">{{ $notificationCenterText['feed_subtitle'] }}</p>
                </div>

                <span class="notification-feed-count" data-notification-highlight @if($unreadNotificationCount === 0) hidden @endif>
                    {{ $formatNotificationCount($unreadNotificationCount) }}
                </span>
            </div>

            <div class="widget-box-content">
                @if($notifications->count() > 0)
                    <div class="notification-center-list" id="infinite-scroll-container">
                        @include('theme::notifications.partials.items', ['notifications' => $notifications])
                    </div>

                    @include('theme::partials.ajax.infinite_scroll', ['paginator' => $notifications])

                    <noscript>
                        <div class="notification-pagination-fallback">
                            {{ $notifications->links() }}
                        </div>
                    </noscript>
                @else
                    <div class="notification-empty-state">
                        <div class="notification-empty-state-icon">
                            <svg class="icon-notification">
                                <use xlink:href="#svg-notification"></use>
                            </svg>
                        </div>
                        <p class="notification-empty-state-title">{{ $notificationCenterText['empty_title'] }}</p>
                        <p class="notification-empty-state-text">{{ $notificationCenterText['empty_text'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
