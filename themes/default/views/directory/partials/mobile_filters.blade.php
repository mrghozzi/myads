@php
    $currentCategoryId = isset($category) ? $category->id : null;
@endphp

<div class="directory-mobile-nav-shell">
    <div class="widget-box directory-mobile-filters-card">
        <div class="widget-box-content">
            <div class="directory-mobile-nav-actions">
                <a href="{{ route('directory.create') }}" class="button secondary directory-mobile-add-btn">
                    <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{{ __('messages.addWebsite') }}
                </a>

                @if(isset($categoryBoard) && $categoryBoard->isNotEmpty())
                    <div class="directory-mobile-category-dropdown">
                        <select class="directory-cat-select" onchange="if(this.value) window.location.href=this.value;">
                            <option value="{{ route('directory.index') }}" {{ is_null($currentCategoryId) ? 'selected' : '' }}>
                                {{ __('messages.All') }} {{ __('messages.cat_s') }}
                            </option>
                            @foreach($categoryBoard as $entry)
                                <option value="{{ route('directory.category.legacy', $entry['category']->id) }}" {{ $currentCategoryId == $entry['category']->id ? 'selected' : '' }}>
                                    {{ $entry['category']->name }}
                                </option>
                                @if($entry['children']->isNotEmpty())
                                    @foreach($entry['children'] as $child)
                                        <option value="{{ route('directory.category.legacy', $child['category']->id) }}" {{ $currentCategoryId == $child['category']->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;— {{ $child['category']->name }}
                                        </option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                        <i class="fa fa-chevron-down select-arrow"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
