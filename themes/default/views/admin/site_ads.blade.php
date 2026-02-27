@extends('theme::layouts.admin')

@section('title', __('messages.e_ads'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.e_ads') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.e_ads') }}</li>
        </ul>
    </div>
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                <button type="submit" form="site-ads-bulk-form" class="btn btn-primary">
                    <i class="feather-save me-2"></i>{{ __('messages.save') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.e_ads') }}</h5>
            </div>
            <div class="card-body custom-card-action">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form id="site-ads-bulk-form" action="{{ route('admin.site_ads.update_all') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
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
                                        <td><span class="fw-bold">#{{ $ad->id }}</span></td>
                                        <td>
                                            <div class="fw-bold">{{ $names[$ad->id] ?? (__('messages.ad_position', ['id' => $ad->id]) ?? 'Ad Position #' . $ad->id) }}</div>
                                        </td>
                                        <td>
                                            <textarea rows="6" name="code_ads[{{ $ad->id }}]" class="form-control">{{ $ad->code_ads }}</textarea>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" formaction="{{ route('admin.site_ads.update', $ad->id) }}" class="btn btn-sm btn-light-brand">
                                                    <i class="feather-save me-1"></i>{{ __('messages.save') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
