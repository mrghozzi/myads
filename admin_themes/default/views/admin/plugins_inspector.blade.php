@extends('admin::layouts.admin')

@section('title', __('messages.plugins_inspector'))
@section('admin_shell_header_mode', 'hidden')

@php
    $actionCount = 0;
    foreach ($actions as $hook => $priorities) {
        foreach ($priorities as $callbacks) {
            $actionCount += count($callbacks);
        }
    }
    $filterCount = 0;
    foreach ($filters as $hook => $priorities) {
        foreach ($priorities as $callbacks) {
            $filterCount += count($callbacks);
        }
    }
@endphp

@section('content')
<div class="main-content container-lg px-4">
    <section class="extension-hub extension-hub--plugins mb-4">
        <div class="row g-0 align-items-center mb-4">
            <div class="col-12">
                <div class="extension-hub__hero" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); padding: 32px; border-radius: 24px; position: relative; overflow: hidden; color: #fff;">
                    <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
                        <div class="col-xl-8">
                            <span class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(255,255,255,0.15); font-size: 13px; font-weight: 600;">
                                <i class="feather-layers"></i>
                                {{ __('messages.plugins_inspector') }}
                            </span>
                            <h1 class="h2 text-white fw-bold mb-2">{{ __('messages.plugins_inspector') }}</h1>
                            <p class="mb-0 text-white-50 fs-15">{{ __('messages.plugins_inspector_desc') }}</p>
                        </div>
                        <div class="col-xl-4 text-xl-end">
                            <a href="{{ route('admin.plugins') }}" class="btn btn-light btn-lg fw-bold shadow-sm px-4 py-2-5" style="border-radius: 14px;">
                                <i class="feather-arrow-left me-2"></i> {{ __('messages.plugins') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 18px;">
                    <div class="card-body p-3-5">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fs-13 fw-semibold">{{ __('messages.registered_actions') }}</span>
                            <span class="avatar-tile rounded-3 bg-primary-subtle text-primary p-2">
                                <i class="feather-zap fs-18"></i>
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1">{{ count($actions) }} <span class="fs-13 text-muted">({{ $actionCount }} callbacks)</span></h3>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 18px;">
                    <div class="card-body p-3-5">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fs-13 fw-semibold">{{ __('messages.registered_filters') }}</span>
                            <span class="avatar-tile rounded-3 bg-info-subtle text-info p-2">
                                <i class="feather-filter fs-18"></i>
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1">{{ count($filters) }} <span class="fs-13 text-muted">({{ $filterCount }} callbacks)</span></h3>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 18px;">
                    <div class="card-body p-3-5">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fs-13 fw-semibold">{{ __('messages.active_editor_plugins') }}</span>
                            <span class="avatar-tile rounded-3 bg-success-subtle text-success p-2">
                                <i class="feather-edit-3 fs-18"></i>
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1">{{ count($editors) }}</h3>
                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 fs-12 fw-semibold">
                            <i class="feather-check-circle me-1"></i> Active: {{ $editors[$activeEditor] ?? $activeEditor }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 18px;">
                    <div class="card-body p-3-5">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fs-13 fw-semibold">{{ __('messages.active_plugins_count') }}</span>
                            <span class="avatar-tile rounded-3 bg-warning-subtle text-warning p-2">
                                <i class="feather-box fs-18"></i>
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1">{{ count($activePlugins) }} / {{ count($plugins) }}</h3>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ count($plugins) > 0 ? (count($activePlugins) / count($plugins)) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Text Editors Box -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 18px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-1"><i class="feather-edit-2 text-primary me-2"></i> {{ __('messages.active_editor_plugins') }}</h5>
                    <p class="text-muted fs-13 mb-0">Extensible Rich Text Editors registered via <code>RichTextEditorService</code> &amp; hooks</p>
                </div>
                <a href="{{ route('admin.settings') }}#tab-identity" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="feather-settings me-1"></i> Switch Editor
                </a>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach($editors as $key => $label)
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between {{ $key === $activeEditor ? 'border-primary bg-primary-subtle' : 'bg-light' }}">
                                <div>
                                    <h6 class="fw-bold mb-0 {{ $key === $activeEditor ? 'text-primary' : '' }}">{{ $label }}</h6>
                                    <small class="text-muted font-monospace fs-12">key: {{ $key }}</small>
                                </div>
                                @if($key === $activeEditor)
                                    <span class="badge bg-primary rounded-pill px-3 py-1 fs-12">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 fs-12">Registered</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Hooks Inspector Tabs -->
        <div class="card border-0 shadow-sm" style="border-radius: 18px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <ul class="nav nav-pills custom-inspector-tabs gap-2" id="inspectorTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-4 py-2" id="actions-tab" data-bs-toggle="tab" data-bs-target="#actions-panel" type="button" role="tab">
                                <i class="feather-zap me-1"></i> {{ __('messages.registered_actions') }} ({{ count($actions) }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-2" id="filters-tab" data-bs-toggle="tab" data-bs-target="#filters-panel" type="button" role="tab">
                                <i class="feather-filter me-1"></i> {{ __('messages.registered_filters') }} ({{ count($filters) }})
                            </button>
                        </li>
                    </ul>

                    <div class="position-relative" style="min-width: 260px;">
                        <i class="feather-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="hookSearchInput" class="form-control ps-5 rounded-pill border-0 bg-light fs-13" placeholder="Search hook names or callbacks...">
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="tab-content" id="inspectorTabsContent">
                    <!-- Actions Panel -->
                    <div class="tab-pane fade show active" id="actions-panel" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="actionsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 fs-13 text-muted uppercase">{{ __('messages.hook_name') }}</th>
                                        <th class="py-3 fs-13 text-muted uppercase">{{ __('messages.priority') }}</th>
                                        <th class="py-3 fs-13 text-muted uppercase">{{ __('messages.callback') }}</th>
                                        <th class="pe-4 py-3 fs-13 text-muted uppercase text-end">{{ __('messages.accepted_args') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($actions as $hook => $priorities)
                                        @foreach($priorities as $priority => $callbacks)
                                            @foreach($callbacks as $cb)
                                                @php
                                                    $callbackStr = is_string($cb['callback']) ? $cb['callback'] : (is_array($cb['callback']) ? (is_object($cb['callback'][0]) ? get_class($cb['callback'][0]) : $cb['callback'][0]) . '@' . $cb['callback'][1] : 'Closure');
                                                @endphp
                                                <tr class="hook-row">
                                                    <td class="ps-4 py-3">
                                                        <span class="fw-bold font-monospace text-primary fs-13">{{ $hook }}</span>
                                                    </td>
                                                    <td class="py-3">
                                                        <span class="badge bg-light text-dark border font-monospace">{{ $priority }}</span>
                                                    </td>
                                                    <td class="py-3">
                                                        <code class="text-dark bg-light px-2 py-1 rounded fs-12">{{ $callbackStr }}</code>
                                                    </td>
                                                    <td class="pe-4 py-3 text-end font-monospace fs-13 text-muted">
                                                        {{ $cb['accepted_args'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="feather-info fs-24 mb-2 d-block text-muted opacity-50"></i>
                                                No action hooks currently registered in execution flow.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Filters Panel -->
                    <div class="tab-pane fade" id="filters-panel" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="filtersTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 fs-13 text-muted uppercase">{{ __('messages.hook_name') }}</th>
                                        <th class="py-3 fs-13 text-muted uppercase">{{ __('messages.priority') }}</th>
                                        <th class="py-3 fs-13 text-muted uppercase">{{ __('messages.callback') }}</th>
                                        <th class="pe-4 py-3 fs-13 text-muted uppercase text-end">{{ __('messages.accepted_args') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($filters as $hook => $priorities)
                                        @foreach($priorities as $priority => $callbacks)
                                            @foreach($callbacks as $cb)
                                                @php
                                                    $callbackStr = is_string($cb['callback']) ? $cb['callback'] : (is_array($cb['callback']) ? (is_object($cb['callback'][0]) ? get_class($cb['callback'][0]) : $cb['callback'][0]) . '@' . $cb['callback'][1] : 'Closure');
                                                @endphp
                                                <tr class="hook-row">
                                                    <td class="ps-4 py-3">
                                                        <span class="fw-bold font-monospace text-info fs-13">{{ $hook }}</span>
                                                    </td>
                                                    <td class="py-3">
                                                        <span class="badge bg-light text-dark border font-monospace">{{ $priority }}</span>
                                                    </td>
                                                    <td class="py-3">
                                                        <code class="text-dark bg-light px-2 py-1 rounded fs-12">{{ $callbackStr }}</code>
                                                    </td>
                                                    <td class="pe-4 py-3 text-end font-monospace fs-13 text-muted">
                                                        {{ $cb['accepted_args'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="feather-info fs-24 mb-2 d-block text-muted opacity-50"></i>
                                                No filter hooks currently registered in execution flow.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('hookSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var query = this.value.toLowerCase().trim();
            var rows = document.querySelectorAll('.hook-row');
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(query) !== -1 ? '' : 'none';
            });
        });
    }
});
</script>
@endsection
