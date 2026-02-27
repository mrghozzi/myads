@extends('theme::layouts.admin')

@section('title', __('messages.cookie_notice_settings'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.cookie_notice_settings') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.settings') }}">{{ __('messages.settings_site') }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.cookie_notice_settings') }}</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">{{ __('messages.cookie_notice_settings') }}</h5>
            </div>
            <div class="card-body">
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

                <form action="{{ route('admin.cookie_notice.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <div class="form-check form-switch px-0 d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="enabled" name="enabled" value="1" {{ $cookieSettings['enabled'] == '1' ? 'checked' : '' }} style="width: 40px; height: 20px;">
                            <label class="form-check-label mb-0" for="enabled" style="font-weight: 500;">
                                {{ __('messages.cookie_enable_notice') }}
                            </label>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-12 mb-4 mb-lg-0">
                            <label class="form-label">{{ __('messages.cookie_position') }} <span class="text-danger">*</span></label>
                            <select name="position" class="form-select">
                                <option value="bottom" {{ $cookieSettings['position'] == 'bottom' ? 'selected' : '' }}>{{ __('messages.cookie_pos_bottom') }}</option>
                                <option value="top" {{ $cookieSettings['position'] == 'top' ? 'selected' : '' }}>{{ __('messages.cookie_pos_top') }}</option>
                                <option value="bottom_left" {{ $cookieSettings['position'] == 'bottom_left' ? 'selected' : '' }}>{{ __('messages.cookie_pos_bottom_left') }}</option>
                                <option value="bottom_right" {{ $cookieSettings['position'] == 'bottom_right' ? 'selected' : '' }}>{{ __('messages.cookie_pos_bottom_right') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <label class="form-label">{{ __('messages.cookie_bg_color') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="bg_color" value="{{ old('bg_color', $cookieSettings['bg_color']) }}" title="Choose your color" style="max-width: 60px;">
                                <input type="text" class="form-control" name="bg_color_text" value="{{ old('bg_color', $cookieSettings['bg_color']) }}" onchange="document.getElementsByName('bg_color')[0].value = this.value">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">{{ __('messages.cookie_text_color') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="text_color" value="{{ old('text_color', $cookieSettings['text_color']) }}" title="Choose your color" style="max-width: 60px;">
                                <input type="text" class="form-control" name="text_color_text" value="{{ old('text_color', $cookieSettings['text_color']) }}" onchange="document.getElementsByName('text_color')[0].value = this.value">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <label class="form-label">{{ __('messages.cookie_btn_bg') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="btn_bg" value="{{ old('btn_bg', $cookieSettings['btn_bg']) }}" title="Choose your color" style="max-width: 60px;">
                                <input type="text" class="form-control" name="btn_bg_text" value="{{ old('btn_bg', $cookieSettings['btn_bg']) }}" onchange="document.getElementsByName('btn_bg')[0].value = this.value">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">{{ __('messages.cookie_btn_text') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="btn_text" value="{{ old('btn_text', $cookieSettings['btn_text']) }}" title="Choose your color" style="max-width: 60px;">
                                <input type="text" class="form-control" name="btn_text_text" value="{{ old('btn_text', $cookieSettings['btn_text']) }}" onchange="document.getElementsByName('btn_text')[0].value = this.value">
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        // Sync color pickers with text inputs
                        document.querySelectorAll('input[type="color"]').forEach(function(el) {
                            el.addEventListener('input', function() {
                                document.getElementsByName(this.name + '_text')[0].value = this.value;
                            });
                        });
                    </script>

                    <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') ?? 'Save' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
