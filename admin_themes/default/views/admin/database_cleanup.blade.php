@extends('admin::layouts.admin')

@section('title', __('messages.database_cleanup'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li>{{ __('messages.database_cleanup') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.maintenance') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.database_cleanup') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.database_cleanup_desc') }}</p>
        </div>
    </section>

    <div class="admin-content-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('messages.cleanup_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.database_cleanup.action') }}" method="POST">
                            @csrf
                            
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="p-3 border rounded">
                                        <h6 class="mb-2">{{ __('messages.state_table') }}</h6>
                                        <p class="text-muted small">{{ __('messages.current_records') }}: <strong>{{ number_format($stateCount) }}</strong></p>
                                        <div class="form-group mb-0">
                                            <label class="form-label">{{ __('messages.delete_older_than_days') }}</label>
                                            <input type="number" name="state_days" class="form-control" min="1" placeholder="30">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 border rounded">
                                        <h6 class="mb-2">{{ __('messages.banner_impressions') }}</h6>
                                        <p class="text-muted small">{{ __('messages.current_records') }}: <strong>{{ number_format($bannerImpressionsCount) }}</strong></p>
                                        <div class="form-group mb-0">
                                            <label class="form-label">{{ __('messages.delete_older_than_days') }}</label>
                                            <input type="number" name="banner_impressions_days" class="form-control" min="1" placeholder="30">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 border rounded">
                                        <h6 class="mb-2">{{ __('messages.seo_daily_metrics') }}</h6>
                                        <p class="text-muted small">{{ __('messages.current_records') }}: <strong>{{ number_format($seoMetricsCount) }}</strong></p>
                                        <div class="form-group mb-0">
                                            <label class="form-label">{{ __('messages.delete_older_than_days') }}</label>
                                            <input type="number" name="seo_metrics_days" class="form-control" min="1" placeholder="30">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" onclick="return confirm('{{ __('messages.confirm_cleanup') }}')">
                                    <i class="feather-trash-2 me-2"></i> {{ __('messages.execute_cleanup') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
