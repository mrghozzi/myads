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
            <div class="widget-box-content">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="form">
                    @csrf
                    
                    @php
                        $coverOption = \App\Models\Option::where('o_type', 'user')->where('o_order', $user->id)->first();
                        $cover = $coverOption && $coverOption->o_mode != '0' ? $coverOption->o_mode : 'upload/cover.jpg';
                    @endphp

                    <!-- USER PREVIEW -->
                    <div class="user-preview small fixed-height">
                        <figure class="user-preview-cover liquid" style="background: url({{ asset($cover) }}) center center / cover no-repeat;">
                            <img id="cover-preview" src="{{ asset($cover) }}" alt="cover-preview" style="display: none;">
                        </figure>
                        <div class="user-preview-info">
                            <div class="user-short-description small">
                                <div class="user-short-description-avatar user-avatar">
                                    <div class="user-avatar-border">
                                        <div class="hexagon-100-110" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
                                    </div>
                                    <div class="user-avatar-content">
                                        <div class="hexagon-image-68-74" data-src="{{ $user->avatarUrl() }}" style="width: 68px; height: 74px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="68" height="74"></canvas></div>
                                    </div>
                                    <div class="user-avatar-progress-border">
                                        <div class="hexagon-border-84-92" style="width: 84px; height: 92px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="84" height="92"></canvas></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /USER PREVIEW -->

                    <!-- UPLOAD BOXES -->
                    <div class="grid grid-3-3-3 centered" style="margin-bottom: 32px;">
                        <div class="upload-box" id="AvatarUpload" style="cursor: pointer;">
                            <svg class="upload-box-icon icon-members">
                                <use xlink:href="#svg-members"></use>
                            </svg>
                            <p class="upload-box-title">{{ __('messages.avatar') }}</p>
                            <p class="upload-box-text">110x110px min</p>
                        </div>
                        <input type="file" id="Avatarload" name="avatar" accept=".jpg, .jpeg, .png, .gif" style="display:none">
                        
                        <div class="upload-box" id="CoverUpload" style="cursor: pointer;">
                            <svg class="upload-box-icon icon-photos">
                                <use xlink:href="#svg-photos"></use>
                            </svg>
                            <p class="upload-box-title">{{ __('messages.cover') }}</p>
                            <p class="upload-box-text">1184x300px min</p>
                        </div>
                        <input type="file" id="Coverload" name="cover" accept=".jpg, .jpeg, .png, .gif" style="display:none">
                    </div>
                    <!-- /UPLOAD BOXES -->

                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-input small active">
                                <label for="username">{{ __('messages.username') }}</label>
                                <input type="text" id="username" value="{{ $user->username }}" disabled>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small {{ $user->email ? 'active' : '' }}">
                                <label for="email">{{ __('messages.email') }}</label>
                                <input type="email" id="email" name="email" value="{{ $user->email }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-input small">
                                <label for="password">{{ __('messages.new_password') }}</label>
                                <input type="password" id="password" name="password" placeholder="{{ __('messages.leave_blank_to_keep') }}">
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small">
                                <label for="password_confirmation">{{ __('messages.confirm_password') }}</label>
                                <input type="password" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-textarea active">
                                <label for="about_me">{{ __('messages.about_me') }}</label>
                                <textarea id="about_me" name="about_me" rows="6" placeholder="{{ __('messages.about_me_placeholder') }}">{{ old('about_me', $user->sig) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-item">
                            <button type="submit" class="button primary">{{ __('messages.save_changes') }}</button>
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
