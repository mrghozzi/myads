@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.edit_profile') }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title">{{ __('messages.edit_profile') }}</p>
            <div class="widget-box-content" style="padding: 32px;">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="form">
                    @csrf
                    
                    @php
                        $coverOption = \App\Models\Option::where('o_type', 'user')->where('o_order', $user->id)->first();
                        $cover = $coverOption && $coverOption->o_mode != '0' ? $coverOption->o_mode : 'upload/cover.jpg';
                    @endphp

                    <!-- SUPERDESIGN USER PREVIEW -->
                    <div class="user-preview small fixed-height" style="margin-bottom: 40px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: visible;">
                        <figure class="user-preview-cover liquid" style="background: url({{ asset($cover) }}) center center / cover no-repeat; position: relative; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                            
                            <!-- Cover Edit Button -->
                            <div id="CoverUpload" style="position: absolute; top: 16px; right: 16px; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); color: #fff; padding: 8px 16px; border-radius: 20px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.2); font-size: 13px; font-weight: 600; z-index: 10;" onmouseover="this.style.background='rgba(0,0,0,0.8)'" onmouseout="this.style.background='rgba(0,0,0,0.6)'">
                                <svg class="icon-camera" style="width: 16px; height: 16px; fill: currentColor;"><use xlink:href="#svg-camera"></use></svg>
                                <span>{{ __('messages.cover') }}</span>
                            </div>
                            
                            <img id="cover-preview" src="{{ asset($cover) }}" alt="cover-preview" style="display: none;">
                        </figure>
                        
                        <div class="user-preview-info">
                            <div class="user-short-description small">
                                <div class="user-short-description-avatar user-avatar" style="position: relative;">
                                    <div class="user-avatar-border">
                                        <div class="hexagon-100-110" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
                                    </div>
                                    <div class="user-avatar-content">
                                        <div class="hexagon-image-68-74" data-src="{{ $user->avatarUrl() }}" style="width: 68px; height: 74px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="68" height="74"></canvas></div>
                                    </div>
                                    <div class="user-avatar-progress-border">
                                        <div class="hexagon-border-84-92" style="width: 84px; height: 92px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="84" height="92"></canvas></div>
                                    </div>
                                    
                                    <!-- Avatar Edit Button -->
                                    <div id="AvatarUpload" style="position: absolute; bottom: -5px; right: -5px; width: 36px; height: 36px; background: var(--primary-color, #23d2e2); border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; color: #fff; box-shadow: 0 0 0 4px var(--widget-box-bg, #fff); z-index: 20; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                        <svg class="icon-camera" style="width: 16px; height: 16px; fill: currentColor;"><use xlink:href="#svg-camera"></use></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /SUPERDESIGN USER PREVIEW -->
                    
                    <!-- HIDDEN UPLOADS -->
                    <input type="file" id="Avatarload" name="avatar" accept=".jpg, .jpeg, .png, .gif" style="display:none">
                    <input type="file" id="Coverload" name="cover" accept=".jpg, .jpeg, .png, .gif" style="display:none">

                    <!-- SECTIONS -->
                    <div style="padding: 0;">
                        
                        <div class="section-header" style="margin-bottom: 24px;">
                            <h4 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--text-color, #3e3f5e);">{{ __('messages.account_details') ?? 'Account Details' }}</h4>
                            <p style="color: var(--text-color-alt, #8f91ac); font-size: 13px; margin-top: 4px;">{{ __('messages.update_account_info') ?? 'Update your basic account information' }}</p>
                        </div>

                        <div class="form-row split">
                            <div class="form-item">
                                <div class="form-input small active">
                                    <label for="username">{{ __('messages.username') }}</label>
                                    <input type="text" id="username" value="{{ $user->username }}" disabled style="background: var(--dark-light-color, rgba(0,0,0,0.03)); opacity: 0.7; color: var(--text-color); border-radius: 12px;">
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input small {{ $user->email ? 'active' : '' }}">
                                    <label for="email">{{ __('messages.email') }}</label>
                                    <input type="text" inputmode="email" id="email" name="email" value="{{ $user->email }}" required style="border-radius: 12px;">
                                </div>
                            </div>
                        </div>

                        <hr style="border: none; border-top: 1px solid var(--border-color, #ebebeb); margin: 32px 0;">

                        <div class="section-header" style="margin-bottom: 24px;">
                            <h4 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--text-color, #3e3f5e);">{{ __('messages.security') ?? 'Security' }}</h4>
                            <p style="color: var(--text-color-alt, #8f91ac); font-size: 13px; margin-top: 4px;">{{ __('messages.leave_blank_to_keep') }}</p>
                        </div>

                        <div class="form-row split">
                            <div class="form-item">
                                <div class="form-input small">
                                    <label for="password">{{ __('messages.new_password') }}</label>
                                    <input type="password" id="password" name="password" style="border-radius: 12px;">
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input small">
                                    <label for="password_confirmation">{{ __('messages.confirm_password') }}</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" style="border-radius: 12px;">
                                </div>
                            </div>
                        </div>

                        <hr style="border: none; border-top: 1px solid var(--border-color, #ebebeb); margin: 32px 0;">

                        <div class="section-header" style="margin-bottom: 24px;">
                            <h4 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--text-color, #3e3f5e);">{{ __('messages.about_me') }}</h4>
                            <p style="color: var(--text-color-alt, #8f91ac); font-size: 13px; margin-top: 4px;">{{ __('messages.about_me_placeholder') }}</p>
                        </div>

                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-textarea active">
                                    <label for="about_me">{{ __('messages.about_me') }}</label>
                                    <textarea id="about_me" name="about_me" rows="6" placeholder="{{ __('messages.about_me_placeholder') }}" style="border-radius: 12px; resize: vertical; padding-top: 16px;">{{ old('about_me', $user->sig) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-row" style="margin-top: 32px; display: flex; justify-content: flex-end;">
                            <div class="form-item" style="min-width: 200px;">
                                <button type="submit" class="button primary" style="width: 100%; border-radius: 12px; padding: 12px 24px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                    {{ __('messages.save_changes') }}
                                </button>
                            </div>
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
        // Init hexagons
        if(typeof initHexagons === 'function') {
            initHexagons();
        }

        // Upload triggers
        const avatarBox = document.getElementById('AvatarUpload');
        const avatarInput = document.getElementById('Avatarload');
        if(avatarBox && avatarInput) {
            avatarBox.addEventListener('click', () => avatarInput.click());
        }

        const coverBox = document.getElementById('CoverUpload');
        const coverInput = document.getElementById('Coverload');
        if(coverBox && coverInput) {
            coverBox.addEventListener('click', () => coverInput.click());
        }

        // Simple preview logic for demonstration purposes (you might want to properly reload hex canvas)
        avatarInput.addEventListener('change', function(e) {
            if(this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Assuming we can update the image source Data-src
                    const hexImg = document.querySelector('.user-preview .hexagon-image-68-74');
                    if (hexImg) {
                        hexImg.style.backgroundImage = 'url(' + e.target.result + ')';
                    }
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        coverInput.addEventListener('change', function(e) {
            if(this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const coverPreview = document.querySelector('.user-preview-cover.liquid');
                    if (coverPreview) {
                        coverPreview.style.backgroundImage = 'url(' + e.target.result + ')';
                    }
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush
@endsection
