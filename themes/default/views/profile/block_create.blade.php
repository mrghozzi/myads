@extends('theme::layouts.master')

@section('content')
<div class="content" style="max-width: 800px; margin: 0 auto; padding-top: 40px;">
    
    <div class="section-header" style="text-align: center; margin-bottom: 40px;">
        <h2 class="section-title" style="font-size: 2.2rem; font-weight: 800; background: linear-gradient(135deg, #FF4B4B 0%, #FF8A8A 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <i class="fa fa-ban" aria-hidden="true"></i>&nbsp;{{ __('messages.block_user') ?? 'Block User' }}
        </h2>
        <p class="section-pre-title" style="margin-top: 10px; font-size: 1.1rem; color: #8f9fc2;">{{ __('messages.block_warning') ?? 'This action will prevent the user from interacting with you.' }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" role="alert" style="border-radius: 12px; font-weight: bold;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" role="alert" style="border-radius: 12px; font-weight: bold;">{{ session('error') }}</div>
    @endif

    <div class="grid grid-6-6">
        <!-- USER PREVIEW CARD -->
        <div class="user-preview" style="border-radius: 24px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08); border: 1px solid rgba(255,255,255,0.5); background: var(--section-bg, #ffffff); overflow: hidden;">
            <figure class="user-preview-cover liquid" style="background: url({{ asset($user->cover_url ?? 'themes/default/assets/img/cover/04.jpg') }}) center center / cover no-repeat; height: 140px;">
                <img src="{{ asset($user->cover_url ?? 'themes/default/assets/img/cover/04.jpg') }}" alt="cover" style="display: none;">
            </figure>
            <div class="user-preview-info" style="padding: 0 30px 30px;">
                <div class="user-short-description" style="margin-top: -60px;">
                    <a class="user-short-description-avatar user-avatar medium" href="{{ route('profile.show', $user->username) }}" style="margin: 0 auto;">
                        <div class="user-avatar-border">
                            <div class="hexagon-120-132" style="width: 120px; height: 132px; position: relative;"><canvas width="120" height="132" style="position: absolute; top: 0; left: 0;"></canvas></div>
                        </div>
                        <div class="user-avatar-content">
                            <div class="hexagon-image-82-90" data-src="{{ $user->avatarUrl() }}" style="width: 82px; height: 90px; position: relative;"><canvas width="82" height="90" style="position: absolute; top: 0; left: 0;"></canvas></div>
                        </div>
                    </a>
                    <p class="user-short-description-title" style="margin-top: 20px; font-size: 1.4rem;"><a href="{{ route('profile.show', $user->username) }}">{{ $user->username }}</a></p>
                    <p class="user-short-description-text"><a href="{{ route('profile.show', $user->username) }}">{{ $user->name ?? __('messages.member') }}</a></p>
                </div>
            </div>
        </div>

        <!-- BLOCK FORM -->
        <div class="widget-box" style="border-radius: 24px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05); border: 1px solid rgba(255,255,255,0.5); padding: 40px;">
            <form action="{{ route('profile.block.store', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                
                <div class="form-item" style="margin-bottom: 25px;">
                    <div class="form-input small full">
                        <label for="block_type" style="font-weight: 700; color: var(--text-color); margin-bottom: 10px; display: block;">
                            <i class="fa fa-shield-alt" style="color: #615dfa;"></i>&nbsp;{{ __('messages.block_type') ?? 'Block Type' }}
                        </label>
                        <select id="block_type" name="block_type" required style="border-radius: 12px; height: 50px; border: 1px solid #eaeaf5; background: #fcfcfd; font-weight: 600;">
                            <option value="messages_only">{{ __('messages.block_messages_only') ?? 'Block Messages Only' }}</option>
                            <option value="full_platform">{{ __('messages.block_full_platform') ?? 'Full Platform Block' }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-item" style="margin-bottom: 30px;">
                    <div class="form-input small full">
                        <label for="duration" style="font-weight: 700; color: var(--text-color); margin-bottom: 10px; display: block;">
                            <i class="fa fa-calendar-alt" style="color: #615dfa;"></i>&nbsp;{{ __('messages.block_duration') ?? 'Duration (Days)' }}
                        </label>
                        <input type="number" id="duration" name="duration" placeholder="{{ __('messages.forever') ?? 'Forever (Leave empty)' }}" min="1" style="border-radius: 12px; height: 50px; border: 1px solid #eaeaf5; background: #fcfcfd; font-weight: 600;">
                    </div>
                </div>
                
                <div class="form-actions" style="display: flex; gap: 15px;">
                    <a href="{{ route('profile.show', $user->username) }}" class="button white full" style="border-radius: 14px; height: 54px; font-weight: 700;">{{ __('messages.cancel') ?? 'Cancel' }}</a>
                    <button type="submit" class="button primary full" style="border-radius: 14px; height: 54px; font-weight: 700; background: linear-gradient(135deg, #FF4B4B 0%, #D42828 100%); border: none; box-shadow: 0 10px 20px rgba(255, 75, 75, 0.3);">
                        <i class="fa fa-ban"></i>&nbsp;{{ __('messages.block') ?? 'Block' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initHexagons === 'function') {
            initHexagons();
        }
    });
</script>
@endpush
@endsection
