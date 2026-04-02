@extends('admin::layouts.admin')

@section('title', __('messages.e_ads'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.e_ads') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.style') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.e_ads') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.code') }} / {{ __('messages.save') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card w-100">
                <button type="submit" form="site-ads-bulk-form" class="btn btn-primary w-100">
                    <i class="feather-save me-2"></i>{{ __('messages.save') }}
                </button>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success mb-0">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.e_ads') }}</span>
                <h2 class="admin-panel__title">{{ number_format($ads->count()) }}</h2>
            </div>
        </div>
        <div class="admin-panel__body">
            <form id="site-ads-bulk-form" action="{{ route('admin.site_ads.update_all') }}" method="POST">
                @csrf
                <div class="admin-table-wrap">
                    <table class="table table-hover mb-0 admin-table admin-table-cardify admin-density-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.code') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ads as $ad)
                                <tr>
                                    <td data-label="#">#{{ $ad->id }}</td>
                                    <td data-label="{{ __('messages.name') }}">
                                        <strong>{{ $names[$ad->id] ?? (__('messages.ad_position', ['id' => $ad->id]) ?? 'Ad Position #' . $ad->id) }}</strong>
                                    </td>
                                    <td data-label="{{ __('messages.code') }}">
                                        <textarea rows="6" name="code_ads[{{ $ad->id }}]" class="form-control">{{ $ad->code_ads }}</textarea>
                                    </td>
                                    <td data-label="{{ __('messages.actions') }}" class="text-end">
                                        <button type="submit" formaction="{{ route('admin.site_ads.update', $ad->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="feather-save me-1"></i>{{ __('messages.save') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
