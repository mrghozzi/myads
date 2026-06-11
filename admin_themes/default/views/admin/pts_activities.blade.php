@extends('admin::layouts.admin')

@section('title', __('messages.pts_activities'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.pts_activities') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.pts_activities') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.pts_vouchers') }} / {{ __('messages.pts_transfers') }}</p>

            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.pts_vouchers') }}</span>
                    <span class="admin-stat-value">{{ number_format($vouchers->total()) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.pts_transfers') }}</span>
                    <span class="admin-stat-value">{{ number_format($transfers->total()) }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Vouchers Panel -->
    <section class="admin-panel mb-5">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.pts_activities') }}</span>
                <h2 class="admin-panel__title">{{ __('messages.pts_vouchers') }}</h2>
            </div>
        </div>

        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle admin-table admin-table-cardify">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.generator') }}</th>
                            <th>{{ __('messages.code') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.claimer') }}</th>
                            <th>{{ __('messages.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $voucher)
                            <tr>
                                <td data-label="#">{{ $voucher->id }}</td>
                                <td data-label="{{ __('messages.generator') }}">
                                    @if($voucher->generator)
                                        <a href="{{ route('profile.show', $voucher->generator->username) }}" target="_blank" class="admin-person">
                                            <span class="admin-person__avatar">
                                                <img src="{{ $voucher->generator->img ? asset($voucher->generator->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="{{ $voucher->generator->username }}">
                                            </span>
                                            <span class="admin-person__body">
                                                <span class="admin-person__name">{{ $voucher->generator->username }}</span>
                                            </span>
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.code') }}"><code>{{ $voucher->code }}</code></td>
                                <td data-label="{{ __('messages.amount') }}"><strong>{{ number_format($voucher->amount, 2) }} PTS</strong></td>
                                <td data-label="{{ __('messages.status') }}">
                                    @if($voucher->is_used)
                                        <span class="badge bg-soft-danger text-danger">{{ __('messages.used') }}</span>
                                    @else
                                        <span class="badge bg-soft-success text-success">{{ __('messages.unused') }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.claimer') }}">
                                    @if($voucher->claimer)
                                        <a href="{{ route('profile.show', $voucher->claimer->username) }}" target="_blank" class="admin-person">
                                            <span class="admin-person__avatar">
                                                <img src="{{ $voucher->claimer->img ? asset($voucher->claimer->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="{{ $voucher->claimer->username }}">
                                            </span>
                                            <span class="admin-person__body">
                                                <span class="admin-person__name">{{ $voucher->claimer->username }}</span>
                                            </span>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.date') }}">
                                    <div class="admin-inline-meta">
                                        <span>{{ $voucher->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="admin-empty-state">
                                        <span class="admin-avatar-circle"><i class="feather-credit-card"></i></span>
                                        <h4 class="mb-0">{{ __('messages.no_records') }}</h4>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-panel__footer">
            <span class="admin-muted">{{ __('messages.pts_vouchers') }}: {{ $vouchers->total() }}</span>
            {{ $vouchers->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
    </section>

    <!-- Transfers Panel -->
    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.pts_activities') }}</span>
                <h2 class="admin-panel__title">{{ __('messages.pts_transfers') }}</h2>
            </div>
        </div>

        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle admin-table admin-table-cardify">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.sender') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.recipient') }}</th>
                            <th>{{ __('messages.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                            <tr>
                                <td data-label="#">{{ $transfer->id }}</td>
                                <td data-label="{{ __('messages.sender') }}">
                                    @if($transfer->user)
                                        <a href="{{ route('profile.show', $transfer->user->username) }}" target="_blank" class="admin-person">
                                            <span class="admin-person__avatar">
                                                <img src="{{ $transfer->user->img ? asset($transfer->user->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="{{ $transfer->user->username }}">
                                            </span>
                                            <span class="admin-person__body">
                                                <span class="admin-person__name">{{ $transfer->user->username }}</span>
                                            </span>
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.amount') }}"><strong>{{ number_format(abs($transfer->amount), 2) }} PTS</strong></td>
                                <td data-label="{{ __('messages.recipient') }}">
                                    @if(isset($transfer->meta['recipient_username']))
                                        <a href="{{ route('profile.show', $transfer->meta['recipient_username']) }}" target="_blank" class="admin-person">
                                            <span class="admin-person__avatar">
                                                <img src="{{ asset('themes/default/assets/images/avatar/1.png') }}" alt="{{ $transfer->meta['recipient_username'] }}">
                                            </span>
                                            <span class="admin-person__body">
                                                <span class="admin-person__name">{{ $transfer->meta['recipient_username'] }}</span>
                                            </span>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.date') }}">
                                    <div class="admin-inline-meta">
                                        <span>{{ $transfer->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="admin-empty-state">
                                        <span class="admin-avatar-circle"><i class="feather-repeat"></i></span>
                                        <h4 class="mb-0">{{ __('messages.no_records') }}</h4>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-panel__footer">
            <span class="admin-muted">{{ __('messages.pts_transfers') }}: {{ $transfers->total() }}</span>
            {{ $transfers->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
    </section>
</div>
@endsection
