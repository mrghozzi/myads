{{-- installer/views/finish.blade.php --}}
@extends('installer::layout')

@section('title', 'MyAds — Installation Complete')

@section('steps')
    <span class="installer-step done"><span class="step-dot"></span> Welcome</span>
    <span class="installer-step done"><span class="step-dot"></span> Requirements</span>
    <span class="installer-step done"><span class="step-dot"></span> Database</span>
    <span class="installer-step done"><span class="step-dot"></span> Migrate</span>
    <span class="installer-step done"><span class="step-dot"></span> Admin</span>
    <span class="installer-step active"><span class="step-dot"></span> Finish</span>
@endsection

@section('content')
    <div class="text-center" style="padding: 1rem 0;">
        <div style="font-size: 3.5rem; margin-bottom: 1rem; color: var(--installer-success);">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Installation Complete!</h2>
        <p class="subtitle">MyAds v{{ \App\Support\SystemVersion::CURRENT }} has been successfully installed.</p>

        @if(session('log'))
            <div class="update-log text-start">
                @foreach(session('log') as $line)
                    <div>{!! $line !!}</div>
                @endforeach
            </div>
        @endif

        <div style="margin-top: 1.5rem; padding: .75rem 1rem; background: rgba(245,158,11,.08); border-radius: .5rem; border: 1px solid rgba(245,158,11,.2);">
            <p style="font-size: .8rem; color: #fcd34d; margin: 0;">
                <i class="fas fa-shield-alt"></i>
                <strong>Security Tip:</strong> The installer is now disabled. Delete the <code>installer/</code> folder for extra security.
            </p>
        </div>

        <div class="d-grid" style="margin-top: 1.5rem;">
            <a href="{{ url('/') }}" class="btn-success-installer justify-content-center" style="text-decoration:none; padding: .85rem;">
                <i class="fas fa-home"></i> Go to Homepage
            </a>
        </div>
    </div>
@endsection
