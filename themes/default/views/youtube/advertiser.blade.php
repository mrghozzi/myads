@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%; background-size: cover;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.yt_advertiser') }}</p>
    <p class="section-banner-text">{{ __('messages.yt_manage_campaigns') }}</p>
</div>

<div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; margin-bottom: 12px;">
    <a href="{{ route('ads.index') }}" class="button tertiary"><i class="fa fa-arrow-left"></i> {{ __('messages.back') }}</a>
    <a href="{{ route('youtube.exchange.index') }}" class="button secondary"><i class="fa-brands fa-youtube"></i> {{ __('messages.yt_watch_earn_btn') }}</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
    <!-- Create Campaign Form -->
    <div class="widget-box" style="padding: 0; overflow: hidden;">
        <div style="display: flex; align-items: center; padding: 20px 28px; border-bottom: 1px solid #f1f1f5; background: linear-gradient(135deg, rgba(225,29,72,.05) 0%, rgba(190,18,60,.05) 100%);">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #e11d48 0%, #be123c 100%); display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                <i class="fa-brands fa-youtube" style="color: #fff; font-size: 1rem;"></i>
            </div>
            <h4 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #3e3f5e;">{{ __('messages.yt_create_campaign') }}</h4>
        </div>
        <div style="padding: 28px;">
            <form action="{{ route('youtube.advertiser.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                    <div class="form-item">
                        <div class="form-input active">
                            <label for="youtube_url">{{ __('messages.yt_video_url') }}</label>
                            <input type="url" id="youtube_url" name="youtube_url" required placeholder="https://www.youtube.com/watch?v=..." value="{{ old('youtube_url') }}" style="width: 100%; border: 1px solid #dedeea; border-radius: 12px; padding: 0 18px; height: 54px; font-weight: 600; color: #3e3f5e;">
                        </div>
                    </div>
                    <div class="form-item">
                        <div class="form-input active">
                            <label for="duration_required">{{ __('messages.yt_duration_req') }}</label>
                            <input type="number" id="duration_required" name="duration_required" required min="15" max="600" value="{{ old('duration_required', 30) }}" style="width: 100%; border: 1px solid #dedeea; border-radius: 12px; padding: 0 18px; height: 54px; font-weight: 600; color: #3e3f5e;">
                        </div>
                    </div>
                    <div class="form-item">
                        <div class="form-input active">
                            <label for="reward_points">{{ __('messages.yt_reward_per_view') }}</label>
                            <input type="number" id="reward_points" name="reward_points" required step="0.01" min="0.01" value="{{ old('reward_points') }}" style="width: 100%; border: 1px solid #dedeea; border-radius: 12px; padding: 0 18px; height: 54px; font-weight: 600; color: #3e3f5e;">
                        </div>
                    </div>
                    <div class="form-item">
                        <div class="form-input active">
                            <label for="total_budget">{{ __('messages.yt_total_budget') }}</label>
                            <input type="number" id="total_budget" name="total_budget" required step="0.01" min="1" value="{{ old('total_budget') }}" style="width: 100%; border: 1px solid #dedeea; border-radius: 12px; padding: 0 18px; height: 54px; font-weight: 600; color: #3e3f5e;">
                            <p style="margin: 8px 0 0; font-size: 0.8rem; color: #8f91ac; font-weight: 600;">{{ __('messages.yt_available_pts', ['pts' => number_format((float)auth()->user()->pts, 2)]) }}</p>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 28px;">
                    <button type="submit" class="button primary" style="background: linear-gradient(135deg, #e11d48 0%, #be123c 100%); box-shadow: 0 4px 15px rgba(225,29,72,0.25); border: none; padding: 0 32px; height: 48px; border-radius: 12px; font-weight: 700;">
                        <i class="fa fa-plus"></i> {{ __('messages.yt_add_campaign') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Active Campaigns List -->
    <div class="widget-box" style="padding: 0; overflow: hidden;">
        <div style="padding: 20px 28px; border-bottom: 1px solid #f1f1f5;">
            <h4 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #3e3f5e;">{{ __('messages.yt_active_campaigns') }}</h4>
        </div>
        <div style="padding: 0 28px 24px;">
            @if($videos->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 16px;">
                        <thead>
                            <tr>
                                <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; border-bottom: 2px solid #f1f1f5;">{{ __('messages.yt_video') }}</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; border-bottom: 2px solid #f1f1f5;">{{ __('messages.yt_duration') }}</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; border-bottom: 2px solid #f1f1f5;">{{ __('messages.yt_reward') }}</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; border-bottom: 2px solid #f1f1f5;">{{ __('messages.yt_budget_remaining') }}</th>
                                <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; border-bottom: 2px solid #f1f1f5;">{{ __('messages.yt_status') }}</th>
                                <th style="padding: 12px 16px; text-align: right; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; border-bottom: 2px solid #f1f1f5;">{{ __('messages.yt_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($videos as $video)
                                <tr style="transition: background-color 0.2s ease;">
                                    <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <img src="{{ $video->thumbnail_url }}" alt="thumb" style="width: 60px; height: 40px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                            <a href="https://youtube.com/watch?v={{ $video->youtube_id }}" target="_blank" style="color: #615dfa; font-weight: 600; text-decoration: none;">
                                                {{ $video->youtube_id }}
                                            </a>
                                        </div>
                                    </td>
                                    <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa; font-weight: 600; color: #3e3f5e;">{{ $video->duration_required }}s</td>
                                    <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa; font-weight: 600; color: #10b981;">{{ $video->reward_points }} PTS</td>
                                    <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa; font-weight: 600; color: #3e3f5e;">{{ $video->remaining_budget }} / {{ $video->total_budget }}</td>
                                    <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa;">
                                        @if($video->status == 'active')
                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: rgba(16,185,129,0.1); color: #10b981; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">{{ __('messages.active') }}</span>
                                        @elseif($video->status == 'paused')
                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: rgba(245,158,11,0.1); color: #f59e0b; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">{{ __('messages.pending') }}</span>
                                        @elseif($video->status == 'completed')
                                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: rgba(143,145,172,0.1); color: #8f91ac; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">{{ __('messages.completed') }}</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa; text-align: right;">
                                        @if($video->status == 'active')
                                            <form action="{{ route('youtube.advertiser.pause', $video->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                <button style="background: rgba(245,158,11,0.1); color: #f59e0b; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f59e0b'; this.style.color='#fff';" onmouseout="this.style.background='rgba(245,158,11,0.1)'; this.style.color='#f59e0b';" title="Pause">
                                                    <i class="fa fa-pause"></i>
                                                </button>
                                            </form>
                                        @elseif($video->status == 'paused')
                                            <form action="{{ route('youtube.advertiser.resume', $video->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                <button style="background: rgba(16,185,129,0.1); color: #10b981; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#10b981'; this.style.color='#fff';" onmouseout="this.style.background='rgba(16,185,129,0.1)'; this.style.color='#10b981';" title="Resume">
                                                    <i class="fa fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 40px 20px;">
                    <i class="fa-brands fa-youtube" style="font-size: 3rem; color: #dedeea; margin-bottom: 12px;"></i>
                    <p style="color: #8f91ac; font-weight: 600;">{{ __('messages.yt_no_campaigns') }}</p>
                </div>
            @endif
        </div>
        @if($videos->hasPages())
            <div style="padding: 0 28px 24px;">
                {{ $videos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
