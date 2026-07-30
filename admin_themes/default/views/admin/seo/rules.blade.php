@extends('admin::layouts.admin')

@section('title', __('messages.seo_rules'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="seo-shell">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.seo.index') }}">{{ __('messages.seo_dashboard') }}</a></li>
                <li>{{ __('messages.seo_rules') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.seo_nav_rules') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.seo_rules') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.seo_rules_intro') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="admin-toolbar-row w-100">
                    <a href="{{ route('admin.seo.index') }}" class="btn btn-light">
                        <i class="feather-activity me-2"></i>{{ __('messages.seo_nav_dashboard') }}
                    </a>
                </div>
            </div>
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.seo_existing_rules') }}</span>
                    <span class="admin-summary-value">{{ $rules->count() }}</span>
                </div>
            </div>
        </div>
    </section>

    @include('admin::admin.seo.partials.nav')
    @include('admin::admin.seo.partials.alerts')

    <div class="card seo-card mb-4">
        <div class="card-header border-bottom-0 pb-0">
            <h5 class="card-title text-primary"><i class="feather-plus-circle me-2"></i>{{ __('messages.seo_create_rule') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.seo.rules.store') }}" method="POST" class="row g-3">
                @csrf
                @include('admin::admin.seo.rules_form', [
                    'rule' => null,
                    'supportedScopes' => $supportedScopes,
                    'schemaTypes' => $schemaTypes,
                    'twitterCards' => $twitterCards,
                    'prefix' => 'new',
                ])
                <div class="col-12 d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>{{ __('messages.seo_create_rule_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card seo-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="feather-shield me-2 text-primary"></i>{{ __('messages.seo_existing_rules') }}</h5>
            <span class="badge bg-soft-primary text-primary fs-6">{{ __('messages.seo_total_count', ['count' => $rules->count()]) }}</span>
        </div>
        <div class="card-body">
            @forelse($rules as $rule)
                <details class="mb-3 rounded-3 p-3 shadow-xs" style="background: var(--admin-premium-surface); border: 1px solid var(--admin-premium-border);">
                    <summary class="d-flex flex-wrap align-items-center justify-content-between gap-2" style="cursor: pointer; list-style: none;">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <i class="feather-chevron-right text-muted me-1"></i>
                            <span class="fw-bold text-dark dark:text-light">{{ $supportedScopes[$rule->scope_key] ?? $rule->scope_key }}</span>
                            <span class="badge bg-soft-secondary text-dark">{{ $rule->scope_key }}</span>
                            @if($rule->content_type && $rule->content_id)
                                <span class="badge bg-soft-primary text-primary"><i class="feather-box me-1"></i>{{ $rule->content_type }} #{{ $rule->content_id }}</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="seo-pill {{ $rule->is_active ? 'ok' : 'warn' }}">
                                <i class="feather-{{ $rule->is_active ? 'check-circle' : 'pause-circle' }}"></i>
                                {{ $rule->is_active ? __('messages.seo_rule_active') : __('messages.seo_rule_inactive') }}
                            </span>
                            <span class="seo-pill {{ $rule->indexable === false ? 'bad' : ($rule->indexable === true ? 'ok' : 'warn') }}">
                                {{ $rule->indexable === false ? __('messages.seo_noindex') : ($rule->indexable === true ? __('messages.seo_index') : __('messages.seo_inherit')) }}
                            </span>
                        </div>
                    </summary>

                    <form action="{{ route('admin.seo.rules.update', $rule) }}" method="POST" class="row g-3 mt-3 pt-3 border-top">
                        @csrf
                        @method('PUT')
                        @include('admin::admin.seo.rules_form', [
                            'rule' => $rule,
                            'supportedScopes' => $supportedScopes,
                            'schemaTypes' => $schemaTypes,
                            'twitterCards' => $twitterCards,
                            'prefix' => 'rule_' . $rule->id,
                        ])
                        <div class="col-12 d-flex justify-content-between align-items-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-2"></i>{{ __('messages.seo_update_rule') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="if(confirm(@js(__('messages.seo_delete_rule_confirm')))) { document.getElementById('delete-rule-form-{{ $rule->id }}').submit(); }">
                                <i class="feather-trash-2 me-2"></i>{{ __('messages.delete') }}
                            </button>
                        </div>
                    </form>
                    <form id="delete-rule-form-{{ $rule->id }}" action="{{ route('admin.seo.rules.delete', $rule) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </details>
            @empty
                <div class="rounded-3 p-4 text-muted text-center border" style="background: rgba(248, 250, 252, 0.5);">
                    <i class="feather-shield-off fs-1 d-block mb-2 text-muted"></i>
                    {{ __('messages.seo_no_rules') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
