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
      <div class="home-dashboard-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
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

        <div class="stats-box small home-stat-card home-stat-card--smart" style="--home-stat-bg: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #0ea5e9 100%); background: var(--home-stat-bg) no-repeat center; background-size: cover;">
          <div class="stats-box-value-wrap">
            <p class="stats-box-value">{{ $smartAdStats['impressions'] }}</p>
            <div class="stats-box-diff">
              <div>
                <svg class="icon-timeline" style="fill: #93c5fd;"><use xlink:href="#svg-timeline"></use></svg>
              </div>
              <p class="stats-box-diff-value">&nbsp;{{ __('messages.smart_impressions') }}</p>
            </div>
          </div>
          <p class="stats-box-title">{{ __('messages.smart_ads') }}</p>
          <a class="stats-box-text" href="#smart-ads">{{ __('messages.MoreInfo') }}</a>
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
            <a class="button tertiary padded" href="{{ url('/state?ty=link&st=vu') }}" >&nbsp;<i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;</a>&nbsp;
            <a href="{{ url('/l_list') }}" class="button secondary padded" >{{ __('messages.list') }}&nbsp;{{ __('messages.textads') }}</a>&nbsp;
            <a class="button padded" href="{{ url('/l_code') }}" >&nbsp;<i class="fa fa-code" aria-hidden="true"></i>&nbsp;</a>
        </p>
      </div>

      <div id="smart-ads" style="margin-top: 24px; position: relative; overflow: hidden; border-radius: 24px; padding: 28px; background: linear-gradient(135deg, rgba(15,23,42,0.98) 0%, rgba(30,64,175,0.96) 52%, rgba(14,165,233,0.92) 100%); box-shadow: 0 24px 50px rgba(37, 99, 235, 0.2); color: #fff;">
        <div style="position: absolute; inset: auto -70px -70px auto; width: 220px; height: 220px; border-radius: 50%; background: rgba(255,255,255,0.08);"></div>
        <div style="position: absolute; inset: -80px auto auto -60px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,0.08);"></div>
        <div style="position: relative; z-index: 1;">
          <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
            <div style="max-width: 700px;">
              <span style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px; background: rgba(255,255,255,0.12); font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;">{{ __('messages.smart_ads') }}</span>
              <h2 style="margin: 16px 0 10px; font-size: 32px; line-height: 1.15; color: #fff;">{{ __('messages.smart_ads_campaign_pitch') }}</h2>
              <p style="margin: 0; max-width: 620px; color: rgba(255,255,255,0.86); font-size: 15px; line-height: 1.8;">{{ __('messages.smart_targeting_intro') }}</p>
            </div>
            <div style="display: grid; gap: 12px; min-width: 260px;">
              <div style="padding: 16px 18px; border-radius: 18px; background: rgba(255,255,255,0.12); backdrop-filter: blur(8px);">
                <p style="margin: 0 0 6px; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: rgba(255,255,255,0.72);">{{ __('messages.smart_admin_balance') }}</p>
                <p style="margin: 0; font-size: 28px; font-weight: 800; color: #fff;">{{ number_format((float) $user->nsmart, 2) }}</p>
                <p style="margin: 6px 0 0; font-size: 13px; color: rgba(255,255,255,0.82);">{{ __('messages.smart_ads_credits_ready') }}</p>
              </div>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-top: 24px;">
            <div style="padding: 16px 18px; border-radius: 18px; background: rgba(255,255,255,0.12); backdrop-filter: blur(8px);">
              <p style="margin: 0 0 6px; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: rgba(255,255,255,0.72);">{{ __('messages.smart_campaigns') }}</p>
              <p style="margin: 0; font-size: 28px; font-weight: 800; color: #fff;">{{ $smartAdStats['total'] }}</p>
            </div>
            <div style="padding: 16px 18px; border-radius: 18px; background: rgba(255,255,255,0.12); backdrop-filter: blur(8px);">
              <p style="margin: 0 0 6px; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: rgba(255,255,255,0.72);">{{ __('messages.smart_impressions_label') }}</p>
              <p style="margin: 0; font-size: 28px; font-weight: 800; color: #fff;">{{ $smartAdStats['impressions'] }}</p>
            </div>
            <div style="padding: 16px 18px; border-radius: 18px; background: rgba(255,255,255,0.12); backdrop-filter: blur(8px);">
              <p style="margin: 0 0 6px; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: rgba(255,255,255,0.72);">{{ __('messages.smart_clicks_label') }}</p>
              <p style="margin: 0; font-size: 28px; font-weight: 800; color: #fff;">{{ $smartAdStats['clicks'] }}</p>
            </div>
            <div style="padding: 16px 18px; border-radius: 18px; background: rgba(255,255,255,0.12); backdrop-filter: blur(8px);">
              <p style="margin: 0 0 6px; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: rgba(255,255,255,0.72);">{{ __('messages.smart_targeting') }}</p>
              <p style="margin: 0; font-size: 15px; line-height: 1.6; color: #fff;">{{ __('messages.smart_targeting_summary') }}</p>
            </div>
          </div>

          <div class="home-smart-actions">
            <a href="{{ route('ads.smart.index') }}" class="button secondary home-smart-action">&nbsp;{{ __('messages.smart_list_ads') }}&nbsp;</a>
            <a href="{{ route('ads.smart.create') }}" class="button primary home-smart-action">&nbsp;{{ __('messages.smart_create_ad') }}&nbsp;</a>
            <a href="{{ route('ads.smart.code') }}" class="button home-smart-action">&nbsp;{{ __('messages.code') }}&nbsp;<i class="fa fa-code" aria-hidden="true"></i>&nbsp;</a>
            <a href="{{ route('legacy.state', ['ty' => 'smart', 'st' => 'vu']) }}" class="button tertiary home-smart-action">&nbsp;{{ __('messages.stats') }}&nbsp;<i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;</a>
          </div>
        </div>
      </div>

      <!-- Visits Decoration -->
      <div class="stats-decoration v2 big secondary" id="Exchange" style="background: url({{ asset('themes/default/assets/img/graph/stat/07.png') }}) repeat-x bottom;">
        <p class="stats-decoration-title">{{ __('messages.exvisit') }}</p>
        <p class="stats-decoration-subtitle">
            {{ __('messages.you_have') }}&nbsp;{{ $user->vu }}&nbsp;{{ __('messages.ptvysa') }}&nbsp;
            {{ __('messages.yshbv') }}&nbsp;:&nbsp;{{ $visitStats['vu'] }}
        </p>
        <p class="stats-decoration-text">
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
                                <option value="smartads">{{ __('messages.smart_convert_option') }}</option>
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
