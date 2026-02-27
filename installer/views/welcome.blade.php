{{-- installer/views/welcome.blade.php --}}
@extends('installer::layout')

@section('title', 'MyAds — Installer')
@section('brand-subtitle', 'Welcome to the Installation Wizard')

@section('content')
    <div class="text-center mb-4">
        <div style="font-size: 3rem; margin-bottom: 1rem;">
            <i class="fas fa-rocket" style="background: linear-gradient(135deg, var(--installer-primary), #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
        </div>
        <h2>Welcome to MyAds</h2>
        <p class="subtitle">Choose an option below to get started.</p>
    </div>

    <div class="d-grid gap-3">
        <a href="{{ route('installer.requirements') }}" class="btn-installer justify-content-center" style="text-decoration:none; padding: .85rem;">
            <i class="fas fa-download"></i>
            Fresh Installation
        </a>
        <a href="{{ route('installer.update') }}" class="btn-warning-installer justify-content-center" style="text-decoration:none; padding: .85rem;">
            <i class="fas fa-arrow-up"></i>
            Upgrade from v3.x to v4.0
        </a>
    </div>

    <div style="margin-top: 1.5rem; padding: .75rem 1rem; background: rgba(99,102,241,.06); border-radius: .5rem; border: 1px solid rgba(99,102,241,.15);">
        <p style="font-size: .8rem; color: var(--installer-text-muted); margin: 0;">
            <i class="fas fa-info-circle" style="color: var(--installer-primary);"></i>
            Select <strong>Fresh Installation</strong> if this is a new setup.
            Select <strong>Upgrade</strong> if you already have v3.x installed and want to migrate your data.
        </p>
    </div>
@endsection
