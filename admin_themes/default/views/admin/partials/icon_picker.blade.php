@php
    $name = $name ?? 'icons';
    $selectedIcon = $selectedIcon ?? '';
    $placeholder = __('messages.select_icon') ?? 'Select Icon';
    $selectedParts = $selectedIcon ? preg_split('/\s+/', trim($selectedIcon)) : [];
    $selectedToken = $selectedParts ? end($selectedParts) : '';
    $selectedLabel = $selectedToken ? \Illuminate\Support\Str::after($selectedToken, 'fa-') : $placeholder;
    $selectedClass = $selectedIcon ?: 'fa-solid fa-icons';
@endphp

<div
    class="icon-picker"
    data-icon-picker
    data-placeholder="{{ $placeholder }}"
    data-search-placeholder="{{ __('messages.search') ?? 'Search' }} icons..."
    data-no-results="{{ __('messages.no_results') ?? 'No icons found' }}"
    data-current-group="{{ __('messages.current') ?? 'Current Selection' }}"
>
    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
        <label class="form-label mb-0">{{ __('messages.icon') }}</label>
        <span class="icon-picker__helper">{{ $placeholder }}</span>
    </div>

    <select name="{{ $name }}" class="form-select icon-picker__native" required>
        <option value="">{{ $placeholder }}</option>
        @if($selectedIcon)
            <option value="{{ $selectedIcon }}" data-icon="{{ $selectedIcon }}" selected hidden>{{ $selectedLabel }}</option>
        @endif
        @include('admin::admin.partials.icon_options', ['selectedIcon' => $selectedIcon])
    </select>

    <button type="button" class="icon-picker__trigger" aria-expanded="false">
        <span class="icon-picker__trigger-badge">
            <i class="{{ $selectedClass }}"></i>
        </span>
        <span class="icon-picker__trigger-content">
            <span class="icon-picker__trigger-label">{{ __('messages.icon') }}</span>
            <span class="icon-picker__trigger-value">{{ $selectedLabel }}</span>
        </span>
        <span class="icon-picker__trigger-arrow" aria-hidden="true">
            <i class="fa-solid fa-chevron-down"></i>
        </span>
    </button>

    <div class="icon-picker__panel" hidden>
        <div class="icon-picker__search-wrap">
            <span class="icon-picker__search-icon" aria-hidden="true">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="search" class="icon-picker__search" autocomplete="off">
        </div>
        <div class="icon-picker__results" data-icon-picker-results></div>
        <div class="icon-picker__empty" hidden>{{ __('messages.no_results') ?? 'No icons found' }}</div>
    </div>
</div>
