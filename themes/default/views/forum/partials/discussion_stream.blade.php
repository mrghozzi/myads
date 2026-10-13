<div id="forumDiscussionList" class="forum-modern-discussion-list">
    @forelse($topics as $topic)
        @include('theme::forum.partials.discussion_item', ['topic' => $topic])
    @empty
        <div class="forum-modern-empty-card">
            <div class="forum-modern-empty-icon">
                <i class="fa-regular fa-comments fa-comments-o"></i>
            </div>
            <h4 class="fw-bold mb-2">{{ __('messages.no_topics_found') ?? 'No discussions found' }}</h4>
            <p class="text-muted mb-4">{{ __('messages.forum_empty_category_desc') ?? 'Be the first to start a discussion in this topic area!' }}</p>
            @auth
                <button type="button" class="btn btn-primary fw-bold px-4 py-2" onclick="ForumComposer.open('topic')">
                    <i class="fa fa-plus me-1"></i> {{ __('messages.w_new_tpc') ?? 'Start a Discussion' }}
                </button>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary fw-bold px-4 py-2">
                    {{ __('messages.login_to_participate') ?? 'Login to Participate' }}
                </a>
            @endauth
        </div>
    @endforelse
</div>

@if(isset($topics) && method_exists($topics, 'hasPages') && $topics->hasPages())
    <div class="forum-modern-pagination-wrap mt-4 text-center">
        @if($topics->hasMorePages())
            <button type="button" id="forumLoadMoreBtn" class="btn btn-light fw-bold px-4 py-2 shadow-sm rounded-pill infinite-scroll-trigger" data-next-url="{{ $topics->nextPageUrl() }}">
                <i class="fa fa-chevron-down me-1"></i> {{ __('messages.load_more') ?? 'Load More Discussions' }}
            </button>
        @endif
    </div>
@endif
