@extends('admin::layouts.admin')

@section('title', __('messages.security_confirm_admin_password_title'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.security_confirm_admin_password_title') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.security_title') }}</li>
        </ul>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('messages.security_confirm_admin_password_title') }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ __('messages.security_confirm_admin_password_help') }}</p>
                <form action="{{ route('admin.confirm-password.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="password">{{ __('messages.password') }}</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">{{ __('messages.confirm') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
