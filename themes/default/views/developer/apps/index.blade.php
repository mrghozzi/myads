@extends('theme::layouts.app')

@section('title', __('messages.my_apps'))

@section('content')
<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 fw-bold mb-0">@lang('messages.my_apps')</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('developer.apps.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> @lang('messages.create_app')
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if(count($apps) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">@lang('messages.app_name')</th>
                                <th class="border-0 px-4 py-3">@lang('messages.domain')</th>
                                <th class="border-0 px-4 py-3">@lang('messages.status')</th>
                                <th class="border-0 px-4 py-3 text-end">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apps as $app)
                                <tr>
                                    <td class="px-4 py-3 fw-bold">
                                        <a href="{{ route('developer.apps.show', $app->id) }}" class="text-decoration-none text-dark">
                                            {{ $app->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-muted">{{ $app->domain }}</td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-{{ $app->status === 'active' ? 'success' : ($app->status === 'draft' ? 'secondary' : 'warning') }}">
                                            @lang('messages.app_status_' . $app->status)
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <a href="{{ route('developer.apps.show', $app->id) }}" class="btn btn-sm btn-outline-secondary">
                                            @lang('messages.manage')
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-cubes fa-3x mb-3 text-light"></i>
                    <p class="mb-0">@lang('messages.no_apps_yet')</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
