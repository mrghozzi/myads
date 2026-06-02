@extends('admin::layouts.admin')

@section('title', __('messages.yt_campaigns_management'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.yt_campaigns') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.yt_campaigns') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.manage_yt_campaigns') }}</p>

            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.total_campaigns') }}</span>
                    <span class="admin-stat-value">{{ number_format($videos->total()) }}</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <a href="{{ route('admin.youtube.settings') }}" class="btn btn-primary admin-icon-btn" title="{{ __('messages.settings') }}">
                    <i class="feather-settings"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.yt_campaigns') }}</span>
                <h2 class="admin-panel__title">
                    @if($videos->total() > 0)
                        {{ $videos->firstItem() }}-{{ $videos->lastItem() }} / {{ $videos->total() }}
                    @else
                        0
                    @endif
                </h2>
            </div>
        </div>

        <div class="admin-panel__body p-0">
            @if(session('success'))
                <div class="alert alert-success m-4">{{ session('success') }}</div>
            @endif

            <div class="admin-table-wrap">
                <table class="table table-hover align-middle admin-table admin-table-cardify">
                    <thead>
                        <tr>
                            <th class="wd-10">ID</th>
                            <th>{{ __('messages.User') }}</th>
                            <th>{{ __('messages.yt_video') }}</th>
                            <th>{{ __('messages.yt_duration') }}</th>
                            <th>{{ __('messages.yt_reward') }}</th>
                            <th>{{ __('messages.yt_budget') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.created_at') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($videos as $video)
                            <tr>
                                <td data-label="ID">#{{ $video->id }}</td>
                                <td data-label="{{ __('messages.User') }}">
                                    @if($video->user)
                                        <a href="{{ route('admin.users.edit', $video->user_id) }}" class="admin-person">
                                            <span class="admin-person__avatar">
                                                <img src="{{ $video->user->img ? asset($video->user->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="{{ $video->user->username }}">
                                            </span>
                                            <span class="admin-person__body">
                                                <span class="admin-person__name">{{ $video->user->username }}</span>
                                            </span>
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('messages.deleted_user') }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.yt_video') }}">
                                    <a href="https://youtube.com/watch?v={{ $video->youtube_id }}" target="_blank" class="fw-bold d-flex align-items-center gap-2" style="color: #ef4444; text-decoration: none;">
                                        <i class="fa-brands fa-youtube"></i> {{ $video->youtube_id }}
                                    </a>
                                </td>
                                <td data-label="{{ __('messages.yt_duration') }}">{{ $video->duration_required }}s</td>
                                <td data-label="{{ __('messages.yt_reward') }}"><strong>{{ $video->reward_points }}</strong></td>
                                <td data-label="{{ __('messages.yt_budget') }}">
                                    <div class="progress" style="height: 6px; width: 80px; margin-bottom: 4px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $video->total_budget > 0 ? ($video->remaining_budget / $video->total_budget) * 100 : 0 }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $video->remaining_budget }} / {{ $video->total_budget }}</small>
                                </td>
                                <td data-label="{{ __('messages.status') }}">
                                    <form action="{{ route('admin.youtube.update', $video->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 110px; border-radius: 8px;">
                                            <option value="active" {{ $video->status == 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                            <option value="paused" {{ $video->status == 'paused' ? 'selected' : '' }}>{{ __('messages.paused') }}</option>
                                            <option value="completed" {{ $video->status == 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                                            <option value="pending" {{ $video->status == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                                        </select>
                                    </form>
                                </td>
                                <td data-label="{{ __('messages.created_at') }}"><span class="text-muted">{{ $video->created_at->format('Y-m-d H:i') }}</span></td>
                                <td data-label="{{ __('messages.actions') }}" class="text-end">
                                    <form action="{{ route('admin.youtube.destroy', $video->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.are_you_sure_delete_campaign') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light admin-icon-btn text-danger" title="{{ __('messages.delete') }}">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="admin-empty-state">
                                        <span class="admin-avatar-circle"><i class="fa-brands fa-youtube"></i></span>
                                        <h4 class="mb-0">{{ __('messages.no_campaigns_found') }}</h4>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($videos->hasPages())
        <div class="admin-panel__footer">
            <span class="admin-muted">{{ __('messages.total') }}: {{ $videos->total() }}</span>
            {{ $videos->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </section>
</div>
@endsection
