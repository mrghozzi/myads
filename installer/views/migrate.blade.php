{{-- installer/views/migrate.blade.php --}}
@extends('installer::layout')

@section('title', 'MyAds — Run Migrations')

@section('steps')
    <span class="installer-step done"><span class="step-dot"></span> Welcome</span>
    <span class="installer-step done"><span class="step-dot"></span> Requirements</span>
    <span class="installer-step done"><span class="step-dot"></span> Database</span>
    <span class="installer-step active"><span class="step-dot"></span> Migrate</span>
    <span class="installer-step"><span class="step-dot"></span> Admin</span>
    <span class="installer-step"><span class="step-dot"></span> Finish</span>
@endsection

@section('content')
    <h2><i class="fas fa-cogs me-2" style="color: var(--installer-primary);"></i>Database Migration</h2>
    <p class="subtitle">Create the required database tables and seed default data.</p>

    <div style="padding: 1.25rem; background: rgba(99,102,241,.06); border-radius: .5rem; border: 1px solid rgba(99,102,241,.15); margin-bottom: 1rem;">
        <p style="font-size: .85rem; color: var(--installer-text); margin: 0 0 .5rem;">
            <i class="fas fa-info-circle" style="color: var(--installer-primary);"></i>
            This will perform the following actions:
        </p>
        <ul style="font-size: .8rem; color: var(--installer-text-muted); margin: 0; padding-left: 1.25rem;">
            <li>Run database migrations (create tables)</li>
            <li>Seed default data (settings, categories, etc.)</li>
            <li>Create storage symlink</li>
        </ul>
    </div>

    <form action="{{ route('installer.migrate.process') }}" method="POST" id="migrateForm">
        @csrf
        <div class="installer-actions">
            <a href="{{ route('installer.database') }}" class="btn-outline-installer" style="text-decoration:none;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn-installer" id="migrateBtn">
                <i class="fas fa-play"></i> Run Migrations
            </button>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    document.getElementById('migrateForm').addEventListener('submit', function() {
        var btn = document.getElementById('migrateBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Running…';
    });
</script>
@endsection
