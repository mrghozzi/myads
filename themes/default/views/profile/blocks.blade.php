@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.blocked_users') ?? 'Blocked Users' }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title">{{ __('messages.blocked_users') ?? 'Blocked Users' }}</p>
            <div class="widget-box-content">
                @if(session('success'))
                    <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" role="alert" style="margin-bottom: 20px;">{{ session('error') }}</div>
                @endif

                @if($blocks->isEmpty())
                    <p class="text-center" style="padding: 20px;">{{ __('messages.no_blocked_users') ?? 'You have not blocked any users.' }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.user') ?? 'User' }}</th>
                                    <th>{{ __('messages.block_type') ?? 'Type' }}</th>
                                    <th>{{ __('messages.block_expires') ?? 'Expires At' }}</th>
                                    <th>{{ __('messages.actions') ?? 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($blocks as $block)
                                    <tr>
                                        <td>
                                            @if($block->blockedUser)
                                                <a href="{{ route('profile.show', $block->blockedUser->username) }}" target="_blank">
                                                    {{ $block->blockedUser->username }}
                                                </a>
                                            @else
                                                <span class="text-muted">{{ __('messages.deleted_user') ?? 'Deleted User' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($block->block_type === 'messages_only')
                                                <span class="badge bg-secondary">{{ __('messages.block_messages_only') ?? 'Messages Only' }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('messages.block_full_platform') ?? 'Full Platform' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($block->expires_at)
                                                {{ $block->expires_at->diffForHumans() }} ({{ $block->expires_at->format('Y-m-d') }})
                                            @else
                                                {{ __('messages.forever') ?? 'Forever' }}
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('profile.block.destroy', $block->blocked_user_id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="button small secondary" onclick="return confirm('{{ __('messages.confirm_unblock') ?? 'Are you sure you want to unblock this user?' }}')">
                                                    {{ __('messages.unblock') ?? 'Unblock' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
