@extends('theme::layouts.admin')

@section('title', __('messages.plugins'))

@section('content')
<!-- Header -->
<div class="row g-0 align-items-center border-bottom help-center-content-header mb-5 pb-5">
    <div class="col-lg-6 offset-lg-3 text-center">
        <h2 class="fw-bolder mb-2 text-dark">{{ __('messages.plugins') }}</h2>
        <p class="text-muted">{{ __('messages.plugins_desc') ?? 'Extend your platform functionality with plugins.' }}</p>
        <div class="mt-4">
             <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPluginModal">
                <i class="feather-upload me-2"></i> {{ __('messages.upload_plugin') }}
            </button>
        </div>
    </div>
</div>

<div class="main-content container-lg px-4">
    <div class="card">
        <div class="card-body">
            @if(empty($plugins))
                <div class="text-center py-5">
                    <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mb-3 mx-auto">
                        <i class="feather-box"></i>
                    </div>
                    <h4>{{ __('messages.no_plugins_found') }}</h4>
                    <p class="text-muted">{{ __('messages.no_plugins_desc') ?? 'You have not installed any plugins yet.' }}</p>
                    <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#uploadPluginModal">
                        {{ __('messages.upload_first_plugin') }}
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>{{ __('messages.plugin') }}</th>
                                <th>{{ __('messages.desc') }}</th>
                                <th>{{ __('messages.version') }}</th>
                                <th>{{ __('messages.author') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th class="text-end" style="min-width: 150px;">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plugins as $plugin)
                            <tr>
                                <td>
                                    @if($plugin['thumbnail'])
                                        <img src="{{ route('admin.plugins.thumbnail', $plugin['slug']) }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $plugin['name'] }}">
                                    @else
                                        <div class="avatar-text bg-soft-primary text-primary rounded">
                                            {{ strtoupper(substr($plugin['name'], 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $plugin['name'] }}</div>
                                    <small class="text-muted">{{ $plugin['slug'] }}</small>
                                    @if(isset($updates[$plugin['slug']]))
                                        <div class="mt-1">
                                            <span class="badge bg-soft-warning text-warning border border-warning">
                                                <i class="feather-arrow-up-circle me-1"></i>
                                                {{ __('messages.update_available') }}: {{ $updates[$plugin['slug']]['new_version'] }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted text-truncate d-inline-block" style="max-width: 300px;" title="{{ $plugin['description'] ?? '' }}">
                                        {{ $plugin['description'] ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $plugin['version'] ?? '1.0' }}</span>
                                </td>
                                <td>
                                    <span class="text-dark">{{ $plugin['author'] ?? __('messages.unknown') ?? 'Unknown' }}</span>
                                </td>
                                <td>
                                    @if($plugin['is_active'])
                                        <span class="badge bg-soft-success text-success">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge bg-soft-secondary text-secondary">{{ __('messages.inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if($plugin['is_active'])
                                            <form action="{{ route('admin.plugins.deactivate') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                                <button type="submit" class="btn btn-sm btn-soft-warning" title="{{ __('messages.deactivate') }}">
                                                    <i class="feather-pause"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.plugins.activate') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                                <button type="submit" class="btn btn-sm btn-soft-success" title="{{ __('messages.activate') }}">
                                                    <i class="feather-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <button type="button" class="btn btn-sm btn-soft-danger" data-bs-toggle="modal" data-bs-target="#deletePluginModal{{ $loop->index }}" title="{{ __('messages.delete') }}">
                                            <i class="feather-trash-2"></i>
                                        </button>

                                        @if(isset($updates[$plugin['slug']]))
                                            @if(isset($updates[$plugin['slug']]['changelog']))
                                                <button type="button" class="btn btn-sm btn-soft-primary" data-bs-toggle="modal" data-bs-target="#changelogModal{{ $loop->index }}" title="{{ __('messages.view_changelog') }}">
                                                    <i class="feather-info"></i>
                                                </button>
                                            @endif
                                            <form action="{{ route('admin.plugins.upgrade') }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_upgrade_plugin') ?? 'Are you sure you want to upgrade this plugin?' }}')">
                                                @csrf
                                                <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                                <button type="submit" class="btn btn-sm btn-soft-info" title="{{ __('messages.update_now') }}">
                                                    <i class="feather-download-cloud"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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
@endsection

@section('modals')
<!-- Upload Plugin Modal -->
<div class="modal fade" id="uploadPluginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.upload_plugin') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.plugins.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <i class="feather-info me-2"></i> {{ __('messages.upload_plugin_info') ?? 'Upload a .zip file containing the plugin. Ensure the zip structure is correct.' }}
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.plugin_zip_file') }}</label>
                        <input type="file" name="plugin_zip" class="form-control" accept=".zip" required>
                        <div class="form-text">{{ __('messages.allowed_file_types') }}: .zip</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.install_now') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!empty($plugins))
    @foreach($plugins as $plugin)
    <!-- Delete Plugin Modal -->
    <div class="modal fade" id="deletePluginModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.delete_plugin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                        <i class="feather-trash-2"></i>
                    </div>
                    <h4>{{ __('messages.confirm_delete_plugin') }}</h4>
                    <p class="text-muted">{{ $plugin['name'] }} ({{ $plugin['slug'] }})</p>
                    <div class="alert alert-warning mt-3">
                        <i class="feather-alert-triangle me-2"></i>
                        {{ __('messages.delete_plugin_warning') ?? 'This action cannot be undone. All plugin files and data will be removed.' }}
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <form action="{{ route('admin.plugins.delete') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                        <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($updates[$plugin['slug']]) && isset($updates[$plugin['slug']]['changelog']))
    <!-- Changelog Modal -->
    <div class="modal fade" id="changelogModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.changelog') ?? 'Changelog' }} - {{ $plugin['name'] }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="bg-light p-4 rounded border">
                        <pre style="white-space: pre-wrap; font-family: inherit; margin-bottom: 0;">{{ $updates[$plugin['slug']]['changelog'] }}</pre>
                    </div>
                    @if(isset($updates[$plugin['slug']]['github_url']))
                        <div class="mt-3 text-center">
                            <a href="{{ $updates[$plugin['slug']]['github_url'] }}" target="_blank" class="btn btn-sm btn-link">
                                <i class="feather-github me-1"></i> {{ __('messages.view_on_github') ?? 'View on GitHub' }}
                            </a>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    <form action="{{ route('admin.plugins.upgrade') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                        <button type="submit" class="btn btn-primary">{{ __('messages.update_now') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
@endif

@endsection
