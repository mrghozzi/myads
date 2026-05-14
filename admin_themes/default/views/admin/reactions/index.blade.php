@extends('admin::layouts.admin')

@section('title', __('messages.manage_reactions'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.manage_reactions') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.reaction_log') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.all_reactions') }}</p>

            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.total') }}</span>
                    <span class="admin-stat-value">{{ number_format($reactions->total()) }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.reactions') ?? 'Reactions' }}</span>
                <h2 class="admin-panel__title">
                    @if($reactions->total() > 0)
                        {{ $reactions->firstItem() }}-{{ $reactions->lastItem() }} / {{ $reactions->total() }}
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
                            <th>{{ __('messages.User') }}</th>
                            <th>{{ __('messages.reaction_emoji') }}</th>
                            <th>{{ __('messages.content_type') }}</th>
                            <th>{{ __('messages.post_link') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reactions as $reaction)
                            @php
                                $typeLabel = 'Unknown';
                                $typeIcon = 'feather-help-circle';
                                $targetUrl = '#';
                                
                                switch($reaction->type) {
                                    case 1:
                                        $typeLabel = __('messages.Followers');
                                        $typeIcon = 'feather-user-plus';
                                        $targetUser = \App\Models\User::find($reaction->sid);
                                        $targetUrl = $targetUser ? route('profile.show', $targetUser->username) : '#';
                                        break;
                                    case 2:
                                        $typeLabel = __('messages.forum');
                                        $typeIcon = 'feather-message-square';
                                        $targetUrl = route('forum.topic', $reaction->sid);
                                        break;
                                    case 22:
                                        $typeLabel = __('messages.directory');
                                        $typeIcon = 'feather-globe';
                                        $targetUrl = url('/dr' . $reaction->sid);
                                        break;
                                    case 3:
                                        $typeLabel = __('messages.products') ?? 'Store';
                                        $typeIcon = 'feather-shopping-bag';
                                        $product = \App\Models\Product::find($reaction->sid);
                                        $targetUrl = $product ? url('/store/' . $product->name) : '#';
                                        break;
                                    case 4:
                                        $typeLabel = __('messages.comment');
                                        $typeIcon = 'feather-message-circle';
                                        $comment = \App\Models\ForumComment::find($reaction->sid);
                                        $targetUrl = $comment ? route('forum.topic', $comment->tid) . '#comment_' . $reaction->sid : '#';
                                        break;
                                    case 44:
                                        $typeLabel = __('messages.directory_comment');
                                        $typeIcon = 'feather-message-circle';
                                        $comment = \App\Models\Option::find($reaction->sid);
                                        $targetUrl = $comment ? url('/dr' . $comment->o_parent) . '#comment_' . $reaction->sid : '#';
                                        break;
                                    case 444:
                                        $typeLabel = __('messages.store_comment') ?? 'Store Comment';
                                        $typeIcon = 'feather-message-circle';
                                        $comment = \App\Models\Option::find($reaction->sid);
                                        $product = $comment ? \App\Models\Product::find($comment->o_parent) : null;
                                        $targetUrl = $product ? url('/store/' . $product->name) . '#comment_' . $reaction->sid : '#';
                                        break;
                                    case 6:
                                        $typeLabel = __('messages.order_requests');
                                        $typeIcon = 'feather-briefcase';
                                        $targetUrl = url('/orders/' . $reaction->sid);
                                        break;
                                    case 66:
                                        $typeLabel = __('messages.order_comment') ?? 'Order Comment';
                                        $typeIcon = 'feather-message-circle';
                                        $comment = \App\Models\Option::find($reaction->sid);
                                        $targetUrl = $comment ? url('/orders/' . $comment->o_parent) . '#comment_' . $reaction->sid : '#';
                                        break;
                                }
                            @endphp
                            <tr>
                                <td data-label="{{ __('messages.User') }}">
                                    @if($reaction->user)
                                        <a href="{{ route('profile.show', $reaction->user->username) }}" target="_blank" class="admin-person">
                                            <span class="admin-person__avatar">
                                                <img src="{{ $reaction->user->avatarUrl() }}" alt="{{ $reaction->user->username }}">
                                            </span>
                                            <span class="admin-person__body">
                                                <span class="admin-person__name">{{ $reaction->user->username }}</span>
                                                <span class="admin-person__meta">#{{ $reaction->user->id }}</span>
                                            </span>
                                        </a>
                                    @else
                                        <span class="admin-muted">{{ __('messages.deleted_user') }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.reaction_emoji') }}">
                                    @if($reaction->emoji)
                                        <img src="{{ theme_asset('img/reaction/' . $reaction->emoji . '.png') }}" width="24" alt="{{ $reaction->emoji }}" title="{{ $reaction->emoji }}">
                                    @else
                                        <span class="badge bg-soft-primary text-primary"><i class="fa-solid fa-user-plus"></i></span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.content_type') }}">
                                    <span class="badge bg-soft-light text-muted">
                                        <i class="{{ $typeIcon }} me-1"></i>
                                        {{ $typeLabel }}
                                    </span>
                                </td>
                                <td data-label="{{ __('messages.post_link') }}">
                                    <a href="{{ $targetUrl }}" target="_blank" class="btn btn-sm btn-light">
                                        <i class="feather-external-link me-1"></i>
                                        {{ __('messages.view_post') }}
                                    </a>
                                </td>
                                <td data-label="{{ __('messages.date') }}">
                                    <span class="admin-muted small">
                                        {{ \Carbon\Carbon::createFromTimestamp($reaction->time_t)->format('Y-m-d H:i') }}
                                        <br>
                                        ({{ \Carbon\Carbon::createFromTimestamp($reaction->time_t)->diffForHumans() }})
                                    </span>
                                </td>
                                <td data-label="{{ __('messages.actions') }}" class="text-end">
                                    <div class="admin-action-cluster">
                                        <form action="{{ route('admin.reactions.delete', $reaction->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');" class="d-inline">
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
                                <td colspan="6">
                                    <div class="admin-empty-state">
                                        <span class="admin-avatar-circle"><i class="feather-heart"></i></span>
                                        <h4 class="mb-0">{{ __('messages.no_results') }}</h4>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($reactions->hasPages())
            <div class="admin-panel__footer">
                {{ $reactions->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </section>
</div>
@endsection
