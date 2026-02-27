{{-- installer/views/admin.blade.php --}}
@extends('installer::layout')

@section('title', 'MyAds — Create Admin Account')

@section('steps')
    <span class="installer-step done"><span class="step-dot"></span> Welcome</span>
    <span class="installer-step done"><span class="step-dot"></span> Requirements</span>
    <span class="installer-step done"><span class="step-dot"></span> Database</span>
    <span class="installer-step done"><span class="step-dot"></span> Migrate</span>
    <span class="installer-step active"><span class="step-dot"></span> Admin</span>
    <span class="installer-step"><span class="step-dot"></span> Finish</span>
@endsection

@section('content')
    <h2><i class="fas fa-user-shield me-2" style="color: var(--installer-primary);"></i>Admin Account</h2>
    <p class="subtitle">Create the main administrator account for your site.</p>

    <form action="{{ route('installer.admin.process') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="admin">
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="admin@example.com">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat password">
        </div>

        <div class="installer-actions">
            <span></span>
            <button type="submit" class="btn-installer">
                Create Admin <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
@endsection
