{{-- installer/views/database.blade.php --}}
@extends('installer::layout')

@section('title', 'MyAds — Database Configuration')

@section('steps')
    <span class="installer-step done"><span class="step-dot"></span> Welcome</span>
    <span class="installer-step done"><span class="step-dot"></span> Requirements</span>
    <span class="installer-step active"><span class="step-dot"></span> Database</span>
    <span class="installer-step"><span class="step-dot"></span> Migrate</span>
    <span class="installer-step"><span class="step-dot"></span> Admin</span>
    <span class="installer-step"><span class="step-dot"></span> Finish</span>
@endsection

@section('content')
    <h2><i class="fas fa-database me-2" style="color: var(--installer-primary);"></i>Database Setup</h2>
    <p class="subtitle">Enter your database connection details below.</p>

    <form action="{{ route('installer.database.process') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-8">
                <label class="form-label">Database Host</label>
                <input type="text" name="host" class="form-control" value="{{ old('host', '127.0.0.1') }}" required placeholder="127.0.0.1">
            </div>
            <div class="col-4">
                <label class="form-label">Port</label>
                <input type="text" name="port" class="form-control" value="{{ old('port', '3306') }}" required placeholder="3306">
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label">Database Name</label>
            <input type="text" name="database" class="form-control" value="{{ old('database') }}" required placeholder="myads_db">
        </div>

        <div class="mt-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="{{ old('username', 'root') }}" required placeholder="root">
        </div>

        <div class="mt-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" value="{{ old('password') }}" placeholder="Leave empty if none">
        </div>

        <div class="mt-3">
            <label class="form-label">Application URL</label>
            <input type="url" name="app_url" class="form-control" value="{{ old('app_url', url('/')) }}" placeholder="https://yourdomain.com">
            <small style="color: var(--installer-text-muted); font-size: .75rem;">The URL your site will be accessed from (without trailing slash).</small>
        </div>

        <div class="installer-actions">
            <a href="{{ route('installer.requirements') }}" class="btn-outline-installer" style="text-decoration:none;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn-installer">
                Test & Save <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
@endsection
