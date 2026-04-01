@php
    $activeFilterCount = collect($filterState)
        ->except('logic')
        ->filter(fn ($value) => !($value === null || $value === ''))
        ->count();
@endphp

<div class="dropdown">
    <a class="btn btn-icon btn-light-brand position-relative" data-bs-toggle="dropdown" data-bs-offset="0, 12" data-bs-auto-close="outside">
        <i class="feather-filter"></i>
        @if($activeFilterCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">{{ $activeFilterCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end p-0 admin-filter-dropdown-menu" style="min-width: 360px; max-width: min(92vw, 360px);">
        <div class="p-3 border-bottom">
            <div class="d-flex align-items-start justify-content-between gap-3">
                <div>
                    <div class="dropdown-header px-0 pb-1 fw-bold text-uppercase fs-11 text-muted">{{ __('messages.advanced_filter') }}</div>
                    <div class="small text-muted">{{ __('messages.results_count', ['count' => $resultsCount]) }}</div>
                </div>
                <span class="badge bg-soft-primary text-primary">{{ strtoupper($filterState['logic'] ?? 'AND') }}</span>
            </div>
        </div>
        <form action="{{ $action }}" method="GET" class="p-3">
            <div class="mb-3">
                <label class="form-label">{{ __('messages.filter_logic') }}</label>
                <select name="logic" class="form-select form-select-sm">
                    <option value="and" {{ ($filterState['logic'] ?? 'and') === 'and' ? 'selected' : '' }}>{{ __('messages.filter_logic_and') }}</option>
                    <option value="or" {{ ($filterState['logic'] ?? 'and') === 'or' ? 'selected' : '' }}>{{ __('messages.filter_logic_or') }}</option>
                </select>
            </div>
            <div class="row g-3">
                @foreach($filterFields as $field)
                    <div class="col-12 col-md-6">
                        <label class="form-label">{{ $field['label'] }}</label>
                        @if($field['type'] === 'select')
                            <select name="{{ $field['name'] }}" class="form-select form-select-sm">
                                @foreach($field['options'] as $optionValue => $optionLabel)
                                    <option value="{{ $optionValue }}" {{ (string) ($filterState[$field['name']] ?? '') === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        @else
                            <input
                                type="{{ $field['type'] }}"
                                name="{{ $field['name'] }}"
                                value="{{ $filterState[$field['name']] ?? '' }}"
                                class="form-control form-control-sm"
                                @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                            >
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="form-check form-switch mt-3">
                <input class="form-check-input" type="checkbox" role="switch" id="saveFilterPreference-{{ $preferenceKey }}" name="save_preference" value="1">
                <label class="form-check-label" for="saveFilterPreference-{{ $preferenceKey }}">{{ __('messages.save_as_default_filter') }}</label>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary flex-fill">{{ __('messages.apply_filter') }}</button>
                <button type="button" class="btn btn-light" data-filter-reset data-reset-url="{{ $resetUrl }}">{{ __('messages.reset_filter') }}</button>
            </div>
        </form>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('click', function (event) {
                var resetButton = event.target.closest('[data-filter-reset]');

                if (!resetButton) {
                    return;
                }

                window.location.href = resetButton.getAttribute('data-reset-url');
            });
        </script>
    @endpush
@endonce
