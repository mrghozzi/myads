{{-- installer/views/update.blade.php --}}
@extends('installer::layout')

@section('title', 'MyAds — Upgrade to v4.0')
@section('brand-subtitle', 'Upgrade Wizard')

@section('content')
    <h2><i class="fas fa-arrow-up me-2" style="color: var(--installer-warning);"></i>Upgrade to v4.0</h2>
    <p class="subtitle">Migrate your v3.x installation to MyAds v4.0 (Laravel).</p>

    {{-- Warning --}}
    <div class="alert-installer alert-warning">
        <i class="fas fa-exclamation-triangle me-1"></i>
        <strong>Important:</strong> Please create a full backup of your database and files before proceeding.
    </div>

    {{-- What will happen --}}
    <div style="padding: 1rem; background: rgba(99,102,241,.06); border-radius: .5rem; border: 1px solid rgba(99,102,241,.15); margin-bottom: 1.25rem;">
        <p style="font-size: .85rem; font-weight: 600; color: var(--installer-text); margin: 0 0 .5rem;">
            <i class="fas fa-list-check" style="color: var(--installer-primary);"></i>
            The upgrade process will:
        </p>
        <ul style="font-size: .8rem; color: var(--installer-text-muted); margin: 0; padding-left: 1.25rem; line-height: 1.8;">
            <li>Add <code>created_at</code> / <code>updated_at</code> to legacy tables</li>
            <li>Add missing columns to <code>users</code> and <code>setting</code> tables</li>
            <li>Create Laravel system tables (sessions, cache, jobs, etc.)</li>
            <li>Run pending migrations and seed default data</li>
            <li>Create storage symlink</li>
            <li>Copy old upload directory if found</li>
            <li>Generate APP_KEY if missing</li>
            <li>Update version to <strong>4.0.0</strong></li>
        </ul>
    </div>

    <form action="{{ route('installer.update.process') }}" method="POST" id="updateForm">
        @csrf
        <div class="installer-actions">
            <a href="{{ url('/') }}" class="btn-outline-installer" style="text-decoration:none;">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn-warning-installer" id="updateBtn">
                <i class="fas fa-rocket"></i> Start Upgrade
            </button>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    document.getElementById('updateForm').addEventListener('submit', function() {
        var btn = document.getElementById('updateBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Upgrading… Please wait';
    });
</script>
@endsection
