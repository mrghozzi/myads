@extends('theme::layouts.admin')

@section('title', __('messages.seo_rules'))

@section('content')
<div class="seo-shell">
    <div class="mb-4">
        <h3 class="mb-1">{{ __('messages.seo_rules') }}</h3>
        <p class="text-muted mb-0">{{ __('messages.seo_rules_intro') }}</p>
    </div>

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
