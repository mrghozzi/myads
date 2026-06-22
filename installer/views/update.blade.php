{{-- installer/views/update.blade.php --}}
@extends('installer::layout')

@section('title', 'MyAds — Upgrade & Update')
@section('brand-subtitle', 'Upgrade Wizard')

@section('content')
    <h2><i class="fas fa-arrow-up me-2" style="color: var(--installer-warning);"></i>System Upgrade</h2>
    <p class="subtitle">Prepare and update your MyAds installation to v{{ \App\Support\SystemVersion::CURRENT }}.</p>

    {{-- Bypass Mode Warning --}}
    @if($usingBypass ?? false)
        <div class="alert-installer alert-warning" style="border-left: 4px solid var(--installer-warning);">
            <i class="fas fa-shield-alt me-1"></i>
            <strong>Security Bypass Active:</strong> Access granted via temporary <code>storage/allow_update</code> file. This file will be deleted automatically upon successful update.
        </div>
    @endif

    {{-- Warning --}}
    <div class="alert-installer alert-danger">
        <i class="fas fa-exclamation-triangle me-1"></i>
        <strong>Backup Required:</strong> Please create a full backup of your database and code files before proceeding.
    </div>

    <form action="{{ route('installer.update.process') }}" method="POST" id="updateForm">
        @csrf

        <div class="mb-4">
            <label class="form-label" style="font-size: .85rem; font-weight: 600;">Select Upgrade Path</label>
            
            {{-- Incremental Update card --}}
            <div class="card p-3 mb-3" style="background: rgba(99,102,241,.03); border: 1px solid var(--installer-card-border); cursor: pointer;" onclick="document.getElementById('type_incremental').checked = true; selectType('incremental');">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="upgrade_type" id="type_incremental" value="incremental" checked>
                    <label class="form-check-label font-semibold text-white ps-1" for="type_incremental" style="font-weight: 600; cursor: pointer;">
                        Incremental Update (v4.x to v{{ \App\Support\SystemVersion::CURRENT }})
                    </label>
                    <div style="font-size: .8rem; color: var(--installer-text-muted); margin-top: .25rem; margin-left: 1.5rem;">
                        Recommended for existing v4.x installations. Runs new migrations, processes version scripts, and clears system cache.
                    </div>
                </div>
            </div>

            {{-- Legacy Migration card --}}
            <div class="card p-3" style="background: rgba(245,158,11,.02); border: 1px solid var(--installer-card-border); cursor: pointer;" onclick="document.getElementById('type_legacy').checked = true; selectType('legacy');">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="upgrade_type" id="type_legacy" value="legacy">
                    <label class="form-check-label font-semibold text-white ps-1" for="type_legacy" style="font-weight: 600; cursor: pointer;">
                        Legacy Database Migration (v3.x to v{{ \App\Support\SystemVersion::CURRENT }})
                    </label>
                    <div style="font-size: .8rem; color: var(--installer-text-muted); margin-top: .25rem; margin-left: 1.5rem;">
                        Use this only if you are migrating a legacy v3.x MySQL database structure to the new Laravel version.
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions list --}}
        <div class="mb-4" id="details-box" style="padding: 1rem; background: rgba(99,102,241,.06); border-radius: .5rem; border: 1px solid rgba(99,102,241,.15);">
            <p style="font-size: .85rem; font-weight: 600; color: var(--installer-text); margin: 0 0 .5rem;">
                <i class="fas fa-list-check" style="color: var(--installer-primary);"></i>
                The upgrade process will:
            </p>
            <ul id="details-list" style="font-size: .8rem; color: var(--installer-text-muted); margin: 0; padding-left: 1.25rem; line-height: 1.8;">
                {{-- Dynamic items injected via JS --}}
            </ul>
        </div>

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
    const incrementalDetails = [
        'Apply database migrations & new tables',
        'Run custom version upgrade script (update.php)',
        'Seed system default additions (if any)',
        'Update version configuration to <strong>v{{ \App\Support\SystemVersion::CURRENT }}</strong>',
        'Optimize & clear system cache'
    ];

    const legacyDetails = [
        'Add <code>created_at</code> / <code>updated_at</code> columns to legacy tables',
        'Add missing Laravel compatibility columns to <code>users</code> & <code>setting</code> tables',
        'Create Laravel system tables (sessions, cache, failed_jobs, cache_locks, jobs)',
        'Create storage directory symlink',
        'Copy old upload files if found',
        'Generate application <code>APP_KEY</code> if missing',
        'Apply Laravel migrations & seed default data',
        'Update version to <strong>v{{ \App\Support\SystemVersion::CURRENT }}</strong>'
    ];

    function selectType(type) {
        const list = document.getElementById('details-list');
        list.innerHTML = '';
        const details = type === 'legacy' ? legacyDetails : incrementalDetails;
        
        details.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = item;
            list.appendChild(li);
        });

        // Highlight selected card border
        const cards = document.querySelectorAll('.card');
        if (type === 'incremental') {
            cards[0].style.borderColor = 'var(--installer-primary)';
            cards[1].style.borderColor = 'var(--installer-card-border)';
        } else {
            cards[0].style.borderColor = 'var(--installer-card-border)';
            cards[1].style.borderColor = 'var(--installer-warning)';
        }
    }

    // Initialize
    selectType('incremental');

    document.getElementById('updateForm').addEventListener('submit', function() {
        var btn = document.getElementById('updateBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Upgrading… Please wait';
    });
</script>
@endsection
