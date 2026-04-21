@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: linear-gradient(135deg, rgba(255,107,61,0.95), rgba(97,93,250,0.92));">
    <p class="section-banner-title">{{ __('messages.groups_edit_title') }}</p>
    <p class="section-banner-text">{{ __('messages.groups_edit_description') }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title">{{ __('messages.Settings') }}</p>
            <div class="widget-box-content">
                <div class="sidebar-menu">
                    <a class="sidebar-menu-item active" href="{{ route('groups.edit', $group) }}">
                        <svg class="sidebar-menu-item-icon icon-settings"><use xlink:href="#svg-settings"></use></svg>
                        {{ __('messages.general_settings') }}
                    </a>
                    <a class="sidebar-menu-item" href="{{ route('groups.show', $group) }}">
                        <svg class="sidebar-menu-item-icon icon-group"><use xlink:href="#svg-group"></use></svg>
                        {{ __('messages.back_to_group') }}
                    </a>
                </div>
            </div>
        </div>
        
        <x-widget-column side="groups_left" />
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title">{{ __('messages.groups_edit_title') }}</p>
            <div class="widget-box-content" style="padding: 32px;">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-inline-start: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('groups.update', $group) }}" method="POST" enctype="multipart/form-data" class="form">
                    @csrf
                    @method('PUT')
                    
                    @php
                        $cover = $group->cover_path ?: 'upload/cover.jpg';
                        $avatar = $group->avatar_path ?: 'upload/avatar.png';
                    @endphp

                    <!-- SUPERDESIGN GROUP PREVIEW -->
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
                                <div class="user-short-description-avatar user-avatar">
                                    <div class="user-avatar-border">
                                        <div class="hexagon-100-110" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
                                    </div>
                                    <div class="user-avatar-content">
                                        <div class="hexagon-image-68-74" data-src="{{ asset($avatar) }}" style="width: 68px; height: 74px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="68" height="74"></canvas></div>
                                    </div>
                                    <div class="user-avatar-progress-border">
                                        <div class="hexagon-border-84-92" style="width: 84px; height: 92px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="84" height="92"></canvas></div>
                                    </div>
                                    
                                    <!-- Avatar Edit Button -->
                                    <div id="AvatarUpload" style="position: absolute; bottom: 0px; right: 0px; width: 32px; height: 32px; background: var(--primary-color, #23d2e2); border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; color: #fff; box-shadow: 0 0 0 3px var(--widget-box-bg, #fff); z-index: 20; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                        <svg class="icon-camera" style="width: 14px; height: 14px; fill: currentColor;"><use xlink:href="#svg-camera"></use></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /SUPERDESIGN GROUP PREVIEW -->
                    
                    <!-- HIDDEN UPLOADS -->
                    <input type="file" id="Avatarload" name="avatar" accept=".jpg, .jpeg, .png, .gif, .webp" style="display:none">
                    <input type="file" id="Coverload" name="cover" accept=".jpg, .jpeg, .png, .gif, .webp" style="display:none">

                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small active">
                                <label for="group-name">{{ __('messages.name') }}</label>
                                <input id="group-name" type="text" name="name" value="{{ old('name', $group->name) }}" required style="border-radius: 12px;">
                            </div>
                        </div>

                        <div class="form-item">
                            <div class="form-input small active">
                                <label for="group-slug">{{ __('messages.slug') }}</label>
                                <input id="group-slug" type="text" name="slug" value="{{ old('slug', $group->slug) }}" style="border-radius: 12px;">
                            </div>
                        </div>
                    </div>

                    <div class="form-item">
                        <div class="form-input small active">
                            <label for="group-short-description">{{ __('messages.groups_short_description') }}</label>
                            <input id="group-short-description" type="text" name="short_description" value="{{ old('short_description', $group->short_description) }}" style="border-radius: 12px;">
                        </div>
                    </div>

                    <div class="form-item">
                        <label class="mb-2 d-block">{{ __('messages.groups_privacy') }}</label>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
                            <label class="widget-box" style="margin:0; cursor: pointer;">
                                <div class="widget-box-content">
                                    <input type="radio" name="privacy" value="public" {{ old('privacy', $group->privacy) === 'public' ? 'checked' : '' }}>
                                    <strong>{{ __('messages.groups_public') }}</strong>
                                    <p class="text-muted mb-0">{{ __('messages.groups_public_hint') }}</p>
                                </div>
                            </label>
                            <label class="widget-box" style="margin:0; cursor: pointer;">
                                <div class="widget-box-content">
                                    <input type="radio" name="privacy" value="private_request" {{ old('privacy', $group->privacy) === 'private_request' ? 'checked' : '' }}>
                                    <strong>{{ __('messages.groups_private') }}</strong>
                                    <p class="text-muted mb-0">{{ __('messages.groups_private_hint') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-item">
                        <div class="form-textarea active">
                            <label for="group-description">{{ __('messages.description') }}</label>
                            <textarea id="group-description" name="description" rows="6" style="border-radius: 12px; padding-top: 16px;">{{ old('description', $group->description) }}</textarea>
                        </div>
                    </div>

                    <div class="form-item">
                        <div class="form-textarea active">
                            <label for="group-rules">{{ __('messages.groups_rules') }}</label>
                            <textarea id="group-rules" name="rules_markdown" rows="6" style="border-radius: 12px; padding-top: 16px;">{{ old('rules_markdown', $group->rules_markdown) }}</textarea>
                        </div>
                    </div>

                    <div class="form-row" style="margin-top: 32px; display: flex; justify-content: flex-end;">
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

        avatarInput.addEventListener('change', function(e) {
            if(this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
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
