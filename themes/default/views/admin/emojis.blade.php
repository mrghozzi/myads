@extends('theme::layouts.admin')

@section('title', __('messages.emojis'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.emojis') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.emojis') }}</li>
        </ul>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <!-- Add Emoji Form -->
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.add_emoji') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.emojis.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-5">
                        <label for="name" class="form-label">{{ __('messages.emoji_shortcut') }}</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder=":smile:" required autocomplete="off">
                    </div>
                    <div class="col-md-5">
                        <label for="img" class="form-label">{{ __('messages.emoji_icon') }} (URL)</label>
                        <input type="text" class="form-control" id="img" name="img" placeholder="https://example.com/smile.png" required autocomplete="off">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="feather-plus me-2"></i> {{ __('messages.add') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Emojis List -->
    <div class="col-lg-12 mt-4">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.emojis') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 80px;">#ID</th>
                                <th>{{ __('messages.emoji_shortcut') }}</th>
                                <th>{{ __('messages.emoji_icon') }}</th>
                                <th class="text-end" style="width: 150px;">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($emojis as $emoji)
                                <tr>
                                    <td><span class="fw-semibold">#{{ $emoji->id }}</span></td>
                                    <td>{{ $emoji->name }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-text avatar-md bg-soft-primary text-primary">
                                                <img src="{{ $emoji->img }}" alt="{{ $emoji->name }}" style="width: 24px; height: 24px; object-fit: contain;">
                                            </div>
                                            <span class="text-muted small text-truncate" style="max-width: 250px;">{{ $emoji->img }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.emojis.delete', $emoji->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('{{ __('messages.confirm_delete_emoji') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger hint--top" aria-label="{{ __('messages.delete') }}">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        {{ __('messages.no_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($emojis->hasPages())
                    <div class="mt-4">
                        {{ $emojis->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
