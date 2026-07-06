@php
    function hasAccess($featureLevel, $userRole) {
        $roles = ['guest' => 1, 'member' => 2, 'premium' => 3];
        return $roles[$userRole] >= $roles[$featureLevel];
    }
@endphp

<div class="seo-results-container" style="text-align: left; padding: 20px;">
    <h2 style="margin-bottom: 20px; text-align: center;">{!! __('messages.seo_results_for', ['url' => '<a href="' . e($results['url']) . '" target="_blank" style="color: var(--primary);">' . e($results['url']) . '</a>']) !!}</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- General Info -->
        <div style="background: rgba(0,0,0,0.03); border-radius: 16px; padding: 20px; border: 1px solid rgba(0,0,0,0.05);">
            <h3 style="font-size: 1.2rem; margin-bottom: 15px;"><i class="fa-solid fa-circle-info text-primary"></i> {{ __('messages.seo_general') }}</h3>
            <p><strong>{{ __('messages.seo_title') }}:</strong> {{ $results['title'] ?: 'N/A' }}</p>
            <p><strong>{{ __('messages.seo_description') }}:</strong> {{ $results['description'] ?: 'N/A' }}</p>
            <p><strong>{{ __('messages.seo_ip') }}:</strong> {{ $results['ip'] ?: 'N/A' }}</p>
        </div>

        <!-- Speed Info -->
        <div style="background: rgba(0,0,0,0.03); border-radius: 16px; padding: 20px; border: 1px solid rgba(0,0,0,0.05); position: relative;">
            <h3 style="font-size: 1.2rem; margin-bottom: 15px;"><i class="fa-solid fa-gauge-high text-warning"></i> {{ __('messages.seo_speed') }}</h3>
            @if(hasAccess($settings['speed'], $userRole))
                <p><strong>{{ __('messages.seo_load_time') }}:</strong> {{ $results['speed']['time_seconds'] }}s</p>
                <p><strong>{{ __('messages.seo_status') }}:</strong> 
                    @if($results['speed']['time_seconds'] < 1)
                        <span style="color: #10b981; font-weight: bold;">{{ __('messages.seo_fast') }}</span>
                    @elseif($results['speed']['time_seconds'] < 3)
                        <span style="color: #f59e0b; font-weight: bold;">{{ __('messages.seo_average') }}</span>
                    @else
                        <span style="color: #ef4444; font-weight: bold;">{{ __('messages.seo_slow') }}</span>
                    @endif
                </p>
            @else
                <div style="filter: blur(5px); pointer-events: none; opacity: 0.5;">
                    <p><strong>{{ __('messages.seo_load_time') }}:</strong> 1.5s</p>
                    <p><strong>{{ __('messages.seo_status') }}:</strong> {{ __('messages.seo_fast') }}</p>
                </div>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; width: 100%;">
                    <i class="fa-solid fa-lock" style="font-size: 2rem; color: var(--text-muted); margin-bottom: 10px;"></i>
                    <p style="font-weight: bold;">{{ __('messages.seo_requires_access', ['access' => __('messages.seo_' . $settings['speed'] . ($settings['speed'] === 'guest' ? '_everyone' : '_only'))]) }}</p>
                    @if($userRole === 'guest')
                        <a href="{{ route('login') }}" class="button primary small">{{ __('messages.seo_login_to_view') }}</a>
                    @elseif($userRole === 'member' && $settings['speed'] === 'premium')
                        <a href="{{ route('billing.plans') }}" class="button primary small">{{ __('messages.seo_upgrade_premium') }}</a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Errors Info -->
    <div style="background: rgba(0,0,0,0.03); border-radius: 16px; padding: 20px; border: 1px solid rgba(0,0,0,0.05); margin-bottom: 20px; position: relative;">
        <h3 style="font-size: 1.2rem; margin-bottom: 15px;"><i class="fa-solid fa-triangle-exclamation text-danger"></i> {{ __('messages.seo_prog_errors') }}</h3>
        @if(hasAccess($settings['errors'], $userRole))
            <ul style="list-style-type: none; padding: 0;">
                <li style="margin-bottom: 10px;">
                    @if($results['errors']['missing_h1'])
                        <i class="fa-solid fa-circle-xmark text-danger"></i> {{ __('messages.seo_missing_h1') }}
                    @else
                        <i class="fa-solid fa-circle-check text-success"></i> {{ __('messages.seo_h1_present') }}
                    @endif
                </li>
                <li style="margin-bottom: 10px;">
                    @if(count($results['errors']['images_without_alt']) > 0)
                        <i class="fa-solid fa-circle-xmark text-danger"></i> {{ __('messages.seo_images_no_alt', ['count' => count($results['errors']['images_without_alt'])]) }}
                    @else
                        <i class="fa-solid fa-circle-check text-success"></i> {{ __('messages.seo_all_images_alt') }}
                    @endif
                </li>
            </ul>
        @else
            <div style="filter: blur(5px); pointer-events: none; opacity: 0.5;">
                <ul style="list-style-type: none; padding: 0;">
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-circle-check text-success"></i> {{ __('messages.seo_h1_present') }}</li>
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-circle-xmark text-danger"></i> {{ __('messages.seo_images_no_alt', ['count' => 5]) }}</li>
                </ul>
            </div>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; width: 100%;">
                <i class="fa-solid fa-lock" style="font-size: 2rem; color: var(--text-muted); margin-bottom: 10px;"></i>
                <p style="font-weight: bold;">{{ __('messages.seo_requires_access', ['access' => __('messages.seo_' . $settings['errors'] . ($settings['errors'] === 'guest' ? '_everyone' : '_only'))]) }}</p>
                @if($userRole === 'guest')
                    <a href="{{ route('login') }}" class="button primary small">{{ __('messages.seo_login_to_view') }}</a>
                @elseif($userRole === 'member' && $settings['errors'] === 'premium')
                    <a href="{{ route('billing.plans') }}" class="button primary small">{{ __('messages.seo_upgrade_premium') }}</a>
                @endif
            </div>
        @endif
    </div>

    <!-- Backlinks Info -->
    <div style="background: rgba(0,0,0,0.03); border-radius: 16px; padding: 20px; border: 1px solid rgba(0,0,0,0.05); position: relative;">
        <h3 style="font-size: 1.2rem; margin-bottom: 15px;"><i class="fa-solid fa-link text-info"></i> {{ __('messages.seo_backlinks_sim') }}</h3>
        @if(hasAccess($settings['backlinks'], $userRole))
            <p><strong>{{ __('messages.seo_total_backlinks') }}:</strong> {{ $results['backlinks']['count'] }}</p>
            <p><strong>{{ __('messages.seo_trust_flow') }}:</strong> {{ $results['backlinks']['trust_flow'] }}</p>
            <p><strong>{{ __('messages.seo_citation_flow') }}:</strong> {{ $results['backlinks']['citation_flow'] }}</p>
        @else
            <div style="filter: blur(5px); pointer-events: none; opacity: 0.5;">
                <p><strong>{{ __('messages.seo_total_backlinks') }}:</strong> 12,345</p>
                <p><strong>{{ __('messages.seo_trust_flow') }}:</strong> 45</p>
                <p><strong>{{ __('messages.seo_citation_flow') }}:</strong> 30</p>
            </div>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; width: 100%;">
                <i class="fa-solid fa-lock" style="font-size: 2rem; color: var(--text-muted); margin-bottom: 10px;"></i>
                <p style="font-weight: bold;">{{ __('messages.seo_requires_access', ['access' => __('messages.seo_' . $settings['backlinks'] . ($settings['backlinks'] === 'guest' ? '_everyone' : '_only'))]) }}</p>
                @if($userRole === 'guest')
                    <a href="{{ route('login') }}" class="button primary small" style="display:inline-block;">{{ __('messages.seo_login_to_view') }}</a>
                @elseif($userRole === 'member' && $settings['backlinks'] === 'premium')
                    <a href="{{ route('billing.plans') }}" class="button primary small" style="display:inline-block;">{{ __('messages.seo_upgrade_premium') }}</a>
                @endif
            </div>
        @endif
    </div>
    
    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ route('seo_checker.index') }}" class="button secondary" style="display:inline-block;">{{ __('messages.seo_check_another') }}</a>
    </div>
</div>
