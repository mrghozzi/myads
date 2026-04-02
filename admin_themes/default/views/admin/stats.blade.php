@extends('admin::layouts.admin')

@section('title', $title)

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ $title }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ $title }}</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded">
                        <i class="feather-bar-chart-2"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $title }}</h5>
                        <p class="text-muted mb-0">
                            @if(request()->has('id'))
                                N°{{ request()->id }}
                            @elseif(request()->has('st'))
                                @ {{ \App\Models\User::find(request()->st)->username ?? __('messages.unknown') ?? 'Unknown' }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i class="feather-arrow-left me-2"></i>{{ __('messages.go_back') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body custom-card-action p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">#ID</th>
                                <th scope="col">{{ __('messages.url_link') ?? 'Url' }}</th>
                                <th scope="col">{{ __('messages.time') ?? 'Time' }}</th>
                                <th scope="col">{{ __('messages.browser') ?? 'Browser' }}</th>
                                <th scope="col">{{ __('messages.platform') ?? 'Platform' }}</th>
                                <th scope="col">{{ __('messages.ip') ?? 'Ip' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats as $stat)
                            <tr>
                                <td>{{ $stat->id }}</td>
                                <td>
                                    @if($stat->r_link == 'N')
                                        <span class="btn btn-sm btn-danger disabled"><i class="feather-link-2"></i></span>
                                    @else
                                        <a href="{{ $stat->r_link }}" target="_blank" class="btn btn-sm btn-success"><i class="feather-external-link"></i></a>
                                    @endif
                                </td>
                                <td>
                                    {{ date('d, M Y', $stat->r_date) }}<br>
                                    <small class="text-muted"><i class="feather-clock"></i> {{ date('H:i:s', $stat->r_date) }}</small>
                                </td>
                                <td>
                                    {{ $stat->browser['name'] }}<br>
                                    <small class="text-muted">{{ $stat->browser['version'] }}</small>
                                </td>
                                <td>{{ $stat->browser['platform'] }}</td>
                                <td>
                                    <a href="http://ip.is-best.net/?ip={{ $stat->v_ip }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="feather-map-pin"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $stats->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
