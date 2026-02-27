@extends('theme::layouts.admin')

@section('title', __('messages.edit_banner'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.edit_banner') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.banners') }}">{{ __('messages.bannads') }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.edit') }}</li>
        </ul>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.bannads') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Name ADS') }}</label>
                            <input type="text" class="form-control" name="name" value="{{ $banner->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Url Link') }}</label>
                            <input type="text" class="form-control" name="url" value="{{ $banner->url }}" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Banner size') }}</label>
                            <select class="form-control" name="px" required>
                                <option value="468" {{ $banner->px == 468 ? 'selected' : '' }}>468x60 (-1 pts)</option>
                                <option value="728" {{ $banner->px == 728 ? 'selected' : '' }}>728x90 (-1 pts)</option>
                                <option value="300" {{ $banner->px == 300 ? 'selected' : '' }}>300x250 (-1 pts)</option>
                                <option value="160" {{ $banner->px == 160 ? 'selected' : '' }}>160x600 (-1 pts)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Image Link') }}</label>
                            <input type="text" class="form-control" name="img" value="{{ $banner->img }}" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.Statu') }}</label>
                            <select class="form-control" name="statu" required>
                                <option value="1" {{ $banner->statu == 1 ? 'selected' : '' }}>ON</option>
                                <option value="2" {{ $banner->statu == 2 ? 'selected' : '' }}>OFF</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">{{ __('messages.edit') }}</button>
                    </div>
                </form>

                <div class="mt-5 text-center">
                    <img src="{{ $banner->img }}" alt="{{ $banner->name }}" class="img-fluid border rounded" style="max-width: 100%;">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
