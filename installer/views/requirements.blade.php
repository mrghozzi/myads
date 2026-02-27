{{-- installer/views/requirements.blade.php --}}
@extends('installer::layout')

@section('title', 'MyAds — Server Requirements')

@section('steps')
    <span class="installer-step done"><span class="step-dot"></span> Welcome</span>
    <span class="installer-step active"><span class="step-dot"></span> Requirements</span>
    <span class="installer-step"><span class="step-dot"></span> Database</span>
    <span class="installer-step"><span class="step-dot"></span> Migrate</span>
    <span class="installer-step"><span class="step-dot"></span> Admin</span>
    <span class="installer-step"><span class="step-dot"></span> Finish</span>
@endsection

@section('content')
    <h2><i class="fas fa-server me-2" style="color: var(--installer-primary);"></i>Server Requirements</h2>
    <p class="subtitle">Make sure your server meets the following requirements.</p>

    {{-- PHP Extensions --}}
    <h6 style="font-size: .8rem; font-weight: 600; color: var(--installer-text-muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: .5rem;">
        PHP Extensions (Required)
    </h6>
    <div style="margin-bottom: 1.25rem;">
        @foreach($requirements as $name => $req)
            <div class="check-item">
                <span>
                    {{ strtoupper($name) }}
                    @if($name === 'php')
                        <small style="color: var(--installer-text-muted);">(>= {{ $req['version'] }}, current: {{ $req['current'] }})</small>
                    @endif
                </span>
                @if($req['status'])
                    <span class="status-ok"><i class="fas fa-check-circle"></i></span>
                @else
                    <span class="status-fail"><i class="fas fa-times-circle"></i></span>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Optional Extensions --}}
    <h6 style="font-size: .8rem; font-weight: 600; color: var(--installer-text-muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: .5rem;">
        PHP Extensions (Optional)
    </h6>
    <div style="margin-bottom: 1.25rem;">
        @foreach($optional as $name => $ext)
            <div class="check-item">
                <span>
                    {{ strtoupper($name) }}
                    <small style="color: var(--installer-text-muted);">— {{ $ext['note'] }}</small>
                </span>
                @if($ext['status'])
                    <span class="status-ok"><i class="fas fa-check-circle"></i></span>
                @else
                    <span style="color: var(--installer-warning);"><i class="fas fa-exclamation-triangle"></i> Optional</span>
                @endif
            </div>
        @endforeach
    </div>


    {{-- Writable Folders --}}
    <h6 style="font-size: .8rem; font-weight: 600; color: var(--installer-text-muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: .5rem;">
        Folder Permissions
    </h6>
    <div style="margin-bottom: .5rem;">
        @foreach($folders as $folder => $writable)
            <div class="check-item">
                <span><code style="font-size: .8rem; color: var(--installer-primary-hover);">{{ $folder }}</code></span>
                @if($writable)
                    <span class="status-ok"><i class="fas fa-check-circle"></i> Writable</span>
                @else
                    <span class="status-fail"><i class="fas fa-times-circle"></i> Not Writable</span>
                @endif
            </div>
        @endforeach
    </div>

    <div class="installer-actions">
        <a href="{{ route('installer.welcome') }}" class="btn-outline-installer" style="text-decoration:none;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        @if($allMet)
            <a href="{{ route('installer.database') }}" class="btn-installer" style="text-decoration:none;">
                Continue <i class="fas fa-arrow-right"></i>
            </a>
        @else
            <button class="btn-installer" disabled>
                <i class="fas fa-ban"></i> Requirements Not Met
            </button>
        @endif
    </div>
@endsection
