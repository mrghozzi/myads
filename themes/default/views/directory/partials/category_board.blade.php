@if($categoryBoard->isNotEmpty())
    <div class="widget-box directory-side-card directory-category-board">
        <p class="widget-box-title">{{ __('messages.cat_s') }}</p>

        <div class="widget-box-content">
            <div class="directory-category-board-list">
                @foreach($categoryBoard as $entry)
                    <article class="directory-category-board-item">
                        <div class="directory-category-board-header">
                            <a class="directory-category-board-link" href="{{ route('directory.category.legacy', $entry['category']->id) }}">
                                <span class="directory-category-board-icon">
                                    <i class="fa fa-folder-open" aria-hidden="true"></i>
                                </span>

                                <span class="directory-category-board-copy">
                                    <strong>{{ $entry['category']->name }}</strong>

                                    @if($entry['category']->txt)
                                        <span>{{ \Illuminate\Support\Str::limit(strip_tags($entry['category']->txt), 80) }}</span>
                                    @endif
                                </span>
                            </a>

                            <span class="directory-category-board-count">{{ $entry['listing_count'] }}</span>
                        </div>

                        @if($entry['children']->isNotEmpty())
                            <div class="directory-category-pill-list">
                                @foreach($entry['children'] as $child)
                                    <a class="directory-category-pill" href="{{ route('directory.category.legacy', $child['category']->id) }}">
                                        {{ $child['category']->name }}
                                        <span>{{ $child['listing_count'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </div>
@endif
