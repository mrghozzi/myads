@extends('theme::layouts.admin')

@section('title', __('Edit User'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('Edit User') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">{{ __('messages.users') }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.edit') }}</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('admin.users') }}" class="btn btn-primary"><i class="feather-users me-2"></i>{{ __('List Users') }}</a>
                    <a href="{{ route('profile.show', $user->username) }}" target="_blank" class="btn btn-info"><i class="feather-user me-2"></i>{{ __('messages.view_profile') }}</a>
                    <a href="{{ route('admin.banners', ['user_id' => $user->id]) }}" class="btn btn-warning"><i class="feather-link me-2"></i>{{ __('messages.Banners') }}</a>
                    <a href="{{ route('admin.links', ['user_id' => $user->id]) }}" class="btn btn-success"><i class="feather-eye me-2"></i>{{ __('messages.Links') }}</a>
                    <a href="{{ route('admin.smart_ads', ['user_id' => $user->id]) }}" class="btn btn-dark"><i class="feather-target me-2"></i>{{ __('messages.smart_ads') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">{{ __('Edit User Details') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.username') }} <span class="text-muted small">({{ __('Login Identity') }})</span></label>
                            <input type="text" class="form-control" name="username" value="{{ $user->username }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('User Slug') }} <span class="text-muted small">({{ __('Profile URL Handle') }})</span></label>
                            <input type="text" class="form-control" name="slug" value="{{ $slug }}" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Verified Account') }}</label>
                            <select class="form-control" name="ucheck">
                                <option value="0" {{ $user->ucheck == 0 ? 'selected' : '' }}>{{ __('messages.No') }}</option>
                                <option value="1" {{ $user->ucheck == 1 ? 'selected' : '' }}>{{ __('messages.Yes') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.pts') }}</label>
                            <input type="number" step="0.01" class="form-control" name="pts" value="{{ $user->pts }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Exchange Visits PTS') }} (vu)</label>
                            <input type="number" step="0.01" class="form-control" name="vu" value="{{ $user->vu }}" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Banner Ads PTS') }} (nvu)</label>
                            <input type="number" step="0.01" class="form-control" name="nvu" value="{{ $user->nvu }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Text Ads PTS') }} (nlink)</label>
                            <input type="number" step="0.01" class="form-control" name="nlink" value="{{ $user->nlink }}" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.smart_ads_credits_admin') }}</label>
                            <input type="number" step="0.01" class="form-control" name="nsmart" value="{{ $user->nsmart }}" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Update User') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">{{ __('Change Password') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.password', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label">{{ __('messages.new_password') }}</label>
                        <input type="password" class="form-control" name="password" required minlength="8" autocomplete="new-password">
                        <small class="text-muted">{{ __('Minimum 8 characters') }}</small>
                    </div>

                    <button type="submit" class="btn btn-warning text-dark">{{ __('Update Password') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
