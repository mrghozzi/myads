@extends('theme::layouts.master')

@section('content')
    <!-- SECTION BANNER -->
    <div class="section-banner" style="background: url({{ asset('themes/default/assets/img/banner/home_banner.png') }}) no-repeat 50%;" >
      <img class="section-banner-icon" src="{{ asset('themes/default/assets/img/banner/home_icon.png') }}"  alt="overview-icon">
      <p class="section-banner-title">{{ __('messages.board') }}</p>
      <p class="section-banner-text"></p>
    </div>

    <div class="grid">
        @if(session('errMSG'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>{{ __('messages.warning') }}</strong> {{ session('errMSG') }}
            </div>
        @endif
        @if(session('MSG'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('MSG') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif
    </div>

    <div class="grid">
      <div class="grid grid-3-3-3-3 centered home-dashboard-stats">
        <!-- Banner Ads Box -->
        <div class="stats-box small home-stat-card" style="--home-stat-bg: url({{ asset('themes/default/assets/img/graph/stat/01.jpg') }}); background: var(--home-stat-bg) no-repeat center; background-size: cover;">
          <div class="stats-box-value-wrap">
            <p class="stats-box-value">{{ $bannerStats['vu'] }}</p>
            <div class="stats-box-diff">
              <div>
                <svg class="icon-status" style="fill: #41efff;"><use xlink:href="#svg-status"></use></svg>
              </div>
                <p class="stats-box-diff-value">&nbsp;{{ __('messages.Views') }}</p>
            </div>
          </div>
          <p class="stats-box-title">{{ __('messages.bannads') }}</p>
          <a class="stats-box-text" href="#Views">{{ __('messages.MoreInfo') }}</a>
        </div>

        <!-- Text Ads Box -->
        <div class="stats-box small home-stat-card" style="--home-stat-bg: url({{ asset('themes/default/assets/img/graph/stat/02.jpg') }}); background: var(--home-stat-bg) no-repeat center; background-size: cover;">
          <div class="stats-box-value-wrap">
            <p class="stats-box-value">{{ $linkStats['clik'] }}</p>
            <div class="stats-box-diff">
              <div>
                 <svg class="icon-events-weekly"  style="fill: #41efff;"><use xlink:href="#svg-events-weekly"></use></svg>
              </div>
              <p class="stats-box-diff-value">&nbsp;{{ __('messages.Click') }}</p>
            </div>
          </div>
          <p class="stats-box-title">{{ __('messages.textads') }}</p>
          <a class="stats-box-text" href="#link">{{ __('messages.MoreInfo') }}</a>
        </div>

        <!-- Visits Box -->
        <div class="stats-box small home-stat-card" style="--home-stat-bg: url({{ asset('themes/default/assets/img/graph/stat/03.jpg') }}); background: var(--home-stat-bg) no-repeat center; background-size: cover;">
          <div class="stats-box-value-wrap">
            <p class="stats-box-value">{{ $visitStats['vu'] }}</p>
            <div class="stats-box-diff">
              <div>
                <svg class="icon-timeline"  style="fill: #41efff;"><use xlink:href="#svg-timeline"></use></svg>
              </div>
              <p class="stats-box-diff-value">&nbsp;{{ __('messages.visits') }}</p>
            </div>
          </div>
          <p class="stats-box-title">{{ __('messages.exvisit') }}</p>
          <a class="stats-box-text" href="#Exchange">{{ __('messages.MoreInfo') }}</a>
        </div>

        <!-- Points Box -->
        <div class="stats-box small home-stat-card" style="--home-stat-bg: url({{ asset('themes/default/assets/img/graph/stat/04.jpg') }}); background: var(--home-stat-bg) no-repeat center; background-size: cover;">
          <div class="stats-box-value-wrap">
            <p class="stats-box-value">{{ $user->pts }}</p>
            <div class="stats-box-diff">
              <div>
                   <svg class="icon-item"  style="fill: #41efff;"><use xlink:href="#svg-item"></use></svg>
              </div>
              <p class="stats-box-diff-value">&nbsp;PTS</p>
            </div>
          </div>
          <p class="stats-box-title">{{ __('messages.pts') }}</p>
          <a class="stats-box-text" href="#pts">{{ __('messages.MoreInfo') }}</a>
        </div>
      </div>
      
      {!! ads_site(2) !!}

      <!-- Banner Decoration -->
      <div class="stats-decoration v2 big secondary" id="Views" style="background: url({{ asset('themes/default/assets/img/graph/stat/05-big.png') }}) repeat-x bottom;">
        <p class="stats-decoration-title">{{ __('messages.bannads') }}</p>
        <p class="stats-decoration-subtitle">
            {{ __('messages.you_have') }}&nbsp;{{ $user->nvu }}&nbsp;{{ __('messages.ptvyba') }}&nbsp;
            {{ __('messages.your') }}&nbsp;{{ $bannerStats['vu'] }}&nbsp;{{ __('messages.bahbpb') }}&nbsp;
            {{ __('messages.And') }}&nbsp;{{ $bannerStats['clik'] }}&nbsp;{{ __('messages.Clik_ads') }}
        </p>
        <p class="stats-decoration-text">
            @if(isset($site_settings->e_links) && $site_settings->e_links == 1)
              <a href="https://github.com/mrghozzi/myads/wiki/Banners Ads" class="button primary padded" target="_blank" >&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>&nbsp;
            @endif
            <a class="button tertiary padded" href="{{ url('/state?ty=banner&st=vu') }}" >&nbsp;<i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;</a>&nbsp;
            <a href="{{ url('/b_list') }}" class="button secondary padded" >{{ __('messages.list') }}&nbsp;{{ __('messages.bannads') }}</a>&nbsp;
            <a class="button padded" href="{{ url('/b_code') }}" >&nbsp;<i class="fa fa-code" aria-hidden="true"></i>&nbsp;</a>
        </p>
      </div>

      <!-- Text Ads Decoration -->
      <div class="stats-decoration v2 big secondary" id="link" style="background: url({{ asset('themes/default/assets/img/graph/stat/06-big.png') }}) repeat-x bottom;">
        <p class="stats-decoration-title">{{ __('messages.textads') }}</p>
        <p class="stats-decoration-subtitle">
            {{ __('messages.you_have') }}&nbsp;{{ $user->nlink }}&nbsp;{{ __('messages.ptcyta') }}&nbsp;
            {{ __('messages.your') }}&nbsp;{{ $linkStats['clik'] }}&nbsp;{{ __('messages.Clik_ads') }}
        </p>
        <p class="stats-decoration-text">
            @if(isset($site_settings->e_links) && $site_settings->e_links == 1)
              <a href="https://github.com/mrghozzi/myads/wiki/Text Ads" class="button primary padded" target="_blank" >&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>&nbsp;
            @endif
            <a class="button tertiary padded" href="{{ url('/state?ty=link&st=vu') }}" >&nbsp;<i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;</a>&nbsp;
            <a href="{{ url('/l_list') }}" class="button secondary padded" >{{ __('messages.list') }}&nbsp;{{ __('messages.textads') }}</a>&nbsp;
            <a class="button padded" href="{{ url('/l_code') }}" >&nbsp;<i class="fa fa-code" aria-hidden="true"></i>&nbsp;</a>
        </p>
      </div>

      <!-- Visits Decoration -->
      <div class="stats-decoration v2 big secondary" id="Exchange" style="background: url({{ asset('themes/default/assets/img/graph/stat/07.png') }}) repeat-x bottom;">
        <p class="stats-decoration-title">{{ __('messages.exvisit') }}</p>
        <p class="stats-decoration-subtitle">
            {{ __('messages.you_have') }}&nbsp;{{ $user->vu }}&nbsp;{{ __('messages.ptvysa') }}&nbsp;
            {{ __('messages.yshbv') }}&nbsp;:&nbsp;{{ $visitStats['vu'] }}
        </p>
        <p class="stats-decoration-text">
            @if(isset($site_settings->e_links) && $site_settings->e_links == 1)
              <a href="https://github.com/mrghozzi/myads/wiki/Exchange" class="button primary padded" target="_blank" >&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>&nbsp;
            @endif
            <a href="{{ url('/v_list') }}" class="button secondary padded" >{{ __('messages.list') }}&nbsp;{{ __('messages.exvisit') }}</a>&nbsp;
            <a class="button padded" href="javascript:void(0);" onclick="window.open('{{ url('/visits?id=' . $user->id) }}');">
              <i class="fa fa-exchange nav_icon"></i>&nbsp;{{ __('messages.exvisit') }}
            </a>
        </p>
      </div>

      <!-- Points Box -->
      {!! ads_site(2) !!}
      <div class="widget-box" id="pts" style="background: url({{ asset('themes/default/assets/img/ad_pattern.png') }}) repeat;">
        <p class="widget-box-title">{{ __('messages.Totalpoints') }} {{ $user->pts }} PTS.</p>
        <div class="widget-box-content">
            <p class="switch-option-title">
                @if(isset($site_settings->e_links) && $site_settings->e_links == 1)
                <a href="https://github.com/mrghozzi/myads/wiki/pts" class="button primary padded" target="_blank" >&nbsp;<b><i class="fa fa-question-circle" aria-hidden="true"></i></b></a>
                @endif
                <a class="button secondary padded" href="{{ url('/referral') }}" ><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ __('messages.list') }}&nbsp;{{ __('messages.referal') }}</a>
                <a class="button padded" href="{{ url('/r_code') }}" ><i class="fa fa-users"></i>&nbsp;{{ __('messages.referal') }}</a>
            </p>
            <hr />
            <p class="switch-option-title"><b>{{ __('messages.Convertpoint') }}</b></p>
            <br />
            <div class="switch-option">
                <form action="{{ url('/home') }}" method="POST">
                    @csrf
                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-input social-input small active">
                                <div class="social-link no-hover twitch">
                                    <svg class="icon-twitch"><use xlink:href="#svg-item"></use></svg>
                                </div>
                                <label for="social-account-twitch">{{ __('messages.Points') }}</label>
                                <input type="text" id="Points" name="pts" >
                            </div>
                        </div>
                        <div class="form-select">
                            <label for="profile-social-stream-schedule-monday">{{ __('messages.to') }}</label>
                            <select id="profile-social-stream-schedule-monday" name="to">
                                <option value="link" >{{ __('messages.tostads') }}</option>
                                <option value="banners"  >{{ __('messages.towthbaner') }}</option>
                                <option value="exchv" >{{ __('messages.toexchvisi') }}</option>
                            </select>
                            <svg class="form-select-icon icon-small-arrow"><use xlink:href="#svg-small-arrow"></use></svg>
                        </div>
                    </div>
                    <div class="form-row split">
                        <button type="submit" class="button tertiary padded" name="bt_pts" value="bt_pts" >{{ __('messages.Conversion') }}</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
      {!! ads_site(2) !!}
      
    </div>
@endsection
