@php
    $currentCategoryId = isset($category) ? $category->id : null;
@endphp

<div class="forum-mobile-nav-shell">
    <div class="widget-box forum-mobile-filters-card">
        <div class="widget-box-content">
            <div class="forum-mobile-nav-actions">
                @auth
                <a href="{{ route('forum.create') }}" class="button secondary forum-mobile-add-btn">
                    <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{{ __('messages.w_new_tpc') }}
                </a>
                @endauth

                @if(isset($categories_list) && $categories_list->isNotEmpty())
                    <div class="forum-mobile-category-dropdown">
                        <select class="forum-cat-select" onchange="if(this.value) window.location.href=this.value;">
                            <option value="{{ route('forum.index') }}" {{ is_null($currentCategoryId) ? 'selected' : '' }}>
                                {{ __('messages.All') }} {{ __('messages.cat_s') }}
                            </option>
                            @foreach($categories_list as $entry)
                                @php
                                    $cat = is_array($entry) ? $entry['category'] : $entry;
                                    $isActive = is_array($entry) ? $entry['is_active'] : ($currentCategoryId == $cat->id);
                                @endphp
                                <option value="{{ route('forum.category', $cat->id) }}" {{ $isActive ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa fa-chevron-down select-arrow"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
