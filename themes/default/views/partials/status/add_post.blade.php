@auth
@php
    $currentUser = auth()->user();
    $composerContext = $composerContext ?? [];
    $composerGroup = $composerContext['group'] ?? null;
    $composerGroupId = (int) ($composerContext['group_id'] ?? ($composerGroup->id ?? 0));
    $isGroupComposer = $composerGroupId > 0;
    $allowedKinds = array_values(array_unique((array) ($composerContext['allowedKinds'] ?? ['text', 'gallery', 'link', 'repost'])));
    $allowGallery = in_array('gallery', $allowedKinds, true);
    $allowLink = in_array('link', $allowedKinds, true);
    $allowRepost = in_array('repost', $allowedKinds, true) && !$isGroupComposer;
    $disableDirectoryOnly = $isGroupComposer || !empty($composerContext['disableDirectoryOnly']);
    $categories = $disableDirectoryOnly
        ? collect()
        : \App\Models\DirectoryCategory::where('statu', 1)->orderBy('name', 'ASC')->get();
    $oldText = (string) old('text', old('txt', request('text', '')));
    $oldLinkUrl = (string) old('link_url', '');
    $oldPublishMode = $disableDirectoryOnly ? 'post' : (string) old('publish_mode', 'post');
    $oldPostKind = (string) old('post_kind', 'text');
    if (!in_array($oldPostKind, array_filter([
        'text',
        $allowGallery ? 'gallery' : null,
        $allowLink ? 'link' : null,
        $allowRepost ? 'repost' : null,
    ]), true)) {
        $oldPostKind = 'text';
    }
    $oldRepostStatusId = $allowRepost ? (string) old('repost_status_id', '') : '';
    $composerPlaceholder = $isGroupComposer
        ? __($composerContext['placeholderKey'] ?? 'messages.groups_post_placeholder')
        : __('messages.whats_on_your_mind', ['username' => $currentUser->username]);
    $composerSubmitLabel = __($composerContext['submitLabelKey'] ?? ($isGroupComposer ? 'messages.groups_publish_post' : 'messages.spread'));
    $composerHasOldInput = old('text') !== null
        || old('txt') !== null
        || old('link_url') !== null
        || old('publish_mode') !== null
        || old('post_kind') !== null
        || old('directory_name') !== null
        || old('directory_tags') !== null
        || old('repost_status_id') !== null
        || old('group_id') !== null;
@endphp

<div class="quick-post composer-refresh" id="social-composer">
    <form class="form" action="{{ route('status.create') }}" method="POST" enctype="multipart/form-data" id="social-composer-form">
        @csrf
        <input type="hidden" name="post_kind" id="composer-post-kind" value="{{ $oldPostKind }}">
        <input type="hidden" name="repost_status_id" id="composer-repost-status-id" value="{{ $oldRepostStatusId }}">
        @if($isGroupComposer)
            <input type="hidden" name="group_id" value="{{ $composerGroupId }}">
        @endif

        <div class="quick-post-body composer-refresh__body">
            @if($composerHasOldInput && session('error'))
                <div class="alert alert-danger composer-refresh__alert" role="alert">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger composer-refresh__alert" role="alert">
                    <ul class="composer-refresh__alert-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="composer-refresh__surface{{ $isGroupComposer ? ' composer-refresh__surface--group' : '' }}">
                <div class="composer-refresh__header">
                    <a class="composer-refresh__identity" href="{{ route('profile.show', $currentUser->username) }}">
                        <img class="composer-refresh__avatar" src="{{ $currentUser->avatarUrl() }}" alt="{{ $currentUser->username }}">
                        <span class="composer-refresh__identity-name">{{ $currentUser->username }}</span>
                    </a>

                    <button type="button" class="composer-refresh__focus-button" id="composer-focus-trigger" aria-label="{{ __('messages.write_post') }}">
                        <i class="fa fa-pen" aria-hidden="true"></i>
                    </button>
                </div>

                @if($composerGroup)
                    <div class="composer-refresh__context">
                        @include('theme::partials.groups.badge', ['groupBadge' => $composerGroup])
                        <p class="composer-refresh__context-copy">{{ __('messages.groups_share_with_group') }}</p>
                    </div>
                @endif

                <div class="composer-refresh__editor" id="composer-editor-shell">
                    <textarea
                        id="composer-text"
                        name="text"
                        class="quicktext composer-refresh__textarea"
                        placeholder="{{ $composerPlaceholder }}"
                    >{{ $oldText }}</textarea>
                </div>

                <p class="composer-refresh__hint">{{ __('messages.mentions_hint') }}</p>
            </div>

            @if($allowRepost)
                <div id="composer-repost-card" class="widget-box composer-refresh__panel composer-refresh__panel--repost" style="display:none;">
                    <div class="widget-box-content composer-refresh__panel-content">
                        <div class="composer-refresh__panel-row">
                            <div class="composer-refresh__panel-copy">
                                <p class="composer-refresh__panel-title">{{ __('messages.quote_repost') }}</p>
                                <p id="composer-repost-text" class="composer-refresh__panel-text">{{ __('messages.repost_ready') }}</p>
                            </div>

                            <button type="button" id="composer-repost-cancel" class="composer-refresh__ghost-button">
                                {{ __('messages.cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if($allowLink)
                <div id="composer-link-block" class="widget-box composer-refresh__panel composer-refresh__panel--link" style="display:none;">
                    <div class="widget-box-content composer-refresh__panel-content">
                        <div class="composer-refresh__field">
                            <label class="composer-refresh__label" for="composer-link-url">{{ __('messages.insertlink') }}</label>
                            <input
                                type="url"
                                id="composer-link-url"
                                name="link_url"
                                class="composer-refresh__control"
                                value="{{ $oldLinkUrl }}"
                                placeholder="{{ __('messages.url_placeholder') }}"
                            >
                        </div>

                        <div id="composer-link-preview" class="composer-refresh__preview" style="display:none;"></div>

                        @unless($disableDirectoryOnly)
                            <div id="composer-publish-options" class="composer-refresh__options" style="display:none;">
                                <p class="composer-refresh__section-hint">{{ __('messages.smart_link_publish_hint') }}</p>

                                <div class="composer-choice-grid">
                                    <label class="composer-choice-card" for="composer-publish-post">
                                        <input
                                            class="composer-choice-input"
                                            type="radio"
                                            id="composer-publish-post"
                                            name="publish_mode"
                                            value="post"
                                            {{ $oldPublishMode !== 'directory_only' ? 'checked' : '' }}
                                        >
                                        <span class="composer-choice-title">{{ __('messages.publish_as_post') }}</span>
                                        <span class="composer-choice-text">{{ __('messages.publish_as_post_hint') }}</span>
                                    </label>

                                    <label class="composer-choice-card" for="composer-publish-directory-only">
                                        <input
                                            class="composer-choice-input"
                                            type="radio"
                                            id="composer-publish-directory-only"
                                            name="publish_mode"
                                            value="directory_only"
                                            {{ $oldPublishMode === 'directory_only' ? 'checked' : '' }}
                                        >
                                        <span class="composer-choice-title">{{ __('messages.move_to_directory') }}</span>
                                        <span class="composer-choice-text">{{ __('messages.move_to_directory_hint') }}</span>
                                    </label>
                                </div>
                            </div>
                        @endunless

                        @unless($disableDirectoryOnly)
                            <div id="composer-directory-fields" class="composer-refresh__directory-grid" style="display:none;">
                                <div class="composer-refresh__field">
                                    <label class="composer-refresh__label" for="composer-directory-name">{{ __('messages.name') }}</label>
                                    <input
                                        type="text"
                                        id="composer-directory-name"
                                        name="directory_name"
                                        class="composer-refresh__control"
                                        value="{{ old('directory_name', '') }}"
                                        placeholder="{{ __('messages.name_placeholder') }}"
                                    >
                                </div>

                                <div class="composer-refresh__field">
                                    <label class="composer-refresh__label" for="composer-directory-category">{{ __('messages.directory') }}</label>
                                    <select id="composer-directory-category" name="directory_category_id" class="composer-refresh__control composer-refresh__control--select">
                                        <option value="0">{{ __('messages.select') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (string) old('directory_category_id', '0') === (string) $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="composer-refresh__field composer-refresh__field--full">
                                    <label class="composer-refresh__label" for="composer-directory-tags">{{ __('messages.tags_placeholder') }}</label>
                                    <input
                                        type="text"
                                        id="composer-directory-tags"
                                        name="directory_tags"
                                        class="composer-refresh__control"
                                        value="{{ old('directory_tags', '') }}"
                                        placeholder="{{ __('messages.tags_placeholder') }}"
                                    >
                                </div>
                            </div>
                        @endunless
                    </div>
                </div>
            @endif

            @if($allowGallery)
                <div id="composer-gallery-block" class="widget-box composer-refresh__panel composer-refresh__panel--gallery" style="display:none;">
                    <div class="widget-box-content composer-refresh__panel-content">
                        <input type="file" id="composer-images" name="images[]" accept=".jpg,.jpeg,.png,.gif,.webp" multiple style="display:none">
                        <div class="composer-refresh__gallery-toolbar" id="composer-gallery-toolbar" style="display:none;">
                            <button type="button" class="composer-refresh__ghost-button composer-refresh__ghost-button--danger" id="composer-gallery-clear">
                                {{ __('messages.delete') }} {{ __('messages.all') }}
                            </button>
                        </div>
                        <div id="composer-gallery-grid" class="composer-refresh__gallery-grid"></div>
                        <p class="composer-refresh__section-hint composer-refresh__section-hint--gallery">{{ __('messages.gallery_limit_hint') }}</p>
                    </div>
                </div>
            @endif

            <input type="file" id="imgupload" name="fimg" accept=".jpg, .jpeg, .png, .gif, .webp" style="display:none"/>
        </div>

        <div class="quick-post-footer composer-refresh__footer">
            <div class="quick-post-footer-actions composer-refresh__toolbar">
                <button type="button" class="quick-post-footer-action composer-refresh__tool" data-title="{{ __('messages.write_post') }}" id="composer-mode-text" aria-pressed="false">
                    <i class="fa fa-font" aria-hidden="true"></i>
                    <span class="composer-refresh__tool-label">{{ __('messages.write_post') }}</span>
                </button>

                @if($allowGallery)
                    <button type="button" class="quick-post-footer-action composer-refresh__tool" data-title="{{ __('messages.insertphoto') }}" id="composer-mode-gallery" aria-pressed="false">
                        <svg class="quick-post-footer-action-icon icon-camera" aria-hidden="true">
                            <use xlink:href="#svg-camera"></use>
                        </svg>
                        <span class="composer-refresh__tool-label">{{ __('messages.insertphoto') }}</span>
                    </button>
                @endif

                @if($allowLink)
                    <button type="button" class="quick-post-footer-action composer-refresh__tool" data-title="{{ __('messages.insertlink') }}" id="composer-mode-link" aria-pressed="false">
                        <i class="fa fa-link" aria-hidden="true"></i>
                        <span class="composer-refresh__tool-label">{{ __('messages.insertlink') }}</span>
                    </button>
                @endif
            </div>

            <div class="quick-post-footer-actions composer-refresh__submit-wrap">
                <button type="submit" class="button small secondary composer-refresh__submit" id="composer-submit">
                    {{ $composerSubmitLabel }}
                </button>
            </div>
        </div>
    </form>
</div>

@push('head')
    <style>
        body[data-theme="css"] #social-composer.composer-refresh {
            --composer-surface: #ffffff;
            --composer-panel-bg: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
            --composer-subtle-bg: #f5f7ff;
            --composer-subtle-bg-strong: #eef2ff;
            --composer-border: #e7eaf5;
            --composer-border-strong: rgba(97, 93, 250, 0.26);
            --composer-text: #2f3142;
            --composer-muted: #8a90a9;
            --composer-helper: #969bb2;
            --composer-shadow: 0 24px 44px rgba(94, 92, 154, 0.12);
            --composer-tool-bg: #f5f6fb;
            --composer-tool-hover: #eceffa;
            --composer-tool-active-bg: rgba(97, 93, 250, 0.12);
            --composer-tool-active-text: #615dfa;
            --composer-accent: #615dfa;
            --composer-accent-shadow: 0 16px 30px rgba(97, 93, 250, 0.18);
            --composer-preview-bg: #ffffff;
        }

        body[data-theme="css_d"] #social-composer.composer-refresh {
            --composer-surface: #1f2436;
            --composer-panel-bg: linear-gradient(180deg, #242b3f 0%, #20263a 100%);
            --composer-subtle-bg: #262d42;
            --composer-subtle-bg-strong: #2a3249;
            --composer-border: #313951;
            --composer-border-strong: rgba(79, 244, 97, 0.24);
            --composer-text: #f5f7ff;
            --composer-muted: #a2acc7;
            --composer-helper: #96a0bb;
            --composer-shadow: 0 24px 44px rgba(0, 0, 0, 0.24);
            --composer-tool-bg: #273047;
            --composer-tool-hover: #2c3650;
            --composer-tool-active-bg: rgba(79, 244, 97, 0.14);
            --composer-tool-active-text: #4ff461;
            --composer-accent: #4ff461;
            --composer-accent-shadow: 0 18px 34px rgba(79, 244, 97, 0.16);
            --composer-preview-bg: #21293d;
        }

        #social-composer.composer-refresh {
            border-radius: 24px;
            background: var(--composer-surface);
            border: 1px solid var(--composer-border);
            box-shadow: var(--composer-shadow);
            overflow: hidden;
        }

        #social-composer.composer-refresh .composer-refresh__body {
            padding: 24px;
            background: transparent;
        }

        #social-composer.composer-refresh .composer-refresh__alert {
            margin-bottom: 16px;
            border-radius: 16px;
        }

        #social-composer.composer-refresh .composer-refresh__alert-list {
            margin: 0;
            padding-inline-start: 20px;
        }

        #social-composer.composer-refresh .composer-refresh__surface {
            padding: 22px;
            border-radius: 22px;
            background: var(--composer-panel-bg);
            border: 1px solid var(--composer-border);
        }

        #social-composer.composer-refresh .composer-refresh__surface--group {
            border-color: rgba(35, 210, 226, 0.22);
        }

        #social-composer.composer-refresh .composer-refresh__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        #social-composer.composer-refresh .composer-refresh__identity {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            color: var(--composer-text);
            text-decoration: none;
        }

        #social-composer.composer-refresh .composer-refresh__identity:hover {
            text-decoration: none;
        }

        #social-composer.composer-refresh .composer-refresh__context {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 18px;
            background: var(--composer-subtle-bg);
            border: 1px solid var(--composer-border);
        }

        #social-composer.composer-refresh .composer-refresh__context-copy {
            margin: 0;
            color: var(--composer-helper);
            font-size: 0.82rem;
            font-weight: 700;
        }

        #social-composer.composer-refresh .composer-refresh__avatar {
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
            border-radius: 50%;
            object-fit: cover;
            background: var(--composer-subtle-bg-strong);
            border: 2px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 20px rgba(94, 92, 154, 0.12);
        }

        #social-composer.composer-refresh .composer-refresh__identity-name {
            display: block;
            font-family: Rajdhani, sans-serif;
            font-size: 1.45rem;
            font-weight: 700;
            line-height: 1;
            color: var(--composer-text);
            text-transform: none;
            word-break: break-word;
        }

        #social-composer.composer-refresh .composer-refresh__focus-button {
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            border: 1px solid var(--composer-border);
            border-radius: 50%;
            background: var(--composer-subtle-bg);
            color: var(--composer-muted);
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease, border-color .2s ease, transform .2s ease;
        }

        #social-composer.composer-refresh .composer-refresh__focus-button:hover {
            background: var(--composer-tool-hover);
            color: var(--composer-tool-active-text);
            border-color: var(--composer-border-strong);
            transform: translateY(-1px);
        }

        #social-composer.composer-refresh .composer-refresh__editor {
            padding: 18px 20px;
            border-radius: 20px;
            background: var(--composer-subtle-bg);
            border: 1px solid transparent;
            transition: background-color .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        #social-composer.composer-refresh .composer-refresh__editor:focus-within {
            background: var(--composer-surface);
            border-color: var(--composer-border-strong);
            box-shadow: 0 12px 24px rgba(94, 92, 154, 0.08);
        }

        #social-composer.composer-refresh .composer-refresh__textarea {
            min-height: 140px;
            width: 100%;
            padding: 0;
            border: 0;
            background: transparent;
            color: var(--composer-text);
            font-size: 1.05rem;
            font-weight: 500;
            line-height: 1.7;
            resize: vertical;
        }

        #social-composer.composer-refresh .composer-refresh__textarea::placeholder {
            color: var(--composer-muted);
            font-weight: 400;
        }

        #social-composer.composer-refresh .composer-refresh__hint {
            margin: 14px 4px 0;
            color: var(--composer-helper);
            font-size: .82rem;
            line-height: 1.7;
            text-align: start;
        }

        #social-composer.composer-refresh .composer-refresh__panel {
            margin: 16px 0 0;
            border-radius: 20px;
            overflow: hidden;
            background: var(--composer-panel-bg);
            border: 1px solid var(--composer-border);
            box-shadow: none;
        }

        #social-composer.composer-refresh .composer-refresh__panel-content {
            padding: 20px 22px;
        }

        #social-composer.composer-refresh .composer-refresh__panel-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        #social-composer.composer-refresh .composer-refresh__panel-copy {
            min-width: 0;
        }

        #social-composer.composer-refresh .composer-refresh__panel-title {
            margin: 0;
            color: var(--composer-text);
            font-family: Rajdhani, sans-serif;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        #social-composer.composer-refresh .composer-refresh__panel-text {
            margin: 8px 0 0;
            color: var(--composer-muted);
            font-size: .92rem;
            line-height: 1.65;
        }

        #social-composer.composer-refresh .composer-refresh__ghost-button {
            min-height: 40px;
            padding: 0 18px;
            border: 1px solid var(--composer-border);
            border-radius: 999px;
            background: transparent;
            color: var(--composer-muted);
            font-family: Rajdhani, sans-serif;
            font-size: .9rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            cursor: pointer;
            transition: color .2s ease, background-color .2s ease, border-color .2s ease;
        }

        #social-composer.composer-refresh .composer-refresh__ghost-button:hover {
            color: var(--composer-tool-active-text);
            background: var(--composer-tool-active-bg);
            border-color: var(--composer-border-strong);
        }

        #social-composer.composer-refresh .composer-refresh__field + .composer-refresh__field,
        #social-composer.composer-refresh .composer-refresh__preview + .composer-refresh__options,
        #social-composer.composer-refresh .composer-refresh__options + .composer-refresh__directory-grid {
            margin-top: 16px;
        }

        #social-composer.composer-refresh .composer-refresh__label {
            display: block;
            margin-bottom: 8px;
            color: var(--composer-muted);
            font-family: Rajdhani, sans-serif;
            font-size: .84rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        #social-composer.composer-refresh .composer-refresh__control {
            width: 100%;
            min-height: 48px;
            padding: 0 16px;
            border-radius: 14px;
            border: 1px solid var(--composer-border);
            background: var(--composer-preview-bg);
            color: var(--composer-text);
            font-size: .95rem;
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        #social-composer.composer-refresh .composer-refresh__control::placeholder {
            color: var(--composer-muted);
        }

        #social-composer.composer-refresh .composer-refresh__control:focus {
            outline: none;
            border-color: var(--composer-border-strong);
            box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.08);
        }

        body[data-theme="css_d"] #social-composer.composer-refresh .composer-refresh__control:focus {
            box-shadow: 0 0 0 4px rgba(79, 244, 97, 0.08);
        }

        #social-composer.composer-refresh .composer-refresh__control--select {
            appearance: none;
            background-image: linear-gradient(45deg, transparent 50%, currentColor 50%), linear-gradient(135deg, currentColor 50%, transparent 50%);
            background-position: calc(100% - 22px) calc(50% - 3px), calc(100% - 16px) calc(50% - 3px);
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
            color: var(--composer-text);
        }

        html[dir="rtl"] #social-composer.composer-refresh .composer-refresh__control--select {
            background-position: 22px calc(50% - 3px), 16px calc(50% - 3px);
        }

        #social-composer.composer-refresh .composer-refresh__preview {
            margin-top: 16px;
        }

        #social-composer.composer-refresh .composer-refresh__preview .post-preview {
            margin: 0;
            border-radius: 18px;
            border: 1px solid var(--composer-border);
            background: var(--composer-preview-bg);
            box-shadow: none;
            overflow: hidden;
        }

        #social-composer.composer-refresh .composer-refresh__preview .post-preview-info.fixed-height {
            min-height: 100px;
        }

        #social-composer.composer-refresh .composer-refresh__preview .post-preview-title {
            color: var(--composer-text);
        }

        #social-composer.composer-refresh .composer-refresh__preview .post-preview-text,
        #social-composer.composer-refresh .composer-refresh__preview .post-preview-link {
            color: var(--composer-muted);
        }

        #social-composer.composer-refresh .composer-refresh__options {
            margin-top: 18px;
        }

        #social-composer.composer-refresh .composer-refresh__section-hint {
            margin: 0 0 12px;
            color: var(--composer-helper);
            font-size: .82rem;
            line-height: 1.7;
            text-align: start;
        }

        #social-composer.composer-refresh .composer-refresh__section-hint--gallery {
            margin-top: 14px;
            margin-bottom: 0;
        }

        #social-composer.composer-refresh .composer-refresh__directory-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 16px;
        }

        #social-composer.composer-refresh .composer-refresh__field--full {
            grid-column: 1 / -1;
        }

        #social-composer.composer-refresh .composer-refresh__gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
        }

        #social-composer.composer-refresh .composer-refresh__gallery-toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 12px;
        }

        #social-composer.composer-refresh .composer-refresh__gallery-grid figure {
            position: relative;
            margin: 0;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            border-radius: 16px;
            background: var(--composer-subtle-bg);
            border: 1px solid var(--composer-border);
        }

        #social-composer.composer-refresh .composer-refresh__gallery-grid img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #social-composer.composer-refresh .composer-refresh__gallery-remove {
            position: absolute;
            inset-block-start: 8px;
            inset-inline-end: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 50%;
            background: rgba(20, 23, 34, 0.72);
            color: #ffffff;
            cursor: pointer;
            transition: background-color .2s ease, transform .2s ease, border-color .2s ease;
        }

        #social-composer.composer-refresh .composer-refresh__gallery-remove:hover {
            background: rgba(233, 75, 95, 0.92);
            border-color: rgba(255, 255, 255, 0.3);
            transform: scale(1.04);
        }

        #social-composer.composer-refresh .composer-refresh__gallery-remove i {
            font-size: .88rem;
            line-height: 1;
        }

        #social-composer.composer-refresh .composer-refresh__ghost-button--danger:hover {
            color: #ffffff;
            background: #e94b5f;
            border-color: #e94b5f;
        }

        body[data-theme="css_d"] #social-composer.composer-refresh .composer-refresh__ghost-button--danger:hover {
            color: #111523;
            background: #ff6d85;
            border-color: #ff6d85;
        }

        #social-composer.composer-refresh .composer-choice-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        #social-composer.composer-refresh .composer-choice-card {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-height: 100%;
            padding: 16px;
            border: 1px solid var(--composer-border);
            border-radius: 18px;
            background: var(--composer-preview-bg);
            cursor: pointer;
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease, transform .2s ease;
        }

        #social-composer.composer-refresh .composer-choice-card:hover {
            border-color: var(--composer-border-strong);
            box-shadow: 0 14px 28px rgba(94, 92, 154, 0.1);
            transform: translateY(-1px);
        }

        #social-composer.composer-refresh .composer-choice-card.is-selected {
            border-color: var(--composer-border-strong);
            background: var(--composer-tool-active-bg);
            box-shadow: 0 16px 30px rgba(94, 92, 154, 0.12);
        }

        #social-composer.composer-refresh .composer-choice-input {
            position: absolute;
            inset-inline-start: 14px;
            top: 14px;
            opacity: 0;
            pointer-events: none;
        }

        #social-composer.composer-refresh .composer-choice-title {
            color: var(--composer-text);
            font-size: .92rem;
            font-weight: 700;
            line-height: 1.5;
        }

        #social-composer.composer-refresh .composer-choice-text {
            color: var(--composer-muted);
            font-size: .8rem;
            line-height: 1.65;
        }

        #social-composer.composer-refresh .composer-refresh__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 74px;
            padding: 14px 20px 18px;
            border-top: 1px solid var(--composer-border);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, var(--composer-subtle-bg) 100%);
        }

        body[data-theme="css_d"] #social-composer.composer-refresh .composer-refresh__footer {
            background: linear-gradient(180deg, rgba(31, 36, 54, 0) 0%, rgba(38, 45, 66, 0.8) 100%);
        }

        #social-composer.composer-refresh .composer-refresh__toolbar,
        #social-composer.composer-refresh .composer-refresh__submit-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #social-composer.composer-refresh .composer-refresh__toolbar {
            flex: 1 1 auto;
            flex-wrap: nowrap;
            min-width: 0;
        }

        #social-composer.composer-refresh .composer-refresh__toolbar .quick-post-footer-action {
            margin-right: 0;
        }

        #social-composer.composer-refresh .composer-refresh__tool {
            display: inline-flex;
            flex: 1 1 0;
            align-items: center;
            justify-content: center;
            min-width: 0;
            gap: 8px;
            min-height: 42px;
            padding: 0 14px;
            border: 1px solid var(--composer-border);
            border-radius: 999px;
            background: var(--composer-tool-bg);
            color: var(--composer-muted);
            transition: background-color .2s ease, color .2s ease, border-color .2s ease, transform .2s ease, box-shadow .2s ease;
        }

        #social-composer.composer-refresh .composer-refresh__tool:hover {
            background: var(--composer-tool-hover);
            color: var(--composer-tool-active-text);
            border-color: var(--composer-border-strong);
            box-shadow: 0 10px 20px rgba(94, 92, 154, 0.1);
            transform: translateY(-1px);
        }

        #social-composer.composer-refresh .composer-refresh__tool.is-active {
            background: var(--composer-tool-active-bg);
            color: var(--composer-tool-active-text);
            border-color: var(--composer-border-strong);
        }

        #social-composer.composer-refresh .composer-refresh__tool .quick-post-footer-action-icon {
            width: 16px;
            height: 16px;
            fill: currentColor;
            transition: fill .2s ease;
        }

        #social-composer.composer-refresh .composer-refresh__tool-label {
            font-family: Rajdhani, sans-serif;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: .82rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        #social-composer.composer-refresh .composer-refresh__submit {
            min-width: 96px;
            min-height: 42px;
            padding-inline: 24px;
            border-radius: 999px;
            box-shadow: var(--composer-accent-shadow);
        }

        @media screen and (max-width: 680px) {
            #social-composer.composer-refresh .composer-refresh__body {
                padding: 18px;
            }

            #social-composer.composer-refresh .composer-refresh__surface,
            #social-composer.composer-refresh .composer-refresh__panel-content {
                padding: 18px;
            }

            #social-composer.composer-refresh .composer-refresh__header,
            #social-composer.composer-refresh .composer-refresh__panel-row,
            #social-composer.composer-refresh .composer-refresh__footer {
                align-items: stretch;
                flex-direction: column;
            }

            #social-composer.composer-refresh .composer-refresh__focus-button {
                align-self: flex-end;
            }

            #social-composer.composer-refresh .composer-refresh__textarea {
                min-height: 120px;
            }

            #social-composer.composer-refresh .composer-refresh__directory-grid {
                grid-template-columns: 1fr;
            }

            #social-composer.composer-refresh .composer-refresh__toolbar,
            #social-composer.composer-refresh .composer-refresh__submit-wrap {
                width: 100%;
            }

            #social-composer.composer-refresh .composer-refresh__tool,
            #social-composer.composer-refresh .composer-refresh__submit {
                width: 100%;
            }

            #social-composer.composer-refresh .composer-refresh__tool {
                justify-content: flex-start;
            }
        }
    </style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('social-composer-form');
        if (!form) {
            return;
        }

        const composerRoot = document.getElementById('social-composer');
        const focusTrigger = document.getElementById('composer-focus-trigger');
        const postKind = document.getElementById('composer-post-kind');
        const repostStatusId = document.getElementById('composer-repost-status-id');
        const linkBlock = document.getElementById('composer-link-block');
        const linkInput = document.getElementById('composer-link-url');
        const linkPreview = document.getElementById('composer-link-preview');
        const publishOptions = document.getElementById('composer-publish-options');
        const galleryBlock = document.getElementById('composer-gallery-block');
        const galleryToolbar = document.getElementById('composer-gallery-toolbar');
        const galleryInput = document.getElementById('composer-images');
        const galleryClearButton = document.getElementById('composer-gallery-clear');
        const galleryGrid = document.getElementById('composer-gallery-grid');
        const directoryFields = document.getElementById('composer-directory-fields');
        const directoryName = document.getElementById('composer-directory-name');
        const repostCard = document.getElementById('composer-repost-card');
        const repostText = document.getElementById('composer-repost-text');
        const composerText = document.getElementById('composer-text');
        const modeTextButton = document.getElementById('composer-mode-text');
        const modeGalleryButton = document.getElementById('composer-mode-gallery');
        const modeLinkButton = document.getElementById('composer-mode-link');
        const repostCancelButton = document.getElementById('composer-repost-cancel');
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const publishModeInputs = Array.from(form.querySelectorAll('input[name="publish_mode"]'));
        const repostEnabled = @json($allowRepost);
        let previewTimeout = null;
        let previewRequestId = 0;
        let lastPreviewUrl = null;
        let autoDetectedLink = extractFirstUrl(composerText.value);
        let linkLockedToText = !linkInput || linkInput.value.trim() === '' || linkInput.value.trim() === autoDetectedLink;
        let manualLinkOpen = @json($oldPostKind === 'link' || $oldLinkUrl !== '' || $oldPublishMode === 'directory_only');
        let selectedGalleryFiles = [];

        function extractFirstUrl(text) {
            const match = String(text || '').match(/\b((https?:\/\/)?[a-z0-9.-]+\.[a-z]{2,}(\/\S*)?)/i);
            return match ? match[1] : '';
        }

        function selectedPublishMode() {
            if (publishModeInputs.length === 0) {
                return 'post';
            }

            const current = publishModeInputs.find(function (input) {
                return input.checked;
            });

            return current ? current.value : 'post';
        }

        function setPublishMode(value) {
            if (publishModeInputs.length === 0) {
                return;
            }

            publishModeInputs.forEach(function (input) {
                input.checked = input.value === value;
            });

            syncPublishChoiceState();
        }

        function syncPublishChoiceState() {
            publishModeInputs.forEach(function (input) {
                const card = input.closest('.composer-choice-card');

                if (!card) {
                    return;
                }

                card.classList.toggle('is-selected', input.checked);
            });
        }

        function toggleTool(button, active) {
            if (!button) {
                return;
            }

            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        }

        function syncToolStates() {
            const hasGallery = galleryInput ? (galleryInput.files || []).length > 0 : false;
            const linkActive = manualLinkOpen || hasResolvedLink() || autoDetectedLink !== '';
            const textActive = !hasGallery && !linkActive;

            toggleTool(modeTextButton, textActive);
            toggleTool(modeGalleryButton, hasGallery);
            toggleTool(modeLinkButton, linkActive);

            composerRoot.classList.toggle('has-gallery', hasGallery);
            composerRoot.classList.toggle('has-link', linkActive);
            composerRoot.classList.toggle('has-repost', repostEnabled && repostStatusId && repostStatusId.value !== '');
        }

        function clearLinkPreview() {
            if (!linkPreview) {
                return;
            }

            lastPreviewUrl = null;
            linkPreview.innerHTML = '';
            linkPreview.style.display = 'none';
        }

        function hasResolvedLink() {
            return !!linkInput && linkInput.value.trim() !== '';
        }

        function syncPostKind() {
            if (galleryInput && (galleryInput.files || []).length > 0) {
                postKind.value = 'gallery';
                return;
            }

            if (repostEnabled && repostStatusId && repostStatusId.value) {
                postKind.value = 'repost';
                return;
            }

            postKind.value = hasResolvedLink() ? 'link' : 'text';
        }

        function syncDirectoryFields() {
            const hasLink = hasResolvedLink();
            if (publishOptions) {
                publishOptions.style.display = hasLink ? 'block' : 'none';
            }

            if (!hasLink) {
                setPublishMode('post');
            }

            if (directoryFields) {
                directoryFields.style.display = hasLink && selectedPublishMode() === 'directory_only' ? 'grid' : 'none';
            }
        }

        function syncLinkBlockVisibility() {
            if (!linkBlock || !linkInput) {
                syncDirectoryFields();
                syncPostKind();
                syncToolStates();
                return;
            }

            const shouldShow = manualLinkOpen || hasResolvedLink() || autoDetectedLink !== '';
            linkBlock.style.display = shouldShow ? 'block' : 'none';

            if (!shouldShow) {
                clearLinkPreview();
            }

            syncDirectoryFields();
            syncPostKind();
            syncToolStates();
        }

        function syncGalleryInputFiles() {
            if (!galleryInput || typeof DataTransfer === 'undefined') {
                return;
            }

            const dataTransfer = new DataTransfer();

            selectedGalleryFiles.forEach(function (file) {
                dataTransfer.items.add(file);
            });

            galleryInput.files = dataTransfer.files;
        }

        function clearGallerySelection() {
            if (!galleryInput || !galleryGrid || !galleryToolbar || !galleryBlock) {
                return;
            }

            selectedGalleryFiles = [];
            galleryInput.value = '';
            galleryGrid.innerHTML = '';
            galleryToolbar.style.display = 'none';
            galleryBlock.style.display = 'none';
            syncPostKind();
            syncToolStates();
        }

        function removeGalleryImage(index) {
            selectedGalleryFiles = selectedGalleryFiles.filter(function (_, fileIndex) {
                return fileIndex !== index;
            });

            if (selectedGalleryFiles.length === 0) {
                clearGallerySelection();
                return;
            }

            syncGalleryInputFiles();
            renderGallery();
            syncLinkBlockVisibility();
        }

        function renderGallery() {
            if (!galleryInput || !galleryGrid || !galleryToolbar || !galleryBlock) {
                return;
            }

            galleryGrid.innerHTML = '';
            const files = selectedGalleryFiles;

            if (files.length === 0) {
                clearGallerySelection();
                return;
            }

            if (files.length > 10) {
                alert(@json(__('messages.gallery_limit_exceeded')));
                clearGallerySelection();
                return;
            }

            galleryBlock.style.display = 'block';
            galleryToolbar.style.display = 'flex';
            postKind.value = 'gallery';

            files.forEach(function (file, index) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const figure = document.createElement('figure');
                    const image = document.createElement('img');
                    const removeButton = document.createElement('button');

                    image.src = event.target.result;
                    image.alt = file.name;

                    removeButton.type = 'button';
                    removeButton.className = 'composer-refresh__gallery-remove';
                    removeButton.setAttribute('aria-label', @json(__('messages.delete')));
                    removeButton.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i>';
                    removeButton.addEventListener('click', function () {
                        removeGalleryImage(index);
                    });

                    figure.appendChild(image);
                    figure.appendChild(removeButton);
                    galleryGrid.appendChild(figure);
                };
                reader.readAsDataURL(file);
            });

            syncToolStates();
        }

        function renderLinkPreviewCard(data) {
            if (!linkPreview) {
                return;
            }

            linkPreview.innerHTML = `
                <a href="${data.url}" target="_blank" rel="noopener noreferrer" class="post-preview medium">
                    <figure class="post-preview-image liquid" style="background: url(${data.image_url || '{{ theme_asset('img/dir_image.png') }}'}) center center / cover no-repeat;"></figure>
                    <div class="post-preview-info fixed-height">
                        <p class="post-preview-title">${data.title || data.domain || data.url}</p>
                        <p class="post-preview-text">${data.description || ''}</p>
                        <p class="post-preview-link">${data.domain || ''}</p>
                    </div>
                </a>
            `;
            linkPreview.style.display = 'block';

            if (directoryName && !directoryName.value.trim()) {
                directoryName.value = data.title || data.domain || '';
            }
        }

        function fetchLinkPreview() {
            if (!linkInput) {
                return;
            }

            const value = linkInput.value.trim();
            if (!value || !csrfToken) {
                clearLinkPreview();
                return;
            }

            if (lastPreviewUrl === value) {
                return;
            }

            lastPreviewUrl = value;
            const requestId = ++previewRequestId;

            fetch('{{ route('status.link_preview') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify({ link_url: value })
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (requestId !== previewRequestId) {
                    return;
                }

                renderLinkPreviewCard(data);
            })
            .catch(function () {
                if (requestId !== previewRequestId) {
                    return;
                }

                clearLinkPreview();
            });
        }

        function scheduleLinkPreview() {
            clearTimeout(previewTimeout);

            if (!hasResolvedLink()) {
                clearLinkPreview();
                return;
            }

            previewTimeout = setTimeout(fetchLinkPreview, 400);
        }

        function syncAutoDetectedLink() {
            if (!linkInput) {
                syncPostKind();
                syncToolStates();
                return;
            }

            const nextDetected = extractFirstUrl(composerText.value);
            const current = linkInput.value.trim();
            const shouldReplace = linkLockedToText || current === '' || current === autoDetectedLink;

            autoDetectedLink = nextDetected;

            if (shouldReplace) {
                linkInput.value = autoDetectedLink;
                linkLockedToText = true;
            }

            syncLinkBlockVisibility();
            scheduleLinkPreview();
        }

        if (focusTrigger) {
            focusTrigger.addEventListener('click', function () {
                composerText.focus();
            });
        }

        if (modeTextButton) {
            modeTextButton.addEventListener('click', function () {
                clearGallerySelection();
                manualLinkOpen = false;

                if (linkInput) {
                    if (autoDetectedLink) {
                        linkInput.value = autoDetectedLink;
                        linkLockedToText = true;
                    } else {
                        linkInput.value = '';
                        clearLinkPreview();
                    }
                }

                syncLinkBlockVisibility();
                composerText.focus();
            });
        }

        if (modeGalleryButton && galleryInput) {
            modeGalleryButton.addEventListener('click', function () {
                galleryInput.click();
            });
        }

        if (modeLinkButton && linkInput) {
            modeLinkButton.addEventListener('click', function () {
                manualLinkOpen = true;

                if (!linkInput.value.trim() && autoDetectedLink) {
                    linkInput.value = autoDetectedLink;
                    linkLockedToText = true;
                }

                syncLinkBlockVisibility();
                linkInput.focus();
            });
        }

        if (galleryInput) {
            galleryInput.addEventListener('change', function () {
                selectedGalleryFiles = Array.from(galleryInput.files || []);
                renderGallery();
                syncLinkBlockVisibility();
            });
        }

        if (galleryClearButton) {
            galleryClearButton.addEventListener('click', function () {
                clearGallerySelection();
                syncLinkBlockVisibility();
            });
        }

        composerText.addEventListener('input', function () {
            syncAutoDetectedLink();
        });

        if (linkInput) {
            linkInput.addEventListener('input', function () {
                manualLinkOpen = true;
                linkLockedToText = this.value.trim() === '' || this.value.trim() === autoDetectedLink;
                syncLinkBlockVisibility();
                scheduleLinkPreview();
            });
        }

        publishModeInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                syncPublishChoiceState();
                syncDirectoryFields();
            });
        });

        if (repostCancelButton && repostStatusId && repostCard && repostText) {
            repostCancelButton.addEventListener('click', function () {
                repostStatusId.value = '';
                repostCard.style.display = 'none';
                repostText.textContent = @json(__('messages.repost_ready'));
                syncPostKind();
                syncToolStates();
            });
        }

        form.addEventListener('submit', function () {
            if (!hasResolvedLink()) {
                setPublishMode('post');
            }

            syncPostKind();
        });

        window.openRepostComposer = function (statusId, authorName, excerpt) {
            if (!repostEnabled || !repostStatusId || !repostCard || !repostText) {
                return;
            }

            repostStatusId.value = statusId;
            postKind.value = 'repost';
            repostCard.style.display = 'block';
            repostText.textContent = (authorName ? authorName + ': ' : '') + (excerpt || @json(__('messages.repost_ready')));
            syncToolStates();
            composerText.focus();
            window.scrollTo({ top: form.getBoundingClientRect().top + window.scrollY - 120, behavior: 'smooth' });
        };

        if (repostEnabled && repostStatusId && repostCard && repostStatusId.value) {
            repostCard.style.display = 'block';
        }

        if (hasResolvedLink()) {
            manualLinkOpen = manualLinkOpen || !autoDetectedLink || linkInput.value.trim() !== autoDetectedLink;
        }

        renderGallery();
        syncAutoDetectedLink();
        syncPublishChoiceState();
        syncToolStates();
    });
</script>
@endpush
@endauth
