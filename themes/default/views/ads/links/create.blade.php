@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.add_link') }}</p>
</div>

<div class="grid grid-3-9">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <a href="{{ route('ads.index') }}" class="btn btn-primary" >{{ __('messages.back') }}</a>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <form action="{{ route('ads.links.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-6-6" style="gap: 18px;">
                        <div>
                            <div class="form-group">
                                <label>{{ __('messages.name') }} (Version A)</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.name') }} (Version B - Optional)</label>
                                <input type="text" name="name_b" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.url') }}</label>
                                <input type="url" name="url" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.desc') }} (Version A)</label>
                                <textarea name="txt" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.desc') }} (Version B - Optional)</label>
                                <textarea name="txt_b" class="form-control" rows="3"></textarea>
                                <small class="text-muted">A/B Testing: Provide a second title/description to automatically serve the best performing version.</small>
                            </div>
                        </div>
                        <div>
                            <div class="form-group">
                                <label>{{ __('messages.smart_form_target_countries') }}</label>
                                <input type="text" name="countries" class="form-control" placeholder="{{ __('messages.smart_form_countries_placeholder') }}">
                                <small class="text-muted">{{ __('messages.smart_form_target_countries_help') }}</small>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.smart_form_target_devices') }}</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 5px;">
                                    @foreach($deviceOptions as $value => $label)
                                        <label style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; font-size: 13px;">
                                            <input type="checkbox" name="devices[]" value="{{ $value }}">
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
