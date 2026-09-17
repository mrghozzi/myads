@php
    $unreadNotifCount = (int) ($unreadNotificationsCount ?? 0);
    $unreadMsgCount = (int) ($unreadMessagesCount ?? 0);
@endphp

<nav class="myads-mobile-bottom-nav" aria-label="Mobile Navigation">
    <!-- Home / Community Feed -->
    <a href="{{ url('/portal') }}" class="myads-nav-item {{ request()->is('portal*') || request()->is('/') ? 'active' : '' }}">
        <i class="fa-solid fa-house"></i>
        <span>{{ __('messages.home') ?? 'الرئيسية' }}</span>
    </a>

    <!-- Videos & Shorts Hub -->
    <a href="{{ url('/video') }}" class="myads-nav-item {{ request()->is('video*') || request()->is('clips*') ? 'active' : '' }}">
        <i class="fa-solid fa-play"></i>
        <span>{{ __('messages.video') ?? 'فيديو' }}</span>
    </a>

    <!-- Quick Post (+ FAB) -->
    @auth
        <a href="{{ url('/post') }}" class="myads-nav-fab myads-btn-press" title="{{ __('messages.add_post') ?? 'نشر' }}" aria-label="{{ __('messages.add_post') ?? 'نشر' }}">
            <i class="fa-solid fa-plus"></i>
        </a>
    @else
        <a href="{{ route('login') }}" class="myads-nav-fab myads-btn-press" title="{{ __('messages.login') ?? 'دخول' }}" aria-label="{{ __('messages.login') ?? 'دخول' }}">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </a>
    @endauth

    <!-- Messages / Forum -->
    @auth
        <a href="{{ url('/messages') }}" class="myads-nav-item {{ request()->is('messages*') ? 'active' : '' }}">
            <i class="fa-solid fa-comment-dots"></i>
            <span>{{ __('messages.messages') ?? 'الرسائل' }}</span>
            @if($unreadMsgCount > 0)
                <span class="myads-nav-badge">{{ $unreadMsgCount > 99 ? '99+' : $unreadMsgCount }}</span>
            @endif
        </a>
    @else
        <a href="{{ url('/forum') }}" class="myads-nav-item {{ request()->is('forum*') ? 'active' : '' }}">
            <i class="fa-solid fa-comments"></i>
            <span>{{ __('messages.forum') ?? 'المنتدى' }}</span>
        </a>
    @endauth

    <!-- Profile / Account -->
    @auth
        <a href="{{ url('/u/' . auth()->user()->username) }}" class="myads-nav-item {{ request()->is('u/' . auth()->user()->username . '*') ? 'active' : '' }}">
            <i class="fa-solid fa-user"></i>
            <span>{{ __('messages.profile') ?? 'حسابي' }}</span>
            @if($unreadNotifCount > 0)
                <span class="myads-nav-badge">{{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}</span>
            @endif
        </a>
    @else
        <a href="{{ route('login') }}" class="myads-nav-item {{ request()->is('login*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-lock"></i>
            <span>{{ __('messages.login') ?? 'دخول' }}</span>
        </a>
    @endauth
</nav>
