@extends('theme::layouts.master')

@section('title', $pageTitle ?? ($topic->title . ' - ' . __('support.support_forum') . ' - ' . $siteName))

@section('content')
<div class="content-grid" style="padding-bottom: 80px;">
    <style>
        .topic-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 28px;
            align-items: start;
        }

        .topic-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-xl, 22px);
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: var(--myads-shadow-sm, 0 4px 16px rgba(0,0,0,0.03));
        }

        .topic-header {
            margin-bottom: 22px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--myads-surface-border, #f1f5f9);
        }

        .topic-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .topic-badge-pill {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-solved {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .badge-unsolved {
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .badge-category {
            background: rgba(35, 210, 226, 0.12);
            color: #0891b2;
            border: 1px solid rgba(35, 210, 226, 0.25);
            text-decoration: none;
        }

        .badge-pinned {
            background: rgba(97, 93, 250, 0.12);
            color: #615dfa;
            border: 1px solid rgba(97, 93, 250, 0.25);
        }

        .topic-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            line-height: 1.35;
            margin: 0 0 16px 0;
            word-break: break-word;
        }

        .topic-author-bar {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topic-author-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--myads-surface-border, #e2e8f0);
        }

        .topic-author-details {
            flex: 1;
        }

        .topic-author-name {
            font-size: 1rem;
            font-weight: 700;
            color: var(--myads-text-main, #1e293b);
            margin: 0 0 2px 0;
        }

        .topic-author-time {
            font-size: 0.82rem;
            color: var(--myads-text-muted, #64748b);
        }

        .topic-content-body {
            font-size: 1.02rem;
            line-height: 1.75;
            color: var(--myads-text-main, #334155);
            word-break: break-word;
            white-space: pre-line;
        }

        /* Solution Banner */
        .solution-box {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(35, 210, 226, 0.06) 100%);
            border: 2px solid #10b981;
            border-radius: var(--myads-radius-lg, 18px);
            padding: 24px;
            margin-bottom: 24px;
            position: relative;
        }

        .solution-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #10b981;
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 9999px;
            margin-bottom: 14px;
        }

        /* Replies stream */
        .replies-section-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 18px 0;
        }

        .reply-item {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 18px);
            padding: 24px;
            margin-bottom: 16px;
            position: relative;
            transition: all 0.2s ease;
        }

        .reply-item.is-solution {
            border: 2px solid #10b981;
            background: rgba(16, 185, 129, 0.03);
        }

        .reply-item.is-staff {
            border-color: var(--myads-primary, #615dfa);
            background: rgba(97, 93, 250, 0.02);
        }

        .reply-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .reply-author-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .reply-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .badge-staff {
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-mark-solution {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 9999px;
            padding: 5px 14px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-mark-solution:hover {
            background: #10b981;
            color: #ffffff;
        }

        .reply-body {
            font-size: 0.98rem;
            line-height: 1.7;
            color: var(--myads-text-main, #334155);
            white-space: pre-line;
            word-break: break-word;
        }

        /* Reply Form */
        .reply-form-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-xl, 20px);
            padding: 26px;
            margin-top: 24px;
        }

        .reply-textarea {
            width: 100%;
            min-height: 140px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--myads-surface-border, #cbd5e1);
            background: var(--myads-surface, #ffffff);
            font-size: 0.95rem;
            outline: none;
            resize: vertical;
            margin-bottom: 16px;
        }

        .reply-textarea:focus {
            border-color: var(--myads-primary, #615dfa);
            box-shadow: 0 0 0 3px rgba(97, 93, 250, 0.15);
        }

        .btn-reply-submit {
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff;
            border: none;
            padding: 10px 26px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(97, 93, 250, 0.3);
        }

        /* Sidebar Cards */
        .sidebar-box {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 18px);
            padding: 22px;
            margin-bottom: 20px;
        }

        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--myads-text-main, #1e293b);
            margin: 0 0 14px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .related-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .related-list a {
            font-size: 0.88rem;
            color: var(--myads-text-main, #334155);
            text-decoration: none;
            line-height: 1.45;
            display: block;
            transition: color 0.2s ease;
        }

        .related-list a:hover {
            color: var(--myads-primary, #615dfa);
        }

        .crumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            color: var(--myads-text-muted, #64748b);
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .crumbs a {
            color: var(--myads-primary, #615dfa);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .topic-layout { grid-template-columns: 1fr; }
        }
    </style>

    <!-- Breadcrumb -->
    <div class="crumbs">
        <a href="{{ route('support.index') }}"><i class="fa-solid fa-house"></i> {{ __('support.support_forum') }}</a>
        <i class="fa-solid fa-chevron-left" style="font-size: 0.75rem;"></i>
        @if($topic->category)
            <a href="{{ route('support.category', $topic->category->slug) }}">{{ $topic->category->name }}</a>
            <i class="fa-solid fa-chevron-left" style="font-size: 0.75rem;"></i>
        @endif
        <span>#{{ $topic->id }}</span>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #065f46; border-radius: 14px; padding: 14px 20px; margin-bottom: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.2rem; color: #10b981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="topic-layout">
        <div>
            <!-- Main Question Card -->
            <div class="topic-card">
                <div class="topic-header">
                    <div class="topic-badges">
                        @if($topic->is_pinned)
                            <span class="topic-badge-pill badge-pinned">
                                <i class="fa-solid fa-thumbtack"></i> {{ __('support.pinned') }}
                            </span>
                        @endif
                        @if($topic->isResolved())
                            <span class="topic-badge-pill badge-solved">
                                <i class="fa-solid fa-check-circle"></i> {{ __('support.solved') }}
                            </span>
                        @else
                            <span class="topic-badge-pill badge-unsolved">
                                <i class="fa-solid fa-circle-question"></i> {{ __('support.unsolved') }}
                            </span>
                        @endif
                        @if($topic->category)
                            <a href="{{ route('support.category', $topic->category->slug) }}" class="topic-badge-pill badge-category">
                                <i class="{{ $topic->category->icon }}"></i> {{ $topic->category->name }}
                            </a>
                        @endif
                    </div>

                    <h1 class="topic-title">{{ $topic->title }}</h1>

                    <div class="topic-author-bar">
                        <img src="{{ !empty($topic->user->img) ? theme_asset('img/' . $topic->user->img) : theme_asset('img/avatar.png') }}"
                             alt="{{ $topic->user->username ?? 'User' }}"
                             class="topic-author-avatar">
                        <div class="topic-author-details">
                            <h4 class="topic-author-name">{{ $topic->user->username ?? 'Member' }}</h4>
                            <div class="topic-author-time">
                                <span><i class="fa-regular fa-clock"></i> {{ $topic->created_at->diffForHumans() }}</span>
                                <span style="margin: 0 6px;">•</span>
                                <span><i class="fa-solid fa-eye"></i> {{ __('support.views_count', ['count' => $topic->views_count]) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="topic-content-body">
                    {{ $topic->content }}
                </div>
            </div>

            <!-- Official Solution Box (if resolved) -->
            @if($topic->bestReply)
                <div class="solution-box">
                    <div class="solution-tag">
                        <i class="fa-solid fa-star"></i>
                        <span>{{ __('support.official_solution') }}</span>
                    </div>
                    <div class="reply-header" style="margin-bottom: 10px;">
                        <div class="reply-author-info">
                            <img src="{{ !empty($topic->bestReply->user->img) ? theme_asset('img/' . $topic->bestReply->user->img) : theme_asset('img/avatar.png') }}"
                                 alt="{{ $topic->bestReply->user->username ?? 'User' }}"
                                 class="reply-avatar">
                            <div>
                                <span style="font-weight: 700; color: var(--myads-text-main, #1e293b);">
                                    {{ $topic->bestReply->user->username ?? 'Member' }}
                                </span>
                                @if($topic->bestReply->is_staff)
                                    <span class="badge-staff ms-2"><i class="fa-solid fa-shield-halved"></i> {{ __('support.staff_member') }}</span>
                                @endif
                                <div style="font-size: 0.78rem; color: var(--myads-text-muted, #64748b);">
                                    {{ $topic->bestReply->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="reply-body">
                        {{ $topic->bestReply->content }}
                    </div>
                </div>
            @endif

            <!-- Replies Stream -->
            <h2 class="replies-section-title">
                <i class="fa-solid fa-comments" style="color: var(--myads-primary, #615dfa);"></i>
                <span>{{ __('support.replies_count', ['count' => $topic->replies_count]) }}</span>
            </h2>

            @forelse($topic->replies as $reply)
                <div class="reply-item {{ $reply->is_solution ? 'is-solution' : '' }} {{ $reply->is_staff ? 'is-staff' : '' }}" id="reply-{{ $reply->id }}">
                    <div class="reply-header">
                        <div class="reply-author-info">
                            <img src="{{ !empty($reply->user->img) ? theme_asset('img/' . $reply->user->img) : theme_asset('img/avatar.png') }}"
                                 alt="{{ $reply->user->username ?? 'User' }}"
                                 class="reply-avatar">
                            <div>
                                <span style="font-weight: 700; color: var(--myads-text-main, #1e293b);">
                                    {{ $reply->user->username ?? 'Member' }}
                                </span>
                                @if($reply->is_staff)
                                    <span class="badge-staff ms-2"><i class="fa-solid fa-shield-halved"></i> {{ __('support.staff_member') }}</span>
                                @endif
                                @if($reply->is_solution)
                                    <span class="topic-badge-pill badge-solved ms-2"><i class="fa-solid fa-check"></i> {{ __('support.official_solution') }}</span>
                                @endif
                                <div style="font-size: 0.78rem; color: var(--myads-text-muted, #64748b);">
                                    {{ $reply->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <!-- Mark Solution Action -->
                        @if(Auth::check() && (Auth::id() === $topic->user_id || $isStaff))
                            <form action="{{ route('support.mark_solution', ['topicId' => $topic->id, 'replyId' => $reply->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-mark-solution">
                                    <i class="fa-solid fa-check-double"></i>
                                    <span>{{ $reply->is_solution ? __('support.unmark_solution') : __('support.mark_as_solution') }}</span>
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="reply-body">
                        {{ $reply->content }}
                    </div>
                </div>
            @empty
                <div style="background: var(--myads-surface, #ffffff); border-radius: 16px; padding: 24px; text-align: center; color: var(--myads-text-muted, #64748b); border: 1px dashed var(--myads-surface-border, #cbd5e1);">
                    <i class="fa-solid fa-comment-dots" style="font-size: 2rem; margin-bottom: 8px; color: #cbd5e1;"></i>
                    <p style="margin: 0;">لا توجد أي ردود حتى الآن. كن أول من يساعد بالإجابة!</p>
                </div>
            @endforelse

            <!-- Add Reply Form -->
            @auth
                @if(!$topic->is_locked)
                    <div class="reply-form-card">
                        <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 14px 0; color: var(--myads-text-main, #1e293b);">
                            <i class="fa-solid fa-reply" style="color: var(--myads-primary, #615dfa);"></i>
                            {{ __('support.write_reply') }}
                        </h3>
                        <form action="{{ route('support.reply', $topic->id) }}" method="POST">
                            @csrf
                            <textarea name="content" class="reply-textarea" placeholder="{{ __('support.write_reply') }}" required></textarea>
                            <button type="submit" class="btn-reply-submit">
                                <i class="fa-solid fa-paper-plane"></i>
                                <span>{{ __('support.post_reply') }}</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div style="padding: 16px; background: rgba(245, 158, 11, 0.1); border-radius: 14px; color: #d97706; font-weight: 700; margin-top: 20px; text-align: center;">
                        <i class="fa-solid fa-lock"></i> هذا الموضوع مغلق ولا يقبل ردوداً جديدة.
                    </div>
                @endif
            @else
                <div style="padding: 24px; background: var(--myads-surface, #ffffff); border-radius: 18px; text-align: center; margin-top: 24px; border: 1px solid var(--myads-surface-border, #e2e8f0);">
                    <p style="color: var(--myads-text-muted, #64748b); margin-bottom: 12px; font-weight: 600;">
                        يرجى تسجيل الدخول لتتمكن من إضافة إجابتك أو استفسارك.
                    </p>
                    <a href="{{ route('login') }}" class="btn-reply-submit">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>{{ __('messages.login') }}</span>
                    </a>
                </div>
            @endauth
        </div>

        <!-- Sidebar -->
        <div>
            @if($relatedTopics->count() > 0)
                <div class="sidebar-box">
                    <h4 class="sidebar-title">
                        <i class="fa-solid fa-signs-post" style="color: var(--myads-primary, #615dfa);"></i>
                        أسئلة مشابهة في نفس القسم
                    </h4>
                    <div class="related-list">
                        @foreach($relatedTopics as $rel)
                            <a href="{{ route('support.topic', ['id' => $rel->id, 'slug' => $rel->slug]) }}">
                                <i class="fa-solid fa-arrow-left" style="font-size: 0.75rem; color: #94a3b8; margin-left: 4px;"></i>
                                {{ $rel->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="sidebar-box" style="background: linear-gradient(135deg, rgba(97, 93, 250, 0.08) 0%, rgba(35, 210, 226, 0.06) 100%); border-color: rgba(97, 93, 250, 0.2);">
                <h4 class="sidebar-title">
                    <i class="fa-solid fa-headset" style="color: var(--myads-primary, #615dfa);"></i>
                    لم تجد حلاً لمشكلتك؟
                </h4>
                <p style="font-size: 0.88rem; color: var(--myads-text-muted, #64748b); line-height: 1.5; margin-bottom: 16px;">
                    يمكنك فتح تذكرة دعم فني مباشرة مع فريق الإدارة لمتابعة المسائل المالية والتقنية الخاصة.
                </p>
                <a href="{{ route('tickets.create') }}" style="background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%); color: #ffffff; padding: 10px 20px; border-radius: 9999px; text-decoration: none; font-weight: 700; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-ticket"></i>
                    <span>{{ __('support.open_new_ticket') }}</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
