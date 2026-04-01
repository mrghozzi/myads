@extends('theme::layouts.admin')

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

    @include('theme::admin.seo.partials.nav')
    @include('theme::admin.seo.partials.alerts')

    <div class="card seo-card mb-4">
        <div class="card-body">
            <h5 class="mb-3">{{ __('messages.seo_create_rule') }}</h5>
            <form action="{{ route('admin.seo.rules.store') }}" method="POST" class="row g-3">
                @csrf
                @include('theme::admin.seo.rules_form', [
                    'rule' => null,
                    'supportedScopes' => $supportedScopes,
                    'schemaTypes' => $schemaTypes,
                    'twitterCards' => $twitterCards,
                    'prefix' => 'new',
                ])
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>{{ __('messages.seo_create_rule_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card seo-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">{{ __('messages.seo_existing_rules') }}</h5>
                <span class="badge bg-light text-dark">{{ __('messages.seo_total_count', ['count' => $rules->count()]) }}</span>
            </div>

            @forelse($rules as $rule)
                <details class="mb-3 rounded-4 p-3" style="border: 1px solid rgba(148, 163, 184, 0.18);">
                    <summary class="d-flex flex-wrap align-items-center gap-2" style="cursor: pointer; list-style: none;">
                        <span class="fw-semibold">{{ $supportedScopes[$rule->scope_key] ?? $rule->scope_key }}</span>
                        <span class="badge bg-light text-dark">{{ $rule->scope_key }}</span>
                        @if($rule->content_type && $rule->content_id)
                            <span class="badge bg-soft-primary text-primary">{{ $rule->content_type }} #{{ $rule->content_id }}</span>
                        @endif
                        <span class="seo-pill {{ $rule->is_active ? 'ok' : 'warn' }}">{{ $rule->is_active ? __('messages.seo_rule_active') : __('messages.seo_rule_inactive') }}</span>
                        <span class="seo-pill {{ $rule->indexable === false ? 'bad' : ($rule->indexable === true ? 'ok' : 'warn') }}">
                            {{ $rule->indexable === false ? __('messages.seo_noindex') : ($rule->indexable === true ? __('messages.seo_index') : __('messages.seo_inherit')) }}
                        </span>
                    </summary>

                    <form action="{{ route('admin.seo.rules.update', $rule) }}" method="POST" class="row g-3 mt-3">
                        @csrf
                        @method('PUT')
                        @include('theme::admin.seo.rules_form', [
                            'rule' => $rule,
                            'supportedScopes' => $supportedScopes,
                            'schemaTypes' => $schemaTypes,
                            'twitterCards' => $twitterCards,
                            'prefix' => 'rule_' . $rule->id,
                        ])
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-2"></i>{{ __('messages.seo_update_rule') }}
                            </button>
                        </div>
                    </form>

                    <form action="{{ route('admin.seo.rules.delete', $rule) }}" method="POST" class="mt-2" onsubmit="return confirm(@js(__('messages.seo_delete_rule_confirm')));">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="feather-trash-2 me-2"></i>{{ __('messages.delete') }}
                        </button>
                    </form>
                </details>
            @empty
                <div class="rounded-4 p-4 text-muted text-center" style="background: rgba(248, 250, 252, 0.85);">
                    {{ __('messages.seo_no_rules') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
