@extends('admin::layouts.admin')

@section('title', __('messages.mobile_settings_title') ?? 'Mobile App Settings')

@section('content')
<div class="admin-page">
    <!-- Hero Header -->
    <section class="admin-hero mb-4">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.settings.system') }}">{{ __('messages.system_settings') ?? 'System Settings' }}</a></li>
                <li>{{ __('messages.mobile_settings_title') ?? 'Mobile App Settings' }}</li>
            </ul>
            <div class="admin-hero__eyebrow"><i class="feather-smartphone me-1"></i> {{ __('MYADS MOBILE HUB') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.mobile_settings_title') ?? 'Mobile App Settings' }}</h1>
            <p class="admin-hero__copy">{{ __('messages.mobile_settings_subtitle') ?? 'Manage your mobile application API key, client authentication security, and app feature toggles.' }}</p>
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
            <i class="feather-alert-triangle me-2"></i> <strong>{{ __('messages.please_check_errors') ?? 'Please fix the errors below:' }}</strong>
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
                        <div class="text-muted fs-12 fw-semibold text-uppercase">{{ __('API Key Status') }}</div>
                        @if(!empty($mobileSettings['api_key']))
                            <div class="fs-15 fw-bold text-success"><i class="feather-shield-check me-1"></i>{{ __('Active & Secured') }}</div>
                        @else
                            <div class="fs-15 fw-bold text-danger"><i class="feather-shield-off me-1"></i>{{ __('Key Not Set') }}</div>
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
                        <div class="text-muted fs-12 fw-semibold text-uppercase">{{ __('Mobile API Status') }}</div>
                        @if(($mobileSettings['enabled'] ?? '1') == '1')
                            @if(($mobileSettings['maintenance_mode'] ?? '0') == '1')
                                <div class="fs-15 fw-bold text-warning"><i class="feather-alert-triangle me-1"></i>{{ __('Maintenance Mode') }}</div>
                            @else
                                <div class="fs-15 fw-bold text-success"><i class="feather-check-circle me-1"></i>{{ __('Online & Active') }}</div>
                            @endif
                        @else
                            <div class="fs-15 fw-bold text-muted"><i class="feather-slash me-1"></i>{{ __('Disabled') }}</div>
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
                        <div class="text-muted fs-12 fw-semibold text-uppercase">{{ __('Auth Protocol') }}</div>
                        <div class="fs-15 fw-bold text-dark">{{ __('Sanctum Bearer') }}</div>
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
                        <div class="text-muted fs-12 fw-semibold text-uppercase">{{ __('Required Header') }}</div>
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
                        {{ __('Mobile Security API Key') }}
                    </h5>
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fs-11">{{ __('Sanctum Protected') }}</span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted fs-13 mb-3">
                            {{ __('This secret API key authenticates requests coming from your official mobile application (Flutter / Android / iOS). Keep it secure and NEVER expose it in public client repositories.') }}
                        </p>

                        <div class="mb-4">
                            <label class="form-label fw-bold fs-12 text-uppercase text-muted">{{ __('Current API Key String') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="feather-lock text-muted"></i></span>
                                <input type="password" id="mobileApiKeyInput" class="form-control font-monospace border-start-0 border-end-0 bg-light fw-bold" value="{{ $mobileSettings['api_key'] ?? '' }}" readonly placeholder="{{ __('No API Key generated yet') }}">
                                <button type="button" class="btn btn-outline-secondary" id="toggleApiKeyBtn" onclick="toggleApiKeyVisibility()" title="{{ __('Toggle Visibility') }}">
                                    <i class="feather-eye" id="toggleApiKeyIcon"></i>
                                </button>
                                <button type="button" class="btn btn-primary" onclick="copyApiKey()" {{ empty($mobileSettings['api_key']) ? 'disabled' : '' }}>
                                    <i class="feather-copy me-1"></i> {{ __('Copy') }}
                                </button>
                            </div>
                            <div id="copyFeedback" class="form-text text-success d-none mt-1 fw-semibold">
                                <i class="feather-check me-1"></i> {{ __('API Key copied to clipboard!') }}
                            </div>
                        </div>

                        <div class="p-3 bg-soft-light border rounded-3 mb-4">
                            <div class="d-flex align-items-start gap-2">
                                <i class="feather-info text-primary fs-5 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 fs-13">{{ __('Flutter App Environment Setup') }}</h6>
                                    <p class="text-muted fs-12 mb-2">{{ __('Add the key to your Flutter app project `.env` configuration file:') }}</p>
                                    <code class="d-block p-2 bg-dark text-white rounded user-select-all fs-12 font-monospace">MOBILE_API_KEY={{ $mobileSettings['api_key'] ?? 'YOUR_GENERATED_API_KEY' }}</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="text-muted fs-12">
                            <i class="feather-shield me-1"></i> {{ __('32-byte cryptographically secure random token') }}
                        </div>
                        <button type="button" class="btn btn-{{ !empty($mobileSettings['api_key']) ? 'warning' : 'primary' }}" data-bs-toggle="modal" data-bs-target="#regenerateKeyModal">
                            <i class="feather-refresh-cw me-1"></i> {{ !empty($mobileSettings['api_key']) ? __('Regenerate API Key') : __('Generate New API Key') }}
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
                        {{ __('Mobile Feature Controls') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.mobile.update') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold fs-13">{{ __('Mobile API Status') }}</label>
                            <select name="enabled" class="form-select">
                                <option value="1" {{ ($mobileSettings['enabled'] ?? '1') == '1' ? 'selected' : '' }}>{{ __('Enabled (Accept Requests)') }}</option>
                                <option value="0" {{ ($mobileSettings['enabled'] ?? '1') == '0' ? 'selected' : '' }}>{{ __('Disabled (Reject Requests)') }}</option>
                            </select>
                            <div class="form-text fs-12">{{ __('When disabled, all Mobile API endpoints will return HTTP 403 status.') }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold fs-13">{{ __('Mobile Maintenance Mode') }}</label>
                            <select name="maintenance_mode" class="form-select">
                                <option value="0" {{ ($mobileSettings['maintenance_mode'] ?? '0') == '0' ? 'selected' : '' }}>{{ __('Normal Operation') }}</option>
                                <option value="1" {{ ($mobileSettings['maintenance_mode'] ?? '0') == '1' ? 'selected' : '' }}>{{ __('Maintenance Mode (Show Warning in App)') }}</option>
                            </select>
                            <div class="form-text fs-12">{{ __('In Maintenance mode, the app will notify mobile users of ongoing server maintenance.') }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold fs-13">{{ __('Minimum Mobile App Version') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="feather-tag"></i></span>
                                <input type="text" name="min_version" class="form-control" value="{{ $mobileSettings['min_version'] ?? '1.0.0' }}" placeholder="1.0.0">
                            </div>
                            <div class="form-text fs-12">{{ __('Clients with older version numbers can be prompted to upgrade from app store.') }}</div>
                        </div>

                        <div class="pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="feather-save me-1"></i> {{ __('Save Controls') }}
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
                        {{ __('Developer Quick API Test') }}
                    </h5>
                    <span class="badge bg-soft-info text-info">{{ __('RESTful Endpoint') }}</span>
                </div>
                <div class="card-body">
                    <p class="text-muted fs-13 mb-3">
                        {{ __('Test your Mobile API connection directly from command line or Postman. Include the header') }} <code>X-API-KEY</code> {{ __('in all requests:') }}
                    </p>

                    <div class="position-relative">
                        <pre class="bg-dark text-white p-3 rounded-3 font-monospace mb-0 fs-12 user-select-all" id="curlSnippet">curl -X GET "{{ url('/api/portal/feed') }}" \
  -H "Accept: application/json" \
  -H "X-API-KEY: {{ $mobileSettings['api_key'] ?? 'YOUR_API_KEY' }}"</pre>
                        <button type="button" class="btn btn-sm btn-light position-absolute top-0 end-0 m-2" onclick="copyCurlSnippet()">
                            <i class="feather-copy me-1"></i> {{ __('Copy cURL') }}
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
                    <i class="feather-alert-triangle me-2"></i> {{ __('Regenerate Mobile API Key?') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-0">
                <p class="text-muted fs-14 mb-3">
                    {{ __('Are you sure you want to generate a new Mobile API Key?') }}
                </p>
                <div class="alert alert-warning py-2 px-3 fs-13">
                    <i class="feather-info me-1"></i> {{ __('Warning: Any deployed Flutter mobile builds using the previous key will instantly lose API access until updated with the new key!') }}
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-3">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <form action="{{ route('admin.settings.api_key.generate') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="feather-refresh-cw me-1"></i> {{ __('Yes, Regenerate Key') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleApiKeyVisibility() {
        const input = document.getElementById('mobileApiKeyInput');
        const icon = document.getElementById('toggleApiKeyIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('feather-eye');
            icon.classList.add('feather-eye-off');
        } else {
            input.type = 'password';
            icon.classList.remove('feather-eye-off');
            icon.classList.add('feather-eye');
        }
    }

    function copyApiKey() {
        const key = "{{ $mobileSettings['api_key'] ?? '' }}";
        if (!key) return;
        navigator.clipboard.writeText(key).then(() => {
            const feedback = document.getElementById('copyFeedback');
            feedback.classList.remove('d-none');
            setTimeout(() => {
                feedback.classList.add('d-none');
            }, 3000);
        });
    }

    function copyCurlSnippet() {
        const snippet = document.getElementById('curlSnippet').innerText;
        navigator.clipboard.writeText(snippet).then(() => {
            alert("cURL snippet copied to clipboard!");
        });
    }
</script>
@endpush
@endsection
