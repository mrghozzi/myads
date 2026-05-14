@extends('admin::layouts.admin')

@section('title', __('messages.manage_comments'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.manage_comments') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.comment_log') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.all_comments') }}</p>

            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.total') }}</span>
                    <span class="admin-stat-value">{{ number_format($comments->total()) }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.comments') }}</span>
                <h2 class="admin-panel__title">
                    @if($comments->total() > 0)
                        {{ $comments->firstItem() }}-{{ $comments->lastItem() }} / {{ $comments->total() }}
                    @else
                        0
                    @endif
                </h2>
            </div>
        </div>

        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle admin-table admin-table-cardify">
                    <thead>
                        <tr>
                            <th>{{ __('messages.author') }}</th>
                            <th>{{ __('messages.comment_text') }}</th>
                            <th>{{ __('messages.post_link') }}</th>
                            <th>{{ __('messages.comment_date') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comments as $comment)
                            <tr>
                                <td data-label="{{ __('messages.author') }}">
                                    @if($comment->user)
                                        <a href="{{ route('profile.show', $comment->user->username) }}" target="_blank" class="admin-person">
                                            <span class="admin-person__avatar">
                                                <img src="{{ $comment->user->avatarUrl() }}" alt="{{ $comment->user->username }}">
                                            </span>
                                            <span class="admin-person__body">
                                                <span class="admin-person__name">{{ $comment->user->username }}</span>
                                                <span class="admin-person__meta">#{{ $comment->user->id }}</span>
                                            </span>
                                        </a>
                                    @else
                                        <span class="admin-muted">{{ __('messages.deleted_user') }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.comment_text') }}">
                                    <div class="text-wrap" style="max-width: 300px; font-size: 0.9rem;">
                                        {{ \Illuminate\Support\Str::limit($comment->txt, 100) }}
                                    </div>
                                </td>
                                <td data-label="{{ __('messages.post_link') }}">
                                    @if($comment->topic)
                                        <a href="{{ route('forum.topic', $comment->topic->id) }}" target="_blank" class="btn btn-sm btn-light">
                                            <i class="feather-external-link me-1"></i>
                                            {{ \Illuminate\Support\Str::limit($comment->topic->title, 30) }}
                                        </a>
                                    @else
                                        <span class="text-danger">{{ __('messages.deleted_post') ?? 'Deleted Post' }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.comment_date') }}">
                                    <span class="admin-muted small">
                                        {{ \Carbon\Carbon::createFromTimestamp($comment->date)->format('Y-m-d H:i') }}
                                        <br>
                                        ({{ \Carbon\Carbon::createFromTimestamp($comment->date)->diffForHumans() }})
                                    </span>
                                </td>
                                <td data-label="{{ __('messages.actions') }}" class="text-end">
                                    <div class="admin-action-cluster">
                                        @if($comment->topic)
                                            <a href="{{ route('forum.topic', $comment->topic->id) }}" target="_blank" class="btn btn-sm btn-light admin-icon-btn" title="{{ __('messages.view_post') }}">
                                                <i class="feather-eye text-primary"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.comments.delete', $comment->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light admin-icon-btn" title="{{ __('messages.delete') }}">
                                                <i class="feather-trash-2 text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="admin-empty-state">
                                        <span class="admin-avatar-circle"><i class="feather-message-square"></i></span>
                                        <h4 class="mb-0">{{ __('messages.no_results') }}</h4>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($comments->hasPages())
            <div class="admin-panel__footer">
                {{ $comments->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </section>
</div>
@endsection
