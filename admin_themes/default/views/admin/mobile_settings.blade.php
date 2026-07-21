@extends('admin::layouts.admin')

@section('title', __('messages.mobile_settings_title'))

@section('content')
<div class="admin-page">
    <!-- Hero Header -->
    <section class="admin-hero mb-4">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.settings.system') }}">{{ __('messages.system_settings') }}</a></li>
                <li>{{ __('messages.mobile_settings_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow"><i class="feather-smartphone me-1"></i> {{ __('messages.myads_mobile_hub') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.mobile_settings_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.mobile_settings_subtitle') }}</p>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="feather-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="feather-alert-triangle me-2"></i> <strong>{{ __('messages.please_check_errors') }}</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Status Overview Widgets -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-3 fs-3">
                        <i class="feather-key"></i>
                    </div>
                    <div>
                        <div class="text-muted fs-12 fw-semibold text-uppercase">{{ __('messages.mobile_api_key_status') }}</div>
                        @if(!empty($mobileSettings['api_key']))
                            <div class="fs-15 fw-bold text-success"><i class="feather-shield-check me-1"></i>{{ __('messages.mobile_active_secured') }}</div>
                        @else
                            <div class="fs-15 fw-bold text-danger"><i class="feather-shield-off me-1"></i>{{ __('messages.mobile_key_not_set') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-lg bg-soft-info text-info rounded-3 fs-3">
                        <i class="feather-cpu"></i>
                    </div>
                    <div>
                        <div class="text-muted fs-12 fw-semibold text-uppercase">{{ __('messages.mobile_api_status') }}</div>
                        @if(($mobileSettings['enabled'] ?? '1') == '1')
                            @if(($mobileSettings['maintenance_mode'] ?? '0') == '1')
                                <div class="fs-15 fw-bold text-warning"><i class="feather-alert-triangle me-1"></i>{{ __('messages.mobile_maintenance_operation') }}</div>
                            @else
                                <div class="fs-15 fw-bold text-success"><i class="feather-check-circle me-1"></i>{{ __('messages.mobile_online_active') }}</div>
                            @endif
                        @else
                            <div class="fs-15 fw-bold text-muted"><i class="feather-slash me-1"></i>{{ __('messages.mobile_disabled_reject') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-lg bg-soft-warning text-warning rounded-3 fs-3">
                        <i class="feather-lock"></i>
                    </div>
                    <div>
                        <div class="text-muted fs-12 fw-semibold text-uppercase">{{ __('messages.mobile_auth_protocol') }}</div>
                        <div class="fs-15 fw-bold text-dark">{{ __('messages.mobile_sanctum_bearer') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-lg bg-soft-success text-success rounded-3 fs-3">
                        <i class="feather-code"></i>
                    </div>
                    <div>
                        <div class="text-muted fs-12 fw-semibold text-uppercase">{{ __('messages.mobile_required_header') }}</div>
                        <div class="fs-15 fw-bold text-dark"><code>X-API-KEY</code></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Card 1: Mobile API Key Management -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="feather-key me-2 text-primary"></i>
                        {{ __('messages.mobile_security_api_key') }}
                    </h5>
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fs-11">{{ __('messages.mobile_sanctum_protected') }}</span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted fs-13 mb-3">
                            {{ __('messages.mobile_key_desc') }}
                        </p>

                        <div class="mb-4">
                            <label class="form-label fw-bold fs-12 text-uppercase text-muted">{{ __('messages.mobile_current_api_key') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="feather-lock text-muted"></i></span>
                                <input type="password" id="mobileApiKeyInput" class="form-control font-monospace border-start-0 border-end-0 bg-light fw-bold" value="{{ $mobileSettings['api_key'] ?? '' }}" readonly placeholder="{{ __('messages.mobile_no_key_generated') }}">
                                <button type="button" class="btn btn-outline-secondary" id="toggleApiKeyBtn" onclick="toggleApiKeyVisibility()" title="{{ __('messages.mobile_toggle_visibility') }}">
                                    <i class="feather-eye" id="toggleApiKeyIcon"></i>
                                </button>
                                <button type="button" class="btn btn-primary" onclick="copyApiKey()" {{ empty($mobileSettings['api_key']) ? 'disabled' : '' }}>
                                    <i class="feather-copy me-1"></i> {{ __('messages.Clik') ?? 'Copy' }}
                                </button>
                            </div>
                            <div id="copyFeedback" class="form-text text-success d-none mt-1 fw-semibold">
                                <i class="feather-check me-1"></i> {{ __('messages.mobile_key_copied') }}
                            </div>
                        </div>

                        <div class="p-3 bg-soft-light border rounded-3 mb-4">
                            <div class="d-flex align-items-start gap-2">
                                <i class="feather-info text-primary fs-5 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 fs-13">{{ __('messages.mobile_flutter_env_setup') }}</h6>
                                    <p class="text-muted fs-12 mb-2">{{ __('messages.mobile_flutter_env_hint') }}</p>
                                    <code class="d-block p-2 bg-dark text-white rounded user-select-all fs-12 font-monospace">MOBILE_API_KEY=<span id="flutterEnvKeyDisplay">{{ !empty($mobileSettings['api_key']) ? '••••••••••••••••••••••••••••••••' : 'YOUR_GENERATED_API_KEY' }}</span></code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="text-muted fs-12">
                            <i class="feather-shield me-1"></i> {{ __('messages.mobile_token_spec') }}
                        </div>
                        <button type="button" class="btn btn-{{ !empty($mobileSettings['api_key']) ? 'warning' : 'primary' }}" data-bs-toggle="modal" data-bs-target="#regenerateKeyModal">
                            <i class="feather-refresh-cw me-1"></i> {{ !empty($mobileSettings['api_key']) ? __('messages.mobile_regenerate_api_key') : __('messages.mobile_generate_api_key') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Mobile App Configuration Toggles -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="feather-sliders me-2 text-primary"></i>
                        {{ __('messages.mobile_feature_controls') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.mobile.update') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold fs-13">{{ __('messages.mobile_api_status') }}</label>
                            <select name="enabled" class="form-select">
                                <option value="1" {{ ($mobileSettings['enabled'] ?? '1') == '1' ? 'selected' : '' }}>{{ __('messages.mobile_enabled_accept') }}</option>
                                <option value="0" {{ ($mobileSettings['enabled'] ?? '1') == '0' ? 'selected' : '' }}>{{ __('messages.mobile_disabled_reject') }}</option>
                            </select>
                            <div class="form-text fs-12">{{ __('messages.mobile_status_help') }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold fs-13">{{ __('messages.mobile_maintenance_mode') }}</label>
                            <select name="maintenance_mode" class="form-select">
                                <option value="0" {{ ($mobileSettings['maintenance_mode'] ?? '0') == '0' ? 'selected' : '' }}>{{ __('messages.mobile_normal_operation') }}</option>
                                <option value="1" {{ ($mobileSettings['maintenance_mode'] ?? '0') == '1' ? 'selected' : '' }}>{{ __('messages.mobile_maintenance_operation') }}</option>
                            </select>
                            <div class="form-text fs-12">{{ __('messages.mobile_maintenance_help') }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold fs-13">{{ __('messages.mobile_min_version') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="feather-tag"></i></span>
                                <input type="text" name="min_version" class="form-control" value="{{ $mobileSettings['min_version'] ?? '1.0.0' }}" placeholder="1.0.0">
                            </div>
                            <div class="form-text fs-12">{{ __('messages.mobile_min_version_help') }}</div>
                        </div>

                        <div class="pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="feather-save me-1"></i> {{ __('messages.save') ?? 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card 3: Developer Integration & Quick Test -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="feather-terminal me-2 text-primary"></i>
                        {{ __('messages.mobile_dev_quick_test') }}
                    </h5>
                    <span class="badge bg-soft-info text-info">{{ __('messages.mobile_rest_endpoint') }}</span>
                </div>
                <div class="card-body">
                    <p class="text-muted fs-13 mb-3">
                        {{ __('messages.mobile_dev_test_desc') }}
                    </p>

                    <div class="position-relative">
                        <pre class="bg-dark text-white p-3 rounded-3 font-monospace mb-0 fs-12 user-select-all">curl -X GET "{{ url('/api/portal/feed') }}" \
  -H "Accept: application/json" \
  -H "X-API-KEY: <span id="curlKeyDisplay">{{ !empty($mobileSettings['api_key']) ? '••••••••••••••••••••••••••••••••' : 'YOUR_API_KEY' }}</span>"</pre>
                        <button type="button" class="btn btn-sm btn-light position-absolute top-0 end-0 m-2" onclick="copyCurlSnippet()">
                            <i class="feather-copy me-1"></i> {{ __('messages.mobile_copy_curl') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Regenerate Key Confirmation -->
<div class="modal fade" id="regenerateKeyModal" tabindex="-1" aria-labelledby="regenerateKeyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold text-danger" id="regenerateKeyModalLabel">
                    <i class="feather-alert-triangle me-2"></i> {{ __('messages.mobile_regenerate_key_confirm_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-0">
                <p class="text-muted fs-14 mb-3">
                    {{ __('messages.mobile_regenerate_key_confirm_desc') }}
                </p>
                <div class="alert alert-warning py-2 px-3 fs-13">
                    <i class="feather-info me-1"></i> {{ __('messages.mobile_regenerate_key_warning') }}
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-3">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                <form action="{{ route('admin.settings.api_key.generate') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="feather-refresh-cw me-1"></i> {{ __('messages.mobile_yes_regenerate_key') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const rawApiKey = "{{ $mobileSettings['api_key'] ?? '' }}";
    const maskedKey = rawApiKey ? "••••••••••••••••••••••••••••••••" : "YOUR_GENERATED_API_KEY";
    const maskedCurlKey = rawApiKey ? "••••••••••••••••••••••••••••••••" : "YOUR_API_KEY";

    function toggleApiKeyVisibility() {
        const input = document.getElementById('mobileApiKeyInput');
        const icon = document.getElementById('toggleApiKeyIcon');
        const envKey = document.getElementById('flutterEnvKeyDisplay');
        const curlKey = document.getElementById('curlKeyDisplay');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('feather-eye');
            icon.classList.add('feather-eye-off');
            if (envKey) envKey.innerText = rawApiKey || "YOUR_GENERATED_API_KEY";
            if (curlKey) curlKey.innerText = rawApiKey || "YOUR_API_KEY";
        } else {
            input.type = 'password';
            icon.classList.remove('feather-eye-off');
            icon.classList.add('feather-eye');
            if (envKey) envKey.innerText = maskedKey;
            if (curlKey) curlKey.innerText = maskedCurlKey;
        }
    }

    function copyApiKey() {
        if (!rawApiKey) return;
        navigator.clipboard.writeText(rawApiKey).then(() => {
            const feedback = document.getElementById('copyFeedback');
            feedback.classList.remove('d-none');
            setTimeout(() => {
                feedback.classList.add('d-none');
            }, 3000);
        });
    }

    function copyCurlSnippet() {
        const baseUrl = "{{ url('/api/portal/feed') }}";
        const key = rawApiKey || "YOUR_API_KEY";
        const snippet = `curl -X GET "${baseUrl}" \\\n  -H "Accept: application/json" \\\n  -H "X-API-KEY: ${key}"`;
        navigator.clipboard.writeText(snippet).then(() => {
            alert("{{ __('messages.mobile_curl_copied') }}");
        });
    }
</script>
@endpush
@endsection
