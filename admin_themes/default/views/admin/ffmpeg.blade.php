@extends('admin::layouts.admin')

@section('title', __('messages.ffmpeg_config') ?? 'FFMPEG Configuration')

@section('content')
<div class="admin-page ffmpeg-admin-page">
    <!-- Hero Header -->
    <section class="admin-hero mb-4">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.settings.upload') }}">{{ __('messages.file_upload_config') ?? 'Upload & Storage' }}</a></li>
                <li>{{ __('messages.ffmpeg_config') ?? 'FFMPEG Configuration' }}</li>
            </ul>
            <div class="admin-hero__eyebrow d-flex align-items-center gap-2">
                <span class="badge bg-danger-subtle text-danger fw-semibold px-2 py-1">
                    <i class="fa-solid fa-film me-1"></i>{{ __('messages.admin_module_settings') ?? 'Video Engine' }}
                </span>
                @if(($options['ffmpeg_system']->o_valuer ?? '0') == '1')
                    <span class="badge bg-success-subtle text-success fw-semibold px-2 py-1">
                        <i class="feather-check-circle me-1"></i>{{ __('messages.ffmpeg_active') ?? 'Active' }}
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary fw-semibold px-2 py-1">
                        <i class="feather-pause-circle me-1"></i>{{ __('messages.ffmpeg_disabled') ?? 'Disabled' }}
                    </span>
                @endif
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="fa-solid fa-video text-danger"></i>
                {{ __('messages.ffmpeg_config') ?? 'FFMPEG Configuration' }}
            </h1>
            <p class="admin-hero__copy">{{ __('messages.ffmpeg_system_help') ?? 'This system compresses, converts, and optimizes uploaded videos to mp4 format and generates poster thumbnails automatically.' }}</p>
        </div>
    </section>

    <!-- Superdesign Storage Sub-Nav -->
    @include('admin::admin.partials.storage_nav')

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="feather-check-circle fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="feather-alert-triangle fs-5"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Environment Diagnostics & KPI Stat Strip -->
    <div class="row g-3 mb-4">
        <!-- KPI 1: System State -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm ffmpeg-stat-card">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="ffmpeg-icon-chip {{ ($options['ffmpeg_system']->o_valuer ?? '0') == '1' ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                        <i class="fa-solid {{ ($options['ffmpeg_system']->o_valuer ?? '0') == '1' ? 'fa-circle-play' : 'fa-circle-pause' }} fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">{{ __('messages.ffmpeg_system_status') ?? 'FFmpeg Status' }}</div>
                        <div class="fw-bold fs-6 mt-1">
                            @if(($options['ffmpeg_system']->o_valuer ?? '0') == '1')
                                <span class="text-success"><i class="feather-check me-1"></i>{{ __('messages.ffmpeg_active') ?? 'Active' }}</span>
                            @else
                                <span class="text-muted"><i class="feather-x me-1"></i>{{ __('messages.ffmpeg_disabled') ?? 'Disabled' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 2: PHP exec Status -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm ffmpeg-stat-card">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="ffmpeg-icon-chip {{ ($serverInfo['exec_allowed'] || $serverInfo['shell_exec_allowed']) ? 'bg-soft-primary text-primary' : 'bg-soft-danger text-danger' }}">
                        <i class="fa-solid fa-terminal fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">{{ __('messages.ffmpeg_php_exec') ?? 'PHP Shell Functions' }}</div>
                        <div class="fw-bold fs-6 mt-1">
                            @if($serverInfo['exec_allowed'] || $serverInfo['shell_exec_allowed'])
                                <span class="text-success"><i class="feather-check-circle me-1"></i>Enabled</span>
                            @else
                                <span class="text-danger" title="exec() is in disable_functions"><i class="feather-alert-triangle me-1"></i>Disabled</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 3: OS & Binary Existence -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm ffmpeg-stat-card">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="ffmpeg-icon-chip {{ $serverInfo['path_exists'] ? 'bg-soft-info text-info' : 'bg-soft-warning text-warning' }}">
                        <i class="fa-solid fa-server fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">{{ __('messages.ffmpeg_server_env') ?? 'Server Platform' }}</div>
                        <div class="fw-bold fs-6 mt-1 text-truncate" style="max-width: 170px;" title="{{ $serverInfo['os'] }}">
                            {{ $serverInfo['os_family'] }}
                            @if($serverInfo['path_exists'])
                                <span class="badge bg-success-subtle text-success ms-1 small">Binary Found</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 4: PHP Upload & Timeout -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm ffmpeg-stat-card">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="ffmpeg-icon-chip bg-soft-purple text-purple">
                        <i class="fa-solid fa-gauge-high fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">{{ __('messages.ffmpeg_max_upload_time') ?? 'Max Limits' }}</div>
                        <div class="fw-bold fs-6 mt-1">
                            <span>{{ $serverInfo['upload_max_filesize'] }}</span>
                            <span class="text-muted mx-1">/</span>
                            <span class="text-muted small">{{ $serverInfo['max_execution_time'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Left / Main Column: Settings Form & Interactive Tester -->
        <div class="col-12 col-lg-7">
            <!-- 1. Settings Form Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 ffmpeg-main-card">
                <div class="card-header bg-transparent border-bottom p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-soft-danger text-danger rounded-3 p-2">
                                <i class="fa-solid fa-sliders fs-5"></i>
                            </div>
                            <div>
                                <h5 class="card-title fw-bold mb-0">{{ __('messages.ffmpeg_core_settings') ?? 'Core Media Engine Settings' }}</h5>
                                <small class="text-muted">{{ __('messages.ffmpeg_core_settings_desc') ?? 'Configure binary executable path, encoding presets and CPU threads' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.settings.ffmpeg.update') }}" method="POST" id="ffmpeg-settings-form">
                        @csrf

                        <!-- Master Switch -->
                        <div class="ffmpeg-switch-banner p-3 rounded-3 mb-4">
                            <div class="form-check form-switch px-0 d-flex align-items-center justify-content-between gap-3">
                                <div>
                                    <label class="form-check-label fw-bold mb-1 cursor-pointer fs-6" for="ffmpeg_system">
                                        {{ __('messages.ffmpeg_system') ?? 'Enable FFMPEG Video Processing' }}
                                    </label>
                                    <div class="small text-muted">{{ __('messages.ffmpeg_system_help') }}</div>
                                </div>
                                <input class="form-check-input ms-0 mt-0 flex-shrink-0" type="checkbox" role="switch" id="ffmpeg_system" name="ffmpeg_system" value="1" {{ ($options['ffmpeg_system']->o_valuer ?? '0') == '1' ? 'checked' : '' }} style="width: 48px; height: 26px;">
                            </div>
                        </div>

                        <!-- Binary Path Input -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-bold mb-0" for="ffmpeg_binary_path">
                                    <i class="fa-solid fa-folder-tree text-primary me-1"></i>
                                    {{ __('messages.ffmpeg_binary_path') ?? 'FFmpeg Binary Path' }}
                                </label>
                                @if($serverInfo['path_exists'])
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="feather-check-circle me-1"></i>{{ __('messages.ffmpeg_executable_badge') ?? 'Binary Exists' }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="feather-help-circle me-1"></i>{{ __('messages.ffmpeg_not_found_badge') ?? 'Path not verified' }}
                                    </span>
                                @endif
                            </div>

                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0">
                                    <i class="fa-solid fa-terminal text-muted"></i>
                                </span>
                                <input type="text" name="ffmpeg_binary_path" id="ffmpeg_binary_path" class="form-control font-monospace border-start-0 ps-0" value="{{ $options['ffmpeg_binary_path']->o_valuer ?? $serverInfo['configured_path'] }}" placeholder="/usr/bin/ffmpeg" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="testCurrentPathInInput()" title="Test this path">
                                    <i class="fa-solid fa-bolt text-warning me-1"></i>Test
                                </button>
                            </div>

                            <!-- Quick Preset Suggestions -->
                            <div class="mt-2 d-flex flex-wrap align-items-center gap-2">
                                <span class="small text-muted fw-semibold">{{ __('messages.ffmpeg_quick_paths') ?? 'Presets:' }}</span>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2 font-monospace" onclick="setBinaryPath('/usr/bin/ffmpeg')">/usr/bin/ffmpeg</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2 font-monospace" onclick="setBinaryPath('/usr/local/bin/ffmpeg')">/usr/local/bin/ffmpeg</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2 font-monospace" onclick="setBinaryPath('C:\\ffmpeg\\bin\\ffmpeg.exe')">C:\ffmpeg\bin\ffmpeg.exe</button>
                                @if(!empty($serverInfo['detected_path']))
                                    <button type="button" class="btn btn-xs btn-outline-success rounded-pill py-0 px-2 font-monospace" onclick="setBinaryPath('{{ addslashes($serverInfo['detected_path']) }}')">
                                        <i class="feather-check me-1"></i>Detected: {{ $serverInfo['detected_path'] }}
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Advanced Parameters Row -->
                        <div class="row g-3 mb-4">
                            <!-- Speed Preset -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold" for="ffmpeg_speed_preset">
                                    <i class="fa-solid fa-gauge text-warning me-1"></i>
                                    {{ __('messages.ffmpeg_speed_preset') ?? 'Conversion Speed (Preset)' }}
                                </label>
                                <select class="form-select" id="ffmpeg_speed_preset" name="ffmpeg_speed_preset">
                                    <option value="ultrafast" {{ ($options['ffmpeg_speed_preset']->o_valuer ?? 'ultrafast') === 'ultrafast' ? 'selected' : '' }}>
                                        {{ __('messages.ffmpeg_preset_ultrafast') ?? 'Ultrafast (Lowest CPU - Recommended)' }}
                                    </option>
                                    <option value="veryfast" {{ ($options['ffmpeg_speed_preset']->o_valuer ?? '') === 'veryfast' ? 'selected' : '' }}>
                                        Very Fast
                                    </option>
                                    <option value="fast" {{ ($options['ffmpeg_speed_preset']->o_valuer ?? '') === 'fast' ? 'selected' : '' }}>
                                        {{ __('messages.ffmpeg_preset_fast') ?? 'Fast (Balanced)' }}
                                    </option>
                                    <option value="medium" {{ ($options['ffmpeg_speed_preset']->o_valuer ?? '') === 'medium' ? 'selected' : '' }}>
                                        {{ __('messages.ffmpeg_preset_medium') ?? 'Medium (Best Compression)' }}
                                    </option>
                                </select>
                                <div class="form-text small text-muted">{{ __('messages.ffmpeg_speed_preset_help') ?? 'Ultrafast saves server CPU power during video uploads.' }}</div>
                            </div>

                            <!-- CPU Threads -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold" for="ffmpeg_threads">
                                    <i class="fa-solid fa-microchip text-info me-1"></i>
                                    {{ __('messages.ffmpeg_threads') ?? 'CPU Threads Limit' }}
                                </label>
                                <select class="form-select" id="ffmpeg_threads" name="ffmpeg_threads">
                                    <option value="auto" {{ ($options['ffmpeg_threads']->o_valuer ?? 'auto') === 'auto' ? 'selected' : '' }}>
                                        {{ __('messages.ffmpeg_threads_auto') ?? 'Auto (Server Managed)' }}
                                    </option>
                                    <option value="1" {{ ($options['ffmpeg_threads']->o_valuer ?? '') === '1' ? 'selected' : '' }}>1 Thread (Safe for Shared Hostings)</option>
                                    <option value="2" {{ ($options['ffmpeg_threads']->o_valuer ?? '') === '2' ? 'selected' : '' }}>2 Threads (Standard VPS)</option>
                                    <option value="4" {{ ($options['ffmpeg_threads']->o_valuer ?? '') === '4' ? 'selected' : '' }}>4 Threads (High Performance)</option>
                                    <option value="8" {{ ($options['ffmpeg_threads']->o_valuer ?? '') === '8' ? 'selected' : '' }}>8 Threads (Dedicated)</option>
                                </select>
                                <div class="form-text small text-muted">{{ __('messages.ffmpeg_threads_help') ?? 'Limits concurrent cores used to prevent server spikes.' }}</div>
                            </div>
                        </div>

                        <!-- Auto Thumbnail Switch -->
                        <div class="mb-4 p-3 bg-body-tertiary rounded-3">
                            <div class="form-check form-switch px-0 d-flex align-items-center justify-content-between gap-3">
                                <div>
                                    <label class="form-check-label fw-semibold mb-0 cursor-pointer" for="ffmpeg_auto_thumbnail">
                                        <i class="fa-solid fa-image text-purple me-1"></i>
                                        {{ __('messages.ffmpeg_auto_thumb') ?? 'Auto-Generate Video Poster Thumbnails' }}
                                    </label>
                                    <div class="small text-muted">{{ __('messages.ffmpeg_auto_thumb_help') ?? 'Extract high-resolution preview images from uploaded videos automatically.' }}</div>
                                </div>
                                <input class="form-check-input ms-0 mt-0 flex-shrink-0" type="checkbox" role="switch" id="ffmpeg_auto_thumbnail" name="ffmpeg_auto_thumbnail" value="1" {{ ($options['ffmpeg_auto_thumbnail']->o_valuer ?? '1') == '1' ? 'checked' : '' }} style="width: 44px; height: 22px;">
                            </div>
                        </div>

                        <!-- Save Changes Button -->
                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                                <i class="feather-save me-1"></i>{{ __('messages.save_changes') ?? 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. Interactive Diagnostic Tester & Live Console -->
            <div class="card border-0 shadow-sm rounded-4 ffmpeg-main-card">
                <div class="card-header bg-transparent border-bottom p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-soft-success text-success rounded-3 p-2">
                                <i class="fa-solid fa-terminal fs-5"></i>
                            </div>
                            <div>
                                <h5 class="card-title fw-bold mb-0">{{ __('messages.ffmpeg_interactive_tester') ?? 'Interactive Diagnostic Tester' }}</h5>
                                <small class="text-muted">{{ __('messages.ffmpeg_interactive_tester_desc') ?? 'Test FFmpeg binary execution and check codec capabilities in real-time' }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-success px-3 fw-semibold shadow-xs" id="btn-run-debug" onclick="debugFfmpeg()">
                                <i class="fa-solid fa-play me-1" id="debug-btn-icon"></i>
                                <span id="debug-btn-text">{{ __('messages.ffmpeg_run_test') ?? 'Run Diagnostic Test' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Dynamic Diagnostic Result Banner -->
                    <div id="ffmpeg-diagnostic-status" class="d-none mb-3"></div>

                    <!-- Terminal Console Header -->
                    <div class="d-flex align-items-center justify-content-between bg-dark text-secondary px-3 py-2 rounded-top-3 font-monospace small border-bottom border-secondary border-opacity-25">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded-circle bg-danger" style="width: 10px; height: 10px;"></span>
                            <span class="d-inline-block rounded-circle bg-warning" style="width: 10px; height: 10px;"></span>
                            <span class="d-inline-block rounded-circle bg-success" style="width: 10px; height: 10px;"></span>
                            <span class="text-light ms-2 fw-semibold">ffmpeg-cli@server:~</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-link btn-sm text-secondary p-0 text-decoration-none" onclick="copyDebugLog()" title="{{ __('messages.ffmpeg_copy_log') ?? 'Copy Log' }}">
                                <i class="feather-copy me-1"></i>{{ __('messages.ffmpeg_copy_log') ?? 'Copy' }}
                            </button>
                            <span class="text-muted">|</span>
                            <button type="button" class="btn btn-link btn-sm text-secondary p-0 text-decoration-none" onclick="clearDebugLog()" title="{{ __('messages.ffmpeg_clear_log') ?? 'Clear' }}">
                                <i class="feather-trash-2 me-1"></i>{{ __('messages.ffmpeg_clear_log') ?? 'Clear' }}
                            </button>
                        </div>
                    </div>

                    <!-- Terminal Output Box -->
                    <div id="ffmpeg-log" class="bg-dark text-light p-3 rounded-bottom-3 font-monospace small" style="min-height: 140px; max-height: 280px; overflow-y: auto; white-space: pre-wrap; line-height: 1.5; font-size: 0.82rem;">{{ __('messages.ffmpeg_debug_click') ?? 'Click "Run Diagnostic Test" above to initiate a live server test.' }}</div>
                </div>
            </div>
        </div>

        <!-- Right Column: Hosting Guides & Best Practices -->
        <div class="col-12 col-lg-5">
            <!-- 3. Comprehensive Hosting Guides (Tabs) -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 ffmpeg-guide-card">
                <div class="card-header bg-transparent border-bottom p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-soft-primary text-primary rounded-3 p-2">
                            <i class="fa-solid fa-book-open fs-5"></i>
                        </div>
                        <div>
                            <h5 class="card-title fw-bold mb-0">{{ __('messages.ffmpeg_hosting_guides') ?? 'Hosting Setup & Usage Guides' }}</h5>
                            <small class="text-muted">Step-by-step instructions for all server environments</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-pills ffmpeg-guide-pills mb-3 gap-1" id="hostingGuideTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active btn-sm" id="guide-vps-tab" data-bs-toggle="pill" data-bs-target="#guide-vps" type="button" role="tab">
                                <i class="fa-solid fa-server me-1"></i>{{ __('messages.ffmpeg_tab_vps') ?? 'VPS / Cloud' }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm" id="guide-shared-tab" data-bs-toggle="pill" data-bs-target="#guide-shared" type="button" role="tab">
                                <i class="fa-solid fa-cloud me-1"></i>{{ __('messages.ffmpeg_tab_shared') ?? 'cPanel' }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm" id="guide-free-tab" data-bs-toggle="pill" data-bs-target="#guide-free" type="button" role="tab">
                                <i class="fa-solid fa-hand-holding-heart me-1"></i>{{ __('messages.ffmpeg_tab_free') ?? 'Free Host' }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm" id="guide-local-tab" data-bs-toggle="pill" data-bs-target="#guide-local" type="button" role="tab">
                                <i class="fa-brands fa-windows me-1"></i>{{ __('messages.ffmpeg_tab_local') ?? 'Localhost' }}
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content" id="hostingGuideTabsContent">
                        <!-- 1. VPS / Dedicated -->
                        <div class="tab-pane fade show active" id="guide-vps" role="tabpanel">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <h6 class="fw-bold mb-2 text-primary">
                                    <i class="feather-check-circle me-1"></i>Recommended for High Performance
                                </h6>
                                <p class="small text-muted mb-2">
                                    On Ubuntu / Debian servers, install FFmpeg with a single command via SSH terminal:
                                </p>
                                <div class="position-relative mb-2">
                                    <pre class="bg-dark text-light p-2 rounded small font-monospace mb-0" style="font-size: 0.8rem;"><code>sudo apt update && sudo apt install ffmpeg -y</code></pre>
                                    <button type="button" class="btn btn-xs btn-outline-light position-absolute top-50 end-0 translate-middle-y me-2" onclick="copyCode('sudo apt update && sudo apt install ffmpeg -y')">Copy</button>
                                </div>
                                <div class="position-relative mb-3">
                                    <pre class="bg-dark text-light p-2 rounded small font-monospace mb-0" style="font-size: 0.8rem;"><code>ffmpeg -version</code></pre>
                                    <button type="button" class="btn btn-xs btn-outline-light position-absolute top-50 end-0 translate-middle-y me-2" onclick="copyCode('ffmpeg -version')">Copy</button>
                                </div>
                                <div class="small text-muted">
                                    <i class="feather-info me-1 text-info"></i>Default Binary Path: <code>/usr/bin/ffmpeg</code>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Shared Hosting (cPanel) -->
                        <div class="tab-pane fade" id="guide-shared" role="tabpanel">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <h6 class="fw-bold mb-2 text-warning">
                                    <i class="feather-help-circle me-1"></i>cPanel & Shared Servers
                                </h6>
                                <ol class="small text-muted ps-3 mb-3" style="line-height: 1.6;">
                                    <li>Open <strong>cPanel &gt; Terminal</strong> and run <code>which ffmpeg</code> to find if pre-installed.</li>
                                    <li>If not installed, download a portable <strong>Static Binary</strong> to your home directory:</li>
                                </ol>
                                <div class="position-relative mb-2">
                                    <pre class="bg-dark text-light p-2 rounded small font-monospace mb-0" style="font-size: 0.75rem;"><code>mkdir -p ~/bin && cd ~/bin
wget https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz
tar -xf ffmpeg-release-amd64-static.tar.xz --strip-components 1
chmod +x ffmpeg</code></pre>
                                    <button type="button" class="btn btn-xs btn-outline-light position-absolute top-0 end-0 m-2" onclick="copyCode('mkdir -p ~/bin && cd ~/bin && wget https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz && tar -xf ffmpeg-release-amd64-static.tar.xz --strip-components 1 && chmod +x ffmpeg')">Copy</button>
                                </div>
                                <div class="small text-muted mt-2">
                                    <i class="feather-check text-success me-1"></i>Path will be: <code>/home/YOUR_USER/bin/ffmpeg</code>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Free Hosting -->
                        <div class="tab-pane fade" id="guide-free" role="tabpanel">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <h6 class="fw-bold mb-2 text-danger">
                                    <i class="feather-alert-triangle me-1"></i>Free Hosting Advice
                                </h6>
                                <p class="small text-muted mb-2">
                                    Free hosting services (InfinityFree, 000webhost) disable <code>exec()</code> and forbid background video encoding to prevent CPU abuse.
                                </p>
                                <ul class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                    <li><strong>Keep FFmpeg disabled</strong> in settings above.</li>
                                    <li>Limit video upload size to <strong>10MB</strong> in File Upload settings.</li>
                                    <li>Encourage users to share videos via <strong>YouTube, Vimeo or TikTok</strong> links which embed seamlessly.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- 4. Localhost (XAMPP / Windows) -->
                        <div class="tab-pane fade" id="guide-local" role="tabpanel">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <h6 class="fw-bold mb-2 text-info">
                                    <i class="feather-monitor me-1"></i>Windows / XAMPP / Laragon
                                </h6>
                                <ol class="small text-muted ps-3 mb-2" style="line-height: 1.6;">
                                    <li>Download FFmpeg build for Windows from <strong>gyan.dev/ffmpeg/builds</strong> (ffmpeg-release-essentials.zip).</li>
                                    <li>Extract the folder and copy <code>ffmpeg.exe</code> to <code>C:\ffmpeg\bin\ffmpeg.exe</code> or <code>C:\xampp\ffmpeg\bin\ffmpeg.exe</code>.</li>
                                    <li>Set the binary path in the input box to <code>C:\ffmpeg\bin\ffmpeg.exe</code> and click <strong>Test</strong>.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Best Practices & Server Checklist Card -->
            <div class="card border-0 shadow-sm rounded-4 ffmpeg-guide-card">
                <div class="card-header bg-transparent border-bottom p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-soft-warning text-warning rounded-3 p-2">
                            <i class="fa-solid fa-lightbulb fs-5"></i>
                        </div>
                        <div>
                            <h5 class="card-title fw-bold mb-0">{{ __('messages.ffmpeg_best_practices') ?? 'Best Practices & Optimization' }}</h5>
                            <small class="text-muted">Recommended PHP configuration for smooth media processing</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <ul class="list-group list-group-flush mb-0">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent py-2">
                            <div>
                                <span class="fw-semibold small d-block">upload_max_filesize</span>
                                <small class="text-muted">Allows large video uploads</small>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary font-monospace">128M+</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent py-2">
                            <div>
                                <span class="fw-semibold small d-block">post_max_size</span>
                                <small class="text-muted">Must be equal or larger than upload size</small>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary font-monospace">128M+</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent py-2">
                            <div>
                                <span class="fw-semibold small d-block">max_execution_time</span>
                                <small class="text-muted">Prevents script timeouts during conversion</small>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary font-monospace">300s+</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent py-2">
                            <div>
                                <span class="fw-semibold small d-block">memory_limit</span>
                                <small class="text-muted">PHP execution memory</small>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary font-monospace">256M+</span>
                        </li>
                    </ul>

                    <div class="alert alert-info py-2 px-3 small mt-3 mb-0 rounded-3 d-flex align-items-center gap-2">
                        <i class="feather-info fs-5 flex-shrink-0"></i>
                        <div>{{ __('messages.ffmpeg_info_doc') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Superdesign Tokens & Styles for FFmpeg Page */
    .ffmpeg-stat-card {
        background: var(--admin-premium-surface, #ffffff);
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08)) !important;
        border-radius: var(--admin-premium-radius, 20px) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .ffmpeg-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--admin-premium-shadow-soft, 0 14px 30px rgba(15, 23, 42, 0.06)) !important;
    }
    .ffmpeg-icon-chip {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .bg-soft-purple { background-color: rgba(139, 92, 246, 0.12); }
    .text-purple { color: #8b5cf6 !important; }

    .ffmpeg-main-card,
    .ffmpeg-guide-card {
        background: var(--admin-premium-surface, #ffffff);
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08)) !important;
    }

    .ffmpeg-switch-banner {
        background: var(--admin-premium-surface-alt, #f8fafc);
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.06));
    }

    .ffmpeg-guide-pills .nav-link {
        color: var(--admin-premium-muted, #64748b);
        border-radius: 10px;
        padding: 6px 12px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .ffmpeg-guide-pills .nav-link:hover {
        background: var(--admin-premium-surface-alt, #f1f5f9);
        color: var(--admin-premium-text, #1e293b);
    }
    .ffmpeg-guide-pills .nav-link.active {
        background: #615dfa !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(97, 93, 250, 0.25);
    }

    /* Dark Skin Overrides */
    html.app-skin-dark .ffmpeg-stat-card,
    html.app-skin-dark .ffmpeg-main-card,
    html.app-skin-dark .ffmpeg-guide-card {
        background: var(--admin-premium-surface, #23263b) !important;
        border-color: var(--admin-premium-border, rgba(148, 163, 184, 0.14)) !important;
    }
    html.app-skin-dark .ffmpeg-switch-banner {
        background: var(--admin-premium-surface-alt, #1b1e2f) !important;
        border-color: var(--admin-premium-border, rgba(148, 163, 184, 0.12)) !important;
    }
    html.app-skin-dark .bg-body-tertiary {
        background-color: var(--admin-premium-surface-alt, #1b1e2f) !important;
    }
    html.app-skin-dark .list-group-item {
        border-color: var(--admin-premium-border, rgba(148, 163, 184, 0.1)) !important;
    }
</style>
@endpush

@push('scripts')
<script>
function setBinaryPath(path) {
    const input = document.getElementById('ffmpeg_binary_path');
    if (input) {
        input.value = path;
        input.focus();
    }
}

function testCurrentPathInInput() {
    const input = document.getElementById('ffmpeg_binary_path');
    if (input) {
        debugFfmpeg(input.value);
    }
}

function debugFfmpeg(customPath = null) {
    const log = document.getElementById('ffmpeg-log');
    const statusBox = document.getElementById('ffmpeg-diagnostic-status');
    const btn = document.getElementById('btn-run-debug');
    const icon = document.getElementById('debug-btn-icon');
    const text = document.getElementById('debug-btn-text');
    const binaryInput = document.getElementById('ffmpeg_binary_path');

    const pathToSend = customPath || (binaryInput ? binaryInput.value : '');

    log.innerHTML = `> Initiating FFmpeg diagnostic check on path: "${pathToSend}"...\n> Testing PHP exec() bridge and binary execution...\n> Please wait...`;
    
    if (btn) btn.disabled = true;
    if (icon) icon.className = 'fa-solid fa-spinner fa-spin me-1';
    if (text) text.innerText = 'Testing...';

    fetch('{{ route("admin.settings.ffmpeg.debug") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            binary_path: pathToSend
        })
    })
    .then(response => response.json())
    .then(data => {
        if (btn) btn.disabled = false;
        if (icon) icon.className = 'fa-solid fa-play me-1';
        if (text) text.innerText = '{{ __("messages.ffmpeg_run_test") ?? "Run Diagnostic Test" }}';

        if (statusBox) {
            statusBox.classList.remove('d-none');
            if (data.success) {
                let codecBadges = '';
                if (data.codecs && data.codecs.length > 0) {
                    codecBadges = '<div class="mt-2 d-flex flex-wrap gap-1">' + 
                        data.codecs.map(c => `<span class="badge bg-success-subtle text-success border border-success border-opacity-25 font-monospace small"><i class="feather-check me-1"></i>${c}</span>`).join('') +
                        '</div>';
                }

                statusBox.innerHTML = `
                    <div class="alert alert-success border-0 shadow-sm rounded-3 py-3 px-3">
                        <div class="d-flex align-items-start gap-2">
                            <i class="feather-check-circle fs-5 text-success mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1 text-success">${data.title || 'FFmpeg Verified Successfully!'}</h6>
                                <p class="small mb-1">${data.message || ''}</p>
                                ${codecBadges}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                statusBox.innerHTML = `
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 py-3 px-3">
                        <div class="d-flex align-items-start gap-2">
                            <i class="feather-alert-triangle fs-5 text-danger mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1 text-danger">${data.title || 'Diagnostic Failed'}</h6>
                                <p class="small mb-0">${data.message || ''}</p>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        log.innerHTML = data.log || data.message || 'No log data returned.';
    })
    .catch(error => {
        if (btn) btn.disabled = false;
        if (icon) icon.className = 'fa-solid fa-play me-1';
        if (text) text.innerText = '{{ __("messages.ffmpeg_run_test") ?? "Run Diagnostic Test" }}';

        if (statusBox) {
            statusBox.classList.remove('d-none');
            statusBox.innerHTML = `
                <div class="alert alert-danger border-0 shadow-sm rounded-3 py-3 px-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="feather-alert-triangle fs-5 text-danger"></i>
                        <div><strong>Network Error:</strong> Failed to communicate with diagnostic endpoint.</div>
                    </div>
                </div>
            `;
        }
        log.innerHTML = '> Error: ' + error;
    });
}

function copyDebugLog() {
    const log = document.getElementById('ffmpeg-log');
    if (log && log.innerText) {
        navigator.clipboard.writeText(log.innerText).then(() => {
            alert('Log copied to clipboard!');
        });
    }
}

function clearDebugLog() {
    const log = document.getElementById('ffmpeg-log');
    const statusBox = document.getElementById('ffmpeg-diagnostic-status');
    if (log) log.innerHTML = '> Console cleared. Click "Run Diagnostic Test" to execute test.';
    if (statusBox) statusBox.classList.add('d-none');
}

function copyCode(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Command copied: ' + text);
    });
}
</script>
@endpush
