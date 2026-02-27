@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.addwebsitdir') }}</p>
</div>

<div class="grid grid-3-9">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <a href="{{ url('/directory') }}" class="btn btn-primary" >{{ __('messages.back') }}</a>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @auth
                <form method="POST" action="{{ route('directory.store') }}">
                    @csrf
                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-input social-input small active">
                                <div class="social-link no-hover name">
                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                </div>
                                <label for="name">{{ __('messages.name') }}</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input social-input small active">
                                <div class="social-link no-hover url">
                                    <i class="fa fa-link" aria-hidden="true"></i>
                                </div>
                                <label for="url">{{ __('messages.url') }}</label>
                                <input type="text" id="url" name="url" value="{{ old('url') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small mid-textarea">
                                <label for="description">{{ __('messages.text_p') }}</label>
                                <textarea id="description" name="txt">{{ old('txt') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-select">
                                <label for="profile-status">{{ __('messages.cat') }}</label>
                                <select id="profile-status" name="categ">
                                    @foreach($mainCategories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @foreach($subCategories->get($cat->id, collect()) as $sub)
                                            <option value="{{ $sub->id }}">_{{ $sub->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                                <svg class="form-select-icon icon-small-arrow">
                                    <use xlink:href="#svg-small-arrow"></use>
                                </svg>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input social-input small active">
                                <div class="social-link no-hover tag">
                                    <i class="fa fa-tag" aria-hidden="true"></i>
                                </div>
                                <label for="tag">{{ __('messages.tag') }}</label>
                                <input type="text" id="tag" name="tag" value="{{ old('tag') }}">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="s_type" value="1" />
                    <button type="submit" class="button primary">{{ __('messages.spread') }}</button>
                </form>
                @else
                    <div class="alert alert-warning" role="alert">
                        <a href="{{ route('login') }}">{{ __('messages.login') }}</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
