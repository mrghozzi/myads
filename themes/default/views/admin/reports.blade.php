@extends('theme::layouts.admin')

@section('title', __('messages.reports'))

@php
    $reportItems = $reportItems ?? collect();
    $reportStats = $reportStats ?? ['total' => $reports->total() ?? 0, 'pending' => 0, 'reviewed' => 0];
@endphp

@section('content')
@include('theme::admin.partials.extension_hub_styles')
@include('theme::admin.partials.reports_hub_styles')

<div class="main-content container-lg px-4">
    <section class="extension-hub extension-hub--reports">
        <div class="row g-0 align-items-center mb-4">
            <div class="col-12">
                <div class="extension-hub__hero">
                    <span class="extension-hub__hero-icon">
                        <i class="fa-solid fa-flag"></i>
                    </span>

                    <div class="row align-items-center g-4 position-relative">
                        <div class="col-xl-7">
                            <span class="extension-hub__hero-kicker">
                                <i class="feather-shield"></i>
                                {{ __('messages.reports') }}
                            </span>
                            <h1 class="extension-hub__hero-title mt-4">{{ __('messages.reports') }}</h1>
                            <p class="extension-hub__hero-desc">{{ __('messages.reports_desc') }}</p>
                        </div>
                        <div class="col-xl-5 text-xl-end">
                            <div class="extension-hub__hero-panel reports-hub__hero-panel">
                                <span class="extension-hub__hero-panel-icon">
                                    <i class="feather-alert-octagon"></i>
                                </span>
                                <div>
                                    <span class="extension-hub__hero-panel-label">{{ __('messages.pending') }}</span>
                                    <span class="extension-hub__hero-panel-value">
                                        {{ $reportStats['pending'] }} {{ __('messages.reports') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 extension-hub__stats mb-4">
            <div class="col-md-4">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-layers"></i></span>
                        {{ __('messages.total') }} {{ __('messages.reports') }}
                    </div>
                    <div class="extension-hub__stat-value">{{ $reportStats['total'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-clock"></i></span>
                        {{ __('messages.pending') }}
                    </div>
                    <div class="extension-hub__stat-value">{{ $reportStats['pending'] }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-check-circle"></i></span>
                        {{ __('messages.reviewed') }}
                    </div>
                    <div class="extension-hub__stat-value">{{ $reportStats['reviewed'] }}</div>
                </div>
            </div>
        </div>

        <div class="extension-hub__surface p-4 p-xl-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="extension-hub__section-title">{{ __('messages.reports_list') }}</h2>
                    <p class="extension-hub__section-subtitle">{{ __('messages.reports_desc') }}</p>
                </div>
                <span class="extension-hub__count-pill">
                    <i class="feather-flag"></i>
                    {{ $reports->total() }} {{ __('messages.reports') }}
                </span>
            </div>

            @if($reportItems->isEmpty())
                <div class="extension-hub__empty">
                    <div class="extension-hub__empty-icon">
                        <i class="feather-shield-off"></i>
                    </div>
                    <h3 class="extension-hub__section-title mb-2">{{ __('messages.no_data') }}</h3>
                    <p class="extension-hub__section-subtitle reports-hub__empty-copy">{{ __('messages.reports_desc') }}</p>
                </div>
            @else
                <div class="reports-hub__list">
                    @foreach($reportItems as $item)
                        @php
                            $reporter = $item['reporter'];
                            $targetUser = $item['target_user'];
                            $reporterInitial = $reporter && $reporter->username ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($reporter->username, 0, 1)) : 'G';
                        @endphp

                        <article class="extension-hub__list-card reports-hub__card {{ $item['is_pending'] ? 'reports-hub__card--pending' : '' }}">
                            <div class="d-flex flex-column flex-xl-row justify-content-between gap-4">
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                        <span class="extension-hub__status-badge {{ $item['is_pending'] ? 'extension-hub__update-badge' : 'extension-hub__status-badge extension-hub__status-badge--inactive' }}">
                                            <i class="{{ $item['is_pending'] ? 'feather-clock' : 'feather-check-circle' }}"></i>
                                            {{ $item['status_label'] }}
                                        </span>
                                        <span class="reports-hub__reference">#{{ $item['id'] }}</span>
                                        @if($item['target_label'])
                                            <span class="reports-hub__type-pill">
                                                <i class="{{ $item['target_icon'] }}"></i>
                                                {{ $item['target_label'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-xl-4">
                                            <div class="reports-hub__meta-card">
                                                <div class="reports-hub__label">
                                                    <i class="feather-user"></i>
                                                    {{ __('messages.reported_by') }}
                                                </div>

                                                <div class="reports-hub__person">
                                                    @if($reporter && $reporter->img)
                                                        <img src="{{ $reporter->avatarUrl() }}" alt="{{ $reporter->username }}" class="reports-hub__person-avatar" style="object-fit: cover; border: none; padding: 0;">
                                                    @else
                                                        <span class="reports-hub__person-avatar">{{ $reporterInitial }}</span>
                                                    @endif
                                                    <div class="min-w-0">
                                                        @if($reporter)
                                                            <p class="reports-hub__person-name">{{ $reporter->username }}</p>
                                                        @else
                                                            <p class="reports-hub__person-name">{{ __('messages.guest') }}</p>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($reporter)
                                                    <div class="reports-hub__actions mt-3">
                                                        <a href="{{ $item['reporter_profile_url'] }}" target="_blank" class="btn-extension-glass btn-extension-glass--muted">
                                                            <i class="feather-user"></i>
                                                            <span>{{ __('messages.view_profile') }}</span>
                                                        </a>
                                                        <a href="{{ $item['reporter_message_url'] }}" class="btn-extension-glass btn-extension-glass--primary">
                                                            <i class="feather-mail"></i>
                                                            <span>{{ __('messages.message') }}</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-xl-8">
                                            <div class="reports-hub__reason-card">
                                                <div class="reports-hub__label">
                                                    <i class="feather-alert-circle"></i>
                                                    {{ __('messages.reason') }}
                                                </div>
                                                <p class="reports-hub__reason">{{ $item['reason'] }}</p>

                                                <div class="reports-hub__label mt-4">
                                                    <i class="{{ $item['target_icon'] }}"></i>
                                                    {{ __('messages.report_content') }}
                                                </div>

                                                @if($item['target_missing'])
                                                    <div class="reports-hub__removed">
                                                        {{ __('messages.reported_content_removed') }}
                                                    </div>
                                                @else
                                                    @if($item['target_title'])
                                                        <h3 class="extension-hub__section-title mb-3">{{ $item['target_title'] }}</h3>
                                                    @endif

                                                    <div class="reports-hub__actions">
                                                        @if($item['preview_url'])
                                                            <a href="{{ $item['preview_url'] }}" target="_blank" class="btn-extension-glass btn-extension-glass--warning">
                                                                <i class="feather-external-link"></i>
                                                                <span>{{ $item['preview_label'] }}</span>
                                                            </a>
                                                        @endif

                                                        @if($targetUser)
                                                            @if($item['target_user_profile_url'] !== $item['preview_url'])
                                                                <a href="{{ $item['target_user_profile_url'] }}" target="_blank" class="btn-extension-glass btn-extension-glass--muted">
                                                                    <i class="feather-user"></i>
                                                                    <span>{{ __('messages.view_profile') }}</span>
                                                                </a>
                                                            @endif

                                                            <a href="{{ $item['target_user_message_url'] }}" class="btn-extension-glass btn-extension-glass--primary">
                                                                <i class="feather-mail"></i>
                                                                <span>{{ __('messages.message') }}</span>
                                                            </a>

                                                            <a href="{{ $item['target_user_admin_url'] }}" class="btn-extension-glass btn-extension-glass--success">
                                                                <i class="feather-edit-2"></i>
                                                                <span>{{ __('messages.edit') }}</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="reports-hub__action-col">
                                    @if($item['is_pending'])
                                        <a href="{{ route('admin.reports', ['wtid' => $item['id']]) }}" class="btn-extension-glass btn-extension-glass--warning" title="{{ __('messages.review') }}">
                                            <i class="feather-eye"></i>
                                            <span>{{ __('messages.review') }}</span>
                                        </a>
                                    @endif

                                    <form action="{{ route('admin.reports.delete', $item['id']) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete_report') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-extension-glass btn-extension-glass--danger">
                                            <i class="feather-trash-2"></i>
                                            <span>{{ __('messages.delete') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            @if($reports->hasPages())
                <div class="card-footer bg-transparent border-0 px-0 pt-4 pb-0">
                    {{ $reports->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
