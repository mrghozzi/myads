@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.edit_link') }}</p>
</div>

<div class="grid grid-3-9">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <a href="{{ route('ads.links.index') }}" class="btn btn-primary" >{{ __('messages.back') }}</a>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <form action="{{ route('ads.links.update', $link->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-6-6" style="gap: 18px;">
                        <div>
                            <div class="form-group">
                                <label>{{ __('messages.name') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ $link->name }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.url') }}</label>
                                <input type="url" name="url" class="form-control" value="{{ $link->url }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.desc') }}</label>
                                <textarea name="txt" class="form-control" rows="3" required>{{ $link->txt }}</textarea>
                            </div>
                        </div>
                        <div>
                            <div class="form-group">
                                <label>{{ __('messages.smart_form_target_countries') }}</label>
                                <input type="text" name="countries" class="form-control" value="{{ old('countries', $targetCountries) }}" placeholder="{{ __('messages.smart_form_countries_placeholder') }}">
                                <small class="text-muted">{{ __('messages.smart_form_target_countries_help') }}</small>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.smart_form_target_devices') }}</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 5px;">
                                    @foreach($deviceOptions as $value => $label)
                                        <label style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; font-size: 13px;">
                                            <input type="checkbox" name="devices[]" value="{{ $value }}" {{ in_array($value, old('devices', $selectedDevices), true) ? 'checked' : '' }}>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="button secondary">{{ __('messages.save') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
