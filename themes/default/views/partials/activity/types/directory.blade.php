@php
    $status = $activity;
    $statusUser = $status->user;
    $statusUserProfileUrl = $statusUser ? route('profile.show', $statusUser->username) : '#';
    $statusUserName = $statusUser?->username ?? __('messages.unknown_user');
    $statusUserAvatar = $statusUser?->img ? asset($statusUser->img) : theme_asset('img/avatar/default.png');
    $statusUserPresence = $statusUser?->isOnline() ? 'online' : 'offline';
    $site = $activity->related_content;
@endphp
<div class="widget-box no-padding activity-post-card post{{ $status->id }}">
    <div class="widget-box-status">
        <div class="widget-box-status-content">
            <div class="user-status">
                <a class="user-status-avatar" href="{{ $statusUserProfileUrl }}">
                    <div class="user-avatar small no-outline {{ $statusUserPresence }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-30-32" data-src="{{ $statusUserAvatar }}" style="width: 30px; height: 32px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                            </div>
                        </div>
                        <div class="user-avatar-progress-border">
                            <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                            </div>
                        </div>
                    </div>
                </a>
                <p class="user-status-title medium">
                    <a class="bold" href="{{ $statusUserProfileUrl }}">{{ $statusUserName }}</a>
                    &nbsp;{{ __('messages.added_new_site') }}
                </p>
                <p class="user-status-timestamp small-space">{{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}</p>
            </div>
            
            <div class="widget-box-status-text">
                @php
                    $siteExcerpt = \Illuminate\Support\Str::limit($site->txt ?? '', 180);
                    $siteBanner = $site->prominent_image ?: theme_asset('img/dir_image.png');
                @endphp
                @once
                    @include('theme::partials.directory.lazy_image_script')
                @endonce
                <a class="activity-super" href="{{ $site->url }}" target="_blank">
                    <div class="activity-super-banner" style="background-image: url({{ $siteBanner }});" data-lazy-fetch-url="{{ route('directory.image.fetch', $site->id) }}">
                        <span class="activity-super-category">
                            <i class="fa {{ $site->category->icon ?? 'fa-globe' }}" aria-hidden="true"></i>
                            {{ $site->category->name ?? '' }}
                        </span>
                    </div>
                    <div class="activity-super-content">
                        <h3 class="activity-super-title">{{ $site->name }}</h3>
                        <p class="activity-super-excerpt">{{ $siteExcerpt }}</p>
                        <div class="activity-super-footer">
                            <div class="activity-super-stats">
                                <div class="activity-super-stat">
                                    <i class="fa fa-eye"></i>
                                    {{ $site->vu }}
                                </div>
                            </div>
                            <span class="activity-super-more">
                                {{ __('messages.visit_site') }}
                                <i class="fa fa-external-link"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
