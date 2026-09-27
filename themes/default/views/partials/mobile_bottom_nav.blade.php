@php
    $unreadNotifCount = (int) ($unreadNotificationsCount ?? ($headerNotificationUnreadCount ?? 0));
    $unreadMsgCount = (int) ($unreadMessagesCount ?? ($headerMessageUnreadCount ?? 0));
    $formatNavCount = static fn (int $count): string => $count > 99 ? '99+' : (string) $count;

    $isOwnProfile = auth()->check() && (
        (isset($user) && $user instanceof \App\Models\User && $user->id === auth()->id()) ||
        request()->is('u/' . auth()->user()->username) ||
        request()->is('u/' . auth()->user()->username . '/*')
    );
    $hasQuickPostOnPage = auth()->check() && (request()->is('portal*') || request()->is('/') || $isOwnProfile);
    $quickPostTarget = $hasQuickPostOnPage ? '#quick-post-box' : url('/share');
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
        <a href="{{ $quickPostTarget }}"
           id="myads-nav-quick-post-btn"
           class="myads-nav-fab myads-btn-press"
           title="{{ __('messages.add_post') }}"
           aria-label="{{ __('messages.add_post') }}">
            <i class="fa-solid fa-plus"></i>
        </a>
    @else
        <a href="{{ route('login') }}" class="myads-nav-fab myads-btn-press" title="{{ __('messages.login') }}" aria-label="{{ __('messages.login') }}">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </a>
    @endauth

    <!-- Messages / Forum -->
    @auth
        <a href="{{ url('/messages') }}" class="myads-nav-item {{ request()->is('messages*') ? 'active' : '' }}" data-message-action-trigger>
            <i class="fa-solid fa-comment-dots"></i>
            <span>{{ __('messages.messages') ?? 'الرسائل' }}</span>
            <span class="myads-nav-badge" data-message-unread-count @if($unreadMsgCount === 0) hidden @endif>{{ $unreadMsgCount > 0 ? $formatNavCount($unreadMsgCount) : '' }}</span>
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
            <span class="myads-nav-badge" data-notification-badge @if($unreadNotifCount === 0) hidden @endif>{{ $unreadNotifCount > 0 ? $formatNavCount($unreadNotifCount) : '' }}</span>
        </a>
    @else
        <a href="{{ route('login') }}" class="myads-nav-item {{ request()->is('login*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-lock"></i>
            <span>{{ __('messages.login') ?? 'دخول' }}</span>
        </a>
    @endauth
</nav>

@auth
<script>
    (function () {
        function setupQuickPostFab() {
            var quickPostBtn = document.getElementById('myads-nav-quick-post-btn');
            if (!quickPostBtn || quickPostBtn.__hasQuickPostListener) return;
            quickPostBtn.__hasQuickPostListener = true;

            quickPostBtn.addEventListener('click', function (e) {
                var quickPostBox = document.getElementById('quick-post-box');
                if (quickPostBox && (quickPostBox.offsetWidth > 0 || quickPostBox.offsetHeight > 0)) {
                    e.preventDefault();
                    quickPostBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    var input = quickPostBox.querySelector('#composer-text, textarea, input[type="text"]');
                    if (input) {
                        setTimeout(function () {
                            input.focus();
                        }, 250);
                    }
                } else if (quickPostBtn.getAttribute('href') === '#quick-post-box') {
                    e.preventDefault();
                    window.location.href = '{{ url('/share') }}';
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupQuickPostFab);
        } else {
            setupQuickPostFab();
        }
    })();
</script>
@endauth
