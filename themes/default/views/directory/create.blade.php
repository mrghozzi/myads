@extends('theme::layouts.master')

@section('content')
<div class="directory-create-shell">
    <div class="section-banner" style="background: linear-gradient(135deg, #615dfa 0%, #40d4f3 100%); min-height: 180px; border-radius: 12px; margin-bottom: 30px; position: relative; overflow: hidden; display: flex; align-items: center; padding: 0 40px;">
        <div class="section-banner-content" style="z-index: 2; position: relative;">
            <p class="section-banner-title" style="font-size: 2rem; font-weight: 700; color: #fff; margin-bottom: 8px;">{{ __('messages.addwebsitdir') }}</p>
            <p class="section-banner-text" style="color: rgba(255, 255, 255, 0.9); font-size: 1rem;">{{ __('messages.seo_directory_description') }}</p>
        </div>
        <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="overview-icon" style="position: absolute; right: 40px; top: 50%; transform: translateY(-50%); height: 120px; opacity: 0.2; pointer-events: none;">
    </div>

    <div class="grid grid-3-9">
        <div class="grid-column">
            <div class="widget-box shadow-sm" style="border-radius: 12px; border: none;">
                <div class="widget-box-content">
                    <a href="{{ url('/directory') }}" class="button secondary small w-100" style="display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-arrow-left me-2"></i> {{ __('messages.back') }}
                    </a>
                </div>
            </div>
            
            <div class="widget-box shadow-sm mt-3" style="border-radius: 12px; border: none; padding: 20px;">
                <h5 class="widget-box-title" style="font-size: 1rem; margin-bottom: 15px;">{{ __('Tip') }}</h5>
                <p style="font-size: 0.875rem; color: #777; line-height: 1.6;">
                    {{ __('Just enter the website URL and we will try to fetch the title, description and tags for you automatically!') }}
                </p>
            </div>
        </div>

        <div class="grid-column">
            <div class="widget-box shadow-lg" style="border-radius: 16px; border: none; padding: 30px;">
                <div class="widget-box-content">
                    @if($errors->any())
                        <div class="alert alert-danger mb-4" role="alert" style="border-radius: 8px;">
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                @foreach ($errors->all() as $error)
                                    <li><i class="fa fa-exclamation-circle me-2"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @auth
                    <form method="POST" action="{{ route('directory.store') }}" id="add-site-form">
                        @csrf
                        <div class="form-row split mb-4">
                            <div class="form-item">
                                <div class="form-input social-input small active shadow-sm" style="border-radius: 8px; border: 1px solid #eee;">
                                    <div class="social-link no-hover url" id="url-icon-container">
                                        <i class="fa fa-link" aria-hidden="true" id="url-icon"></i>
                                        <div class="spinner-border spinner-border-sm text-primary d-none" id="url-loader" role="status"></div>
                                    </div>
                                    <label for="url">{{ __('messages.url') }}</label>
                                    <input type="url" id="url" name="url" value="{{ old('url') }}" required placeholder="https://example.com">
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input social-input small active shadow-sm" style="border-radius: 8px; border: 1px solid #eee;">
                                    <div class="social-link no-hover name">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                    </div>
                                    <label for="name">{{ __('messages.name') }}</label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-row mb-4">
                            <div class="form-item">
                                <div class="form-input small mid-textarea shadow-sm" style="border-radius: 8px; border: 1px solid #eee;">
                                    <label for="description">{{ __('messages.text_p') }}</label>
                                    <textarea id="description" name="txt" style="min-height: 120px;">{{ old('txt') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-row split mb-4">
                            <div class="form-item">
                                <div class="form-select shadow-sm" style="border-radius: 8px; border: 1px solid #eee;">
                                    <label for="profile-status">{{ __('messages.cat') }}</label>
                                    <select id="profile-status" name="categ">
                                        @foreach($mainCategories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @foreach($subCategories->get($cat->id, collect()) as $sub)
                                                <option value="{{ $sub->id }}">_{{ $sub->name }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    <svg class="form-select-icon icon-small-arrow">
                                        <use xlink:href="#svg-small-arrow"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input social-input small active shadow-sm" style="border-radius: 8px; border: 1px solid #eee;">
                                    <div class="social-link no-hover tag">
                                        <i class="fa fa-tag" aria-hidden="true"></i>
                                    </div>
                                    <label for="tag">{{ __('messages.tag') }}</label>
                                    <input type="text" id="tag" name="tag" value="{{ old('tag') }}" placeholder="tag1, tag2...">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="s_type" value="1" />
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="button primary big" style="padding: 0 40px; border-radius: 8px; font-weight: 600;">
                                {{ __('messages.spread') }}
                            </button>
                        </div>
                    </form>
                    @else
                        <div class="alert alert-warning" role="alert">
                            <a href="{{ route('login') }}">{{ __('messages.login') }}</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .social-link.url .spinner-border {
        width: 1rem;
        height: 1rem;
    }
    .form-input.social-input input {
        padding-left: 60px !important;
    }
    .shadow-lg {
        box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
    }
    .shadow-sm {
        box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
    }
    .w-100 { width: 100%; }
    .me-2 { margin-right: 0.5rem; }
    .mt-3 { margin-top: 1rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .d-none { display: none !important; }
    .d-flex { display: flex !important; }
    .justify-content-end { justify-content: flex-end !important; }
    .align-items-center { align-items: center !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('url');
    const nameInput = document.getElementById('name');
    const descInput = document.getElementById('description');
    const tagInput = document.getElementById('tag');
    const urlLoader = document.getElementById('url-loader');
    const urlIcon = document.getElementById('url-icon');

    let timeout = null;

    urlInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const url = this.value;
        
        if (!url || !url.startsWith('http')) return;

        timeout = setTimeout(() => {
            fetchMetadata(url);
        }, 1000);
    });

    function fetchMetadata(url) {
        urlIcon.classList.add('d-none');
        urlLoader.classList.remove('d-none');

        fetch('{{ route("directory.fetch_metadata") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ url: url })
        })
        .then(response => response.json())
        .then(data => {
            if (data.title && !nameInput.value) {
                nameInput.value = data.title;
                nameInput.parentElement.classList.add('active');
            }
            if (data.description && !descInput.value) {
                descInput.value = data.description;
                descInput.parentElement.classList.add('active');
            }
            if (data.tags && !tagInput.value) {
                tagInput.value = data.tags;
                tagInput.parentElement.classList.add('active');
            }
        })
        .catch(error => console.error('Error fetching metadata:', error))
        .finally(() => {
            urlIcon.classList.remove('d-none');
            urlLoader.classList.add('d-none');
        });
    }
});
</script>
@endsection
