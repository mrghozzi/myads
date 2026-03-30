@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center; height: 100%;">
        <svg class="icon-social" style="width: 32px; height: 32px; fill: #fff;"><use xlink:href="#svg-social"></use></svg>
    </div>
    <p class="section-banner-title">{{ __('messages.social_links') }}</p>
    <p class="section-banner-text">{{ __('messages.social_links_desc') ?? 'Connect your social media profiles to show them on your profile.' }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title">{{ __('messages.social_links') }}</p>
            <div class="widget-box-content" style="padding: 32px;">
                <form action="{{ route('profile.social.update') }}" method="POST" class="form">
                    @csrf
                    
                    <div class="user-preview small" style="margin-bottom: 40px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 24px; display: flex; align-items: center; gap: 20px; background: linear-gradient(90deg, var(--widget-box-bg, #fff) 0%, var(--dark-light-color, #fbfcfd) 100%);">
                        <div class="user-avatar small">
                            <div class="hexagon-image-68-74" data-src="{{ $user->avatarUrl() }}" style="width: 68px; height: 74px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="68" height="74"></canvas></div>
                        </div>
                        <div class="user-info">
                            <p class="user-preview-title" style="font-size: 18px; margin-bottom: 4px;">{{ $user->username }}</p>
                            <p class="user-preview-text" style="color: var(--primary-color, #23d2e2); font-weight: 700; display: flex; align-items: center; gap: 6px;">
                                <i class="fa fa-link"></i> {{ __('messages.social_links') }}
                            </p>
                        </div>
                    </div>

                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-input small {{ isset($links['facebook']) ? 'active' : '' }}">
                                <label for="facebook">{{ __('messages.facebook') ?? 'Facebook' }}</label>
                                <input type="text" id="facebook" name="facebook" value="{{ old('facebook', $links['facebook'] ?? '') }}" placeholder="@username" style="border-radius: 12px; padding-left: 45px;">
                                <i class="fab fa-facebook-f" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #1877f2; font-size: 18px;"></i>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small {{ isset($links['twitter']) ? 'active' : '' }}">
                                <label for="twitter">{{ __('messages.twitter') ?? 'Twitter (X)' }}</label>
                                <input type="text" id="twitter" name="twitter" value="{{ old('twitter', $links['twitter'] ?? '') }}" placeholder="@username" style="border-radius: 12px; padding-left: 45px;">
                                <i class="fab fa-x-twitter" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #000; font-size: 18px;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-row split" style="margin-top: 24px;">
                        <div class="form-item">
                            <div class="form-input small {{ isset($links['vkontakte']) ? 'active' : '' }}">
                                <label for="vkontakte">{{ __('messages.vkontakte') ?? 'Vkontakte' }}</label>
                                <input type="text" id="vkontakte" name="vkontakte" value="{{ old('vkontakte', $links['vkontakte'] ?? '') }}" placeholder="id123 or username" style="border-radius: 12px; padding-left: 45px;">
                                <i class="fab fa-vk" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #0077ff; font-size: 18px;"></i>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small {{ isset($links['linkedin']) ? 'active' : '' }}">
                                <label for="linkedin">{{ __('messages.linkedin') ?? 'LinkedIn' }}</label>
                                <input type="text" id="linkedin" name="linkedin" value="{{ old('linkedin', $links['linkedin'] ?? '') }}" placeholder="username" style="border-radius: 12px; padding-left: 45px;">
                                <i class="fab fa-linkedin-in" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #0077b5; font-size: 18px;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-row split" style="margin-top: 24px;">
                        <div class="form-item">
                            <div class="form-input small {{ isset($links['instagram']) ? 'active' : '' }}">
                                <label for="instagram">{{ __('messages.instagram') ?? 'Instagram' }}</label>
                                <input type="text" id="instagram" name="instagram" value="{{ old('instagram', $links['instagram'] ?? '') }}" placeholder="@username" style="border-radius: 12px; padding-left: 45px;">
                                <i class="fab fa-instagram" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #e4405f; font-size: 18px;"></i>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small {{ isset($links['youtube']) ? 'active' : '' }}">
                                <label for="youtube">{{ __('messages.youtube') ?? 'YouTube' }}</label>
                                <input type="text" id="youtube" name="youtube" value="{{ old('youtube', $links['youtube'] ?? '') }}" placeholder="@channel" style="border-radius: 12px; padding-left: 45px;">
                                <i class="fab fa-youtube" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #ff0000; font-size: 18px;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-row split" style="margin-top: 24px;">
                        <div class="form-item">
                            <div class="form-input small {{ isset($links['threads']) ? 'active' : '' }}">
                                <label for="threads">{{ __('messages.threads') ?? 'Threads' }}</label>
                                <input type="text" id="threads" name="threads" value="{{ old('threads', $links['threads'] ?? '') }}" placeholder="@username" style="border-radius: 12px; padding-left: 45px;">
                                <i class="fab fa-threads" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #000; font-size: 18px;"></i>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small {{ isset($links['reddit']) ? 'active' : '' }}">
                                <label for="reddit">{{ __('messages.reddit') ?? 'Reddit' }}</label>
                                <input type="text" id="reddit" name="reddit" value="{{ old('reddit', $links['reddit'] ?? '') }}" placeholder="username" style="border-radius: 12px; padding-left: 45px;">
                                <i class="fab fa-reddit-alien" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #ff4500; font-size: 18px;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-row split" style="margin-top: 24px;">
                        <div class="form-item">
                            <div class="form-input small {{ isset($links['github']) ? 'active' : '' }}">
                                <label for="github">{{ __('messages.github') ?? 'GitHub' }}</label>
                                <input type="text" id="github" name="github" value="{{ old('github', $links['github'] ?? '') }}" placeholder="username" style="border-radius: 12px; padding-left: 45px;">
                                <i class="fab fa-github" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #333; font-size: 18px;"></i>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small {{ isset($links['adstn']) ? 'active' : '' }}">
                                <label for="adstn">{{ __('messages.adstn') ?? 'ADStn' }}</label>
                                <input type="text" id="adstn" name="adstn" value="{{ old('adstn', $links['adstn'] ?? '') }}" placeholder="username" style="border-radius: 12px; padding-left: 45px;">
                                <i class="fa-brands fa-buysellads" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: rgb(84, 56, 163); font-size: 18px;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-row" style="margin-top: 40px; display: flex; justify-content: center;">
                        <div class="form-item" style="min-width: 200px;">
                            <button type="submit" class="button primary" style="width: 100%; border-radius: 12px; padding: 12px 24px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;">
                                {{ __('messages.save_changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof initHexagons === 'function') {
            initHexagons();
        }
    });
</script>
@endpush
@endsection
