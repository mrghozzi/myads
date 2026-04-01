@extends('theme::layouts.admin')

@section('title', __('messages.edit_banner'))

@section('content')
@php($bannerSizes = \App\Support\BannerSizeCatalog::ordered())

<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.banners') }}">{{ __('messages.bannads') }}</a></li>
                <li>{{ __('messages.edit') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.bannads') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.edit_banner') }}</h1>
            <p class="admin-hero__copy">#{{ $banner->id }} / {{ $banner->name }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.size') }}</span>
                    <span class="admin-summary-value">{{ $banner->px }}</span>
                </div>
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.Statu') }}</span>
                    <span class="admin-summary-value">{{ $banner->statu == 1 ? 'ON' : 'OFF' }}</span>
                </div>
            </div>
        </div>
    </section>

    <div class="admin-split-grid">
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.edit') }}</span>
                    <h2 class="admin-panel__title">{{ __('messages.bannads') }}</h2>
                </div>
            </div>
            <div class="admin-panel__body">
                <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" class="row g-4">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Name ADS') }}</label>
                        <input type="text" class="form-control" name="name" value="{{ $banner->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Url Link') }}</label>
                        <input type="text" class="form-control" name="url" value="{{ $banner->url }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Banner size') }}</label>
                        <select class="form-select" name="px" required>
                            @foreach($bannerSizes as $size)
                                <option value="{{ $size['value'] }}" {{ $banner->px == $size['value'] ? 'selected' : '' }}>{{ $size['label'] }} (-1 pts)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Image Link') }}</label>
                        <input type="text" class="form-control" name="img" value="{{ $banner->img }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.Statu') }}</label>
                        <select class="form-select" name="statu" required>
                            <option value="1" {{ $banner->statu == 1 ? 'selected' : '' }}>ON</option>
                            <option value="2" {{ $banner->statu == 2 ? 'selected' : '' }}>OFF</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">{{ __('messages.edit') }}</button>
                    </div>
                </form>
            </div>
        </section>

        <aside class="admin-panel">
            <div class="admin-panel__header">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.view') }}</span>
                    <h2 class="admin-panel__title">{{ __('messages.bannads') }}</h2>
                </div>
            </div>
            <div class="admin-panel__body">
                <div class="admin-preview-card text-center">
                    <img src="{{ $banner->img }}" alt="{{ $banner->name }}" class="img-fluid rounded">
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
