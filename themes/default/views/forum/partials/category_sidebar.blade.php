@if($sidebarCategories->isNotEmpty())
    <div class="widget-box forum-sidebar-card forum-category-board">
        <p class="widget-box-title">{{ __('messages.cat_s') }}</p>

        <div class="widget-box-content">
            <div class="forum-category-board-list">
                @foreach($sidebarCategories as $entry)
                    <article
                        class="forum-category-board-item{{ $entry['is_active'] ? ' is-active' : '' }}"
                        data-forum-category-id="{{ $entry['category']->id }}"
                        data-topic-count="{{ $entry['topic_count'] }}"
                    >
                        <div class="forum-category-board-header">
                            <a
                                class="forum-category-board-link"
                                href="{{ route('forum.category', $entry['category']->id) }}"
                                @if($entry['is_active']) aria-current="page" @endif
                            >
                                <span class="forum-category-board-icon">
                                    <i class="fa {{ $entry['category']->icons }}" aria-hidden="true"></i>
                                </span>

                                <span class="forum-category-board-copy">
                                    <strong>{{ $entry['category']->name }}</strong>

                                    @if($entry['description'] !== '')
                                        <span>{{ $entry['description'] }}</span>
                                    @endif
                                </span>
                            </a>

                            <span class="forum-category-board-count">{{ $entry['topic_count'] }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
@endif
