@extends('admin::layouts.admin')

@section('title', __('messages.edit_terms') ?? 'Edit Terms')
@section('admin_shell_header_mode', 'hidden')

@section('content')
@php
    $defaultTermsCollection = collect($defaultTerms);
    $extraTermsCollection = collect($terms)->reject(function ($value, $key) use ($defaultTerms) {
        return array_key_exists($key, $defaultTerms);
    });
    $totalTerms = $defaultTermsCollection->count();
    $translatedCount = $defaultTermsCollection->filter(function ($defaultValue, $key) use ($terms) {
        return trim((string) ($terms[$key] ?? '')) !== '';
    })->count();
    $missingCount = max($totalTerms - $translatedCount, 0);
    $extraCount = $extraTermsCollection->count();
@endphp

<div class="admin-page translation-workspace">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.languages') }}">{{ __('messages.languages') }}</a></li>
                <li>{{ __('messages.edit_terms') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.languages') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.edit_terms') }} ({{ $language->name }})</h1>
            <p class="admin-hero__copy">{{ __('messages.translation_terms_admin_help') ?? 'Review the English source text and write the translated value without needing horizontal scrolling.' }}</p>

            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.translation_total_terms') ?? 'Total Terms' }}</span>
                    <span class="admin-stat-value" data-translation-total>{{ number_format($totalTerms) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.translation_translated') ?? 'Translated' }}</span>
                    <span class="admin-stat-value" data-translation-translated>{{ number_format($translatedCount) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.translation_missing') ?? 'Missing' }}</span>
                    <span class="admin-stat-value" data-translation-missing>{{ number_format($missingCount) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.translation_extra_keys') ?? 'Extra Keys' }}</span>
                    <span class="admin-stat-value" data-translation-extra>{{ number_format($extraCount) }}</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="admin-toolbar-row w-100">
                    <a href="{{ route('admin.languages') }}" class="btn btn-light">
                        <i class="feather-arrow-left me-2"></i>{{ __('messages.back') ?? 'Back' }}
                    </a>
                    <button type="submit" form="languageTermsForm" class="btn btn-primary">
                        <i class="feather-save me-2"></i>{{ __('messages.save_changes') ?? 'Save Changes' }}
                    </button>
                </div>
                <div class="admin-summary-grid w-100">
                    <div class="admin-summary-card">
                        <span class="admin-summary-label">{{ __('messages.code') }}</span>
                        <span class="admin-summary-value">{{ strtoupper($language->o_valuer) }}</span>
                    </div>
                    <div class="admin-summary-card">
                        <span class="admin-summary-label">{{ __('messages.progress') ?? 'Progress' }}</span>
                        <span class="admin-summary-value" data-translation-progress-label>{{ $translatedCount }}/{{ $totalTerms }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="languageTermsForm" action="{{ route('admin.languages.terms.update', $language->id) }}" method="POST" class="translation-workspace__form">
        @csrf

        <section class="admin-panel translation-toolbar-panel">
            <div class="admin-panel__body">
                <div class="translation-toolbar">
                    <div class="translation-toolbar__search">
                        <label for="translation-search" class="admin-form-label">{{ __('messages.search') ?? 'Search' }}</label>
                        <div class="position-relative">
                            <i class="feather-search translation-toolbar__search-icon"></i>
                            <input
                                id="translation-search"
                                type="search"
                                class="form-control"
                                placeholder="{{ __('messages.translation_search_terms') ?? 'Search by key, English default, or translation' }}"
                                data-translation-search
                            >
                        </div>
                    </div>

                    <div class="translation-toolbar__filters">
                        <span class="admin-form-label d-block">{{ __('messages.status') ?? 'Status' }}</span>
                        <div class="translation-filter-group" role="group" aria-label="{{ __('messages.status') ?? 'Status' }}">
                            <button type="button" class="translation-filter-btn is-active" data-translation-filter="all">{{ __('messages.all') ?? 'All' }}</button>
                            <button type="button" class="translation-filter-btn" data-translation-filter="missing">{{ __('messages.translation_missing') ?? 'Missing' }}</button>
                            <button type="button" class="translation-filter-btn" data-translation-filter="translated">{{ __('messages.translation_translated') ?? 'Translated' }}</button>
                            <button type="button" class="translation-filter-btn" data-translation-filter="extra">{{ __('messages.translation_extra_keys') ?? 'Extra Keys' }}</button>
                        </div>
                    </div>

                    <div class="translation-toolbar__meta">
                        <div class="translation-progress">
                            <div class="translation-progress__meta">
                                <span class="translation-progress__label">{{ __('messages.progress') ?? 'Progress' }}</span>
                                <span class="translation-progress__value" data-translation-progress-label>{{ $translatedCount }}/{{ $totalTerms }}</span>
                            </div>
                            <div class="progress">
                                <div
                                    class="progress-bar"
                                    role="progressbar"
                                    style="width: {{ $totalTerms > 0 ? ($translatedCount / $totalTerms) * 100 : 0 }}%;"
                                    aria-valuenow="{{ $translatedCount }}"
                                    aria-valuemin="0"
                                    aria-valuemax="{{ $totalTerms }}"
                                    data-translation-progress-bar
                                ></div>
                            </div>
                        </div>

                        <div class="translation-toolbar__result">
                            <span class="translation-toolbar__result-label">{{ __('messages.search_results') ?? 'Search Results' }}</span>
                            <strong data-translation-visible-count>{{ $totalTerms + $extraCount }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="admin-panel translation-section" data-translation-section="default">
            <div class="admin-panel__header">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.translation_default_reference') ?? 'English Default' }}</span>
                    <h2 class="admin-panel__title">{{ __('messages.edit_terms') }}</h2>
                </div>
                <div class="admin-chip-list">
                    <span class="admin-chip"><i class="feather-check-circle"></i><span data-translation-translated>{{ $translatedCount }}</span></span>
                    <span class="admin-chip"><i class="feather-alert-circle"></i><span data-translation-missing>{{ $missingCount }}</span></span>
                </div>
            </div>

            <div class="admin-panel__body">
                <div class="translation-accordion">
                    @foreach($defaultTermsCollection as $key => $defaultValue)
                        @php
                            $currentValue = old('terms.' . $key, $terms[$key] ?? '');
                            $isTranslated = trim((string) $currentValue) !== '';
                        @endphp
                        <details
                            class="translation-card {{ $isTranslated ? 'is-translated' : 'is-missing' }}"
                            data-translation-card
                            data-translation-type="default"
                            data-translation-key="{{ strtolower($key) }}"
                            data-translation-state="{{ $isTranslated ? 'translated' : 'missing' }}"
                        >
                            <summary class="translation-card__summary">
                                <div class="translation-card__summary-main">
                                    <span class="translation-card__key">{{ $key }}</span>
                                    <span class="translation-card__preview">{{ \Illuminate\Support\Str::limit((string) $defaultValue, 160) }}</span>
                                </div>
                                <div class="translation-card__summary-meta">
                                    <span class="translation-status {{ $isTranslated ? 'translation-status--translated' : 'translation-status--missing' }}" data-translation-status>
                                        {{ $isTranslated ? __('messages.translation_status_translated') : __('messages.translation_status_missing') }}
                                    </span>
                                    <span class="translation-card__chevron" aria-hidden="true">
                                        <i class="feather-chevron-down"></i>
                                    </span>
                                </div>
                            </summary>
                            <div class="translation-card__body">
                                <div class="translation-card__grid">
                                    <div class="translation-source">
                                        <span class="translation-card__label">{{ __('messages.translation_default_reference') ?? 'English Default' }}</span>
                                        <div class="translation-source__content">{{ $defaultValue }}</div>
                                    </div>
                                    <div>
                                        <label class="translation-card__label" for="translation-{{ md5($key) }}">{{ __('messages.translation_value') ?? 'Translation Value' }}</label>
                                        <textarea
                                            id="translation-{{ md5($key) }}"
                                            name="terms[{{ $key }}]"
                                            class="form-control translation-textarea"
                                            rows="5"
                                            placeholder="{{ __('messages.enter_translation') ?? 'Enter translation...' }}"
                                            data-translation-input
                                        >{{ $currentValue }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="admin-panel translation-section" data-translation-section="extra">
            <div class="admin-panel__header">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.translation_status_extra') ?? 'Extra Key' }}</span>
                    <h2 class="admin-panel__title">{{ __('messages.translation_extra_keys') ?? 'Extra Keys' }}</h2>
                </div>
                <div class="admin-chip-list">
                    <span class="admin-chip"><i class="feather-layers"></i><span data-translation-extra>{{ $extraCount }}</span></span>
                </div>
            </div>

            <div class="admin-panel__body">
                @if($extraCount > 0)
                    <div class="translation-accordion">
                        @foreach($extraTermsCollection as $key => $value)
                            @php $currentValue = old('terms.' . $key, $value); @endphp
                            <details
                                class="translation-card is-extra"
                                data-translation-card
                                data-translation-type="extra"
                                data-translation-key="{{ strtolower($key) }}"
                                data-translation-state="extra"
                            >
                                <summary class="translation-card__summary">
                                    <div class="translation-card__summary-main">
                                        <span class="translation-card__key">{{ $key }}</span>
                                        <span class="translation-card__preview">{{ \Illuminate\Support\Str::limit((string) $currentValue, 160) }}</span>
                                    </div>
                                    <div class="translation-card__summary-meta">
                                        <span class="translation-status translation-status--extra" data-translation-status>
                                            {{ __('messages.translation_status_extra') ?? 'Extra Key' }}
                                        </span>
                                        <span class="translation-card__chevron" aria-hidden="true">
                                            <i class="feather-chevron-down"></i>
                                        </span>
                                    </div>
                                </summary>
                                <div class="translation-card__body">
                                    <div class="translation-card__grid">
                                        <div class="translation-source">
                                            <span class="translation-card__label">{{ __('messages.translation_default_reference') ?? 'English Default' }}</span>
                                            <div class="translation-source__content translation-source__content--warning">
                                                {{ __('messages.key_not_in_default') ?? 'Not in default English file' }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="translation-card__label" for="translation-extra-{{ md5($key) }}">{{ __('messages.translation_value') ?? 'Translation Value' }}</label>
                                            <textarea
                                                id="translation-extra-{{ md5($key) }}"
                                                name="terms[{{ $key }}]"
                                                class="form-control translation-textarea"
                                                rows="5"
                                                placeholder="{{ __('messages.enter_translation') ?? 'Enter translation...' }}"
                                                data-translation-input
                                            >{{ $currentValue }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </details>
                        @endforeach
                    </div>
                @else
                    <div class="translation-empty-state is-inline">
                        <span class="admin-avatar-circle"><i class="feather-check-circle"></i></span>
                        <h4>{{ __('messages.translation_extra_keys') ?? 'Extra Keys' }}</h4>
                        <p class="admin-muted mb-0">{{ __('messages.no_data') ?? 'No data available.' }}</p>
                    </div>
                @endif
            </div>
        </section>

        <section class="translation-empty-state" data-translation-empty hidden>
            <span class="admin-avatar-circle"><i class="feather-search"></i></span>
            <h4>{{ __('messages.translation_no_matches') ?? 'No matching terms found.' }}</h4>
            <p class="admin-muted mb-0">{{ __('messages.translation_search_terms') ?? 'Search by key, English default, or translation' }}</p>
        </section>

        <section class="admin-panel">
            <div class="admin-panel__body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="admin-muted">{{ __('messages.translation_terms_admin_help') ?? 'Review the English source text and write the translated value without needing horizontal scrolling.' }}</div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.languages') }}" class="btn btn-light">
                            <i class="feather-arrow-left me-2"></i>{{ __('messages.back') ?? 'Back' }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i>{{ __('messages.save_changes') ?? 'Save Changes' }}
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const workspace = document.querySelector('.translation-workspace');

    if (!workspace) {
        return;
    }

    const searchInput = workspace.querySelector('[data-translation-search]');
    const filterButtons = Array.from(workspace.querySelectorAll('[data-translation-filter]'));
    const cards = Array.from(workspace.querySelectorAll('[data-translation-card]'));
    const textareas = Array.from(workspace.querySelectorAll('[data-translation-input]'));
    const totalLabels = workspace.querySelectorAll('[data-translation-total]');
    const translatedLabels = workspace.querySelectorAll('[data-translation-translated]');
    const missingLabels = workspace.querySelectorAll('[data-translation-missing]');
    const extraLabels = workspace.querySelectorAll('[data-translation-extra]');
    const progressLabels = workspace.querySelectorAll('[data-translation-progress-label]');
    const progressBar = workspace.querySelector('[data-translation-progress-bar]');
    const visibleCount = workspace.querySelector('[data-translation-visible-count]');
    const emptyState = workspace.querySelector('[data-translation-empty]');
    const sections = Array.from(workspace.querySelectorAll('[data-translation-section]'));

    let activeFilter = 'all';

    function autosizeTextarea(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.max(textarea.scrollHeight, 180) + 'px';
    }

    function updateCardState(card) {
        const input = card.querySelector('[data-translation-input]');
        const status = card.querySelector('[data-translation-status]');

        if (!input || !status) {
            return;
        }

        const type = card.dataset.translationType;
        const rawValue = input.value.trim();
        const isTranslated = rawValue !== '';
        const nextState = type === 'extra' ? 'extra' : (isTranslated ? 'translated' : 'missing');

        card.dataset.translationState = nextState;
        card.classList.toggle('is-translated', nextState === 'translated');
        card.classList.toggle('is-missing', nextState === 'missing');
        card.classList.toggle('is-extra', nextState === 'extra');

        if (nextState === 'translated') {
            status.textContent = @json(__('messages.translation_status_translated') ?? 'Translated');
            status.className = 'translation-status translation-status--translated';
        } else if (nextState === 'missing') {
            status.textContent = @json(__('messages.translation_status_missing') ?? 'Missing');
            status.className = 'translation-status translation-status--missing';
        } else {
            status.textContent = @json(__('messages.translation_status_extra') ?? 'Extra Key');
            status.className = 'translation-status translation-status--extra';
        }
    }

    function updateCounters() {
        const defaultCards = cards.filter(function (card) {
            return card.dataset.translationType === 'default';
        });
        const extraCards = cards.filter(function (card) {
            return card.dataset.translationType === 'extra';
        });
        const translated = defaultCards.filter(function (card) {
            return card.dataset.translationState === 'translated';
        }).length;
        const total = defaultCards.length;
        const missing = Math.max(total - translated, 0);
        const extra = extraCards.length;
        const progress = total > 0 ? Math.round((translated / total) * 100) : 0;

        totalLabels.forEach(function (node) {
            node.textContent = total;
        });
        translatedLabels.forEach(function (node) {
            node.textContent = translated;
        });
        missingLabels.forEach(function (node) {
            node.textContent = missing;
        });
        extraLabels.forEach(function (node) {
            node.textContent = extra;
        });
        progressLabels.forEach(function (node) {
            node.textContent = translated + '/' + total;
        });

        if (progressBar) {
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', String(translated));
            progressBar.setAttribute('aria-valuemax', String(total));
        }
    }

    function applyFilters() {
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        let visible = 0;

        cards.forEach(function (card) {
            const type = card.dataset.translationType;
            const state = card.dataset.translationState;
            const sourceNode = card.querySelector('.translation-source__content');
            const inputNode = card.querySelector('[data-translation-input]');
            const sourceText = sourceNode ? sourceNode.textContent.toLowerCase() : '';
            const valueText = inputNode ? inputNode.value.toLowerCase() : '';
            const haystack = [
                card.dataset.translationKey || '',
                sourceText,
                valueText
            ].join(' ');

            const matchesQuery = query === '' || haystack.includes(query);
            let matchesFilter = activeFilter === 'all';

            if (activeFilter === 'missing') {
                matchesFilter = type === 'default' && state === 'missing';
            } else if (activeFilter === 'translated') {
                matchesFilter = type === 'default' && state === 'translated';
            } else if (activeFilter === 'extra') {
                matchesFilter = type === 'extra';
            }

            const isVisible = matchesQuery && matchesFilter;
            card.hidden = !isVisible;
            card.classList.toggle('is-search-match', query !== '' && isVisible);

            if (query !== '') {
                if (isVisible) {
                    card.open = true;
                    card.dataset.searchOpen = 'true';
                } else if (card.dataset.searchOpen === 'true') {
                    card.open = false;
                    delete card.dataset.searchOpen;
                }
            } else if (card.dataset.searchOpen === 'true') {
                card.open = false;
                delete card.dataset.searchOpen;
            }

            if (isVisible) {
                visible += 1;
            }
        });

        sections.forEach(function (section) {
            const type = section.dataset.translationSection;
            const hasVisibleCards = cards.some(function (card) {
                return card.dataset.translationType === type && !card.hidden;
            });
            section.hidden = !hasVisibleCards;
        });

        if (visibleCount) {
            visibleCount.textContent = visible;
        }

        if (emptyState) {
            emptyState.hidden = visible !== 0;
        }
    }

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            activeFilter = button.dataset.translationFilter || 'all';
            filterButtons.forEach(function (candidate) {
                candidate.classList.toggle('is-active', candidate === button);
            });
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    textareas.forEach(function (textarea) {
        autosizeTextarea(textarea);
        textarea.addEventListener('input', function () {
            autosizeTextarea(textarea);
            const card = textarea.closest('[data-translation-card]');
            if (!card) {
                return;
            }
            updateCardState(card);
            updateCounters();
            applyFilters();
        });
    });

    cards.forEach(function (card) {
        updateCardState(card);
    });

    updateCounters();
    applyFilters();
});
</script>
@endpush
