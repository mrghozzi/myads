@extends('theme::layouts.admin')

@section('title', __('messages.reports'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.reports') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.reports') }}</li>
        </ul>
    </div>
</div>

<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">{{ __('messages.reports_list') ?? __('messages.reports') . ' List' }}</h5>
    </div>
    <div class="card-body custom-card-action p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">{{ __('messages.reported_by') }}</th>
                        <th scope="col">{{ __('messages.report_content') }}</th>
                        <th scope="col">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        @php
                            $target = null;
                            $previewUrl = null;
                            $previewLabel = __('messages.preview');
                            $targetTitle = null;
                            $targetUser = null;

                            if ($report->s_type == 1) {
                                $target = \App\Models\Directory::find($report->tp_id);
                                $previewUrl = $target ? route('directory.show.short', $target->id) : null;
                                $targetTitle = $target?->name;
                                $targetUser = $target?->user;
                            } elseif (in_array($report->s_type, [2, 4, 100])) {
                                $target = \App\Models\ForumTopic::find($report->tp_id);
                                $previewUrl = $target ? route('forum.topic', $target->id) : null;
                                $targetTitle = $target?->name;
                                $targetUser = $target?->user;
                            } elseif ($report->s_type == 3) {
                                $target = \App\Models\News::find($report->tp_id);
                                $previewUrl = $target ? route('news.show', $target->id) : null;
                                $targetTitle = $target?->name;
                            } elseif ($report->s_type == 7867) {
                                $target = \App\Models\Product::withoutGlobalScope('store')->find($report->tp_id);
                                $previewUrl = $target ? route('store.show', $target->name) : null;
                                $targetTitle = $target?->name;
                                $targetUser = $target?->user;
                            } elseif ($report->s_type == 99) {
                                $target = \App\Models\User::find($report->tp_id);
                                $previewUrl = $target ? route('profile.short', $target->id) : null;
                                $targetTitle = $target?->username;
                                $targetUser = $target;
                            } elseif ($report->s_type == 201) {
                                $target = \App\Models\Link::find($report->tp_id);
                                $previewUrl = $target?->url;
                                $targetTitle = $target?->name;
                                $targetUser = $target?->user;
                            } elseif ($report->s_type == 202) {
                                $target = \App\Models\Banner::find($report->tp_id);
                                $previewUrl = $target ? route('admin.banners.edit', $target->id) : null;
                                $targetTitle = $target?->name;
                                $targetUser = $target?->user;
                            } elseif ($report->s_type == 203) {
                                $target = \App\Models\Visit::find($report->tp_id);
                                $previewUrl = $target?->url;
                                $targetTitle = $target?->name;
                                $targetUser = $target?->user;
                            } elseif ($report->s_type == 204) {
                                $target = \App\Models\SmartAd::find($report->tp_id);
                                $previewUrl = $target ? route('admin.smart_ads.edit', $target->id) : null;
                                $targetTitle = $target?->displayTitle();
                                $targetUser = $target?->user;
                            } elseif ($report->s_type == 205) {
                                $target = \App\Models\Option::where('o_type', 'knowledgebase')->find($report->tp_id);
                                $previewUrl = $target ? route('kb.show', ['name' => $target->o_mode, 'article' => $target->name]) : null;
                                $targetTitle = $target ? __('messages.knowledgebase') . ': ' . $target->name : null;
                                $targetUser = ($target && (int) $target->o_parent > 0) ? \App\Models\User::find($target->o_parent) : null;
                            }
                        @endphp
                        <tr class="{{ $report->statu == 1 ? 'table-warning' : '' }}">
                            <td><span class="fw-bold">#{{ $report->id }}</span></td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if($report->uid == 0)
                                        <span class="font-medium text-dark">{{ __('messages.guest') }}</span>
                                    @else
                                        @if($report->reporter)
                                            <a href="{{ route('profile.show', $report->reporter->username) }}" class="fw-bold text-primary" target="_blank">
                                                {{ $report->reporter->username }}
                                            </a>
                                            <a href="{{ route('messages.create', ['recipient' => $report->reporter->username]) }}" class="fs-11 text-muted">
                                                <i class="feather-mail me-1"></i> {{ __('messages.message') }}
                                            </a>
                                        @else
                                            <span class="font-medium text-dark">{{ __('messages.unknown') }}</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td>
                                <p class="mb-2 text-dark">{{ $report->txt }}</p>
                                @if($target)
                                    @if($targetTitle)
                                        <div class="fw-bold mb-2">{{ $targetTitle }}</div>
                                    @endif
                                    <div class="d-flex flex-wrap gap-2">
                                        @if($previewUrl)
                                            <a href="{{ $previewUrl }}" target="_blank" class="btn btn-sm btn-warning">
                                                <i class="feather-external-link me-1"></i> {{ $previewLabel }}
                                            </a>
                                        @endif
                                        @if($targetUser)
                                            <a href="{{ route('profile.show', $targetUser->username) }}" target="_blank" class="btn btn-sm btn-secondary">
                                                <i class="feather-user me-1"></i> {{ $targetUser->username }}
                                            </a>
                                            <a href="{{ route('messages.create', ['recipient' => $targetUser->username]) }}" class="btn btn-sm btn-info">
                                                <i class="feather-mail me-1"></i> {{ __('messages.message') }}
                                            </a>
                                            <a href="{{ route('admin.users.edit', $targetUser->id) }}" class="btn btn-sm btn-success">
                                                <i class="feather-edit-2 me-1"></i> {{ __('messages.edit') }}
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-muted">{{ __('messages.reported_content_removed') ?? 'The reported content has been removed' }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($report->statu == 1)
                                        <a href="{{ route('admin.reports', ['wtid' => $report->id]) }}" class="btn btn-sm btn-danger" title="{{ __('messages.review') }}">
                                            <i class="feather-eye-off"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.reports.delete', $report->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('{{ __('messages.confirm_delete_report') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $reports->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
