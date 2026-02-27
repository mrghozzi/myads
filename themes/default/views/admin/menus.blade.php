@extends('theme::layouts.admin')

@section('title', __('messages.menu'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.menu') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.menu') }}</li>
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
    <!-- Add Menu Form -->
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.new_menu') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.menus.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-5">
                        <label for="name" class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" class="form-control" id="name" name="name" required autocomplete="off">
                    </div>
                    <div class="col-md-5">
                        <label for="dir" class="form-label">{{ __('messages.url') }}</label>
                        <input type="text" class="form-control" id="dir" name="dir" required autocomplete="off">
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

    <!-- Menus List -->
    <div class="col-lg-12 mt-4">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.navigation_menu_list') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 80px;">#ID</th>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.url') }}</th>
                                <th class="text-end" style="width: 200px;">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $menu)
                                <tr>
                                    <td><span class="fw-semibold">#{{ $menu->id_m }}</span></td>
                                    <td>
                                        <form action="{{ route('admin.menus.update', $menu->id_m) }}" method="POST" id="edit-form-{{ $menu->id_m }}" class="d-flex align-items-center">
                                            @csrf
                                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $menu->name }}" required>
                                    </td>
                                    <td>
                                            <input type="text" name="dir" class="form-control form-control-sm" value="{{ $menu->dir }}" required>
                                    </td>
                                    <td class="text-end">
                                            <button type="submit" class="btn btn-sm btn-success hint--top" aria-label="{{ __('messages.save') }}">
                                                <i class="feather-save"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.menus.delete', $menu->id_m) }}" method="POST" class="d-inline-block ms-1" onsubmit="return confirm('{{ __('messages.confirm_delete_menu') }}');">
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
                                    <td colspan="4" class="text-center text-muted">{{ __('messages.no_menus_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($menus->hasPages())
                    <div class="mt-4">
                        {{ $menus->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
