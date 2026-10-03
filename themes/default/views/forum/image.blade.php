@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
@php
    $group = $group ?? null;
    $showForumRoleBadges = (int) ($forumSettings['show_role_badges'] ?? 1) === 1;
    $topicCategoryId = (int) $topic->cat;
    $groupAccess = app(\App\Services\GroupAccessService::class);
    $canManageGroupTopic = $group && auth()->check() ? $groupAccess->canManageGroup($group, auth()->user()) : false;
    $canEditTopic = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || $canManageGroupTopic
        || auth()->user()->canModerateForum('edit_topics', $topicCategoryId)
    );
    $canDeleteTopic = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || $canManageGroupTopic
        || auth()->user()->canModerateForum('delete_topics', $topicCategoryId)
    );
    $canPinTopic = auth()->check() && ($canManageGroupTopic || auth()->user()->canModerateForum('pin_topics', $topicCategoryId));
    $canLockTopic = auth()->check() && ($canManageGroupTopic || auth()->user()->canModerateForum('lock_topics', $topicCategoryId));
    $canCommentWhenLocked = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || $canManageGroupTopic
        || auth()->user()->canModerateForum('lock_topics', $topicCategoryId)
    );

    // Eagerly collected image attachments
    $imageAttachments = $imageAttachments ?? $topic->attachments->filter(fn($att) => $att->isImage())->sortBy('sort_order')->values();
    $nonImageAttachments = $topic->attachments->filter(fn($att) => !$att->isImage())->sortBy('sort_order')->values();
    $totalImagesCount = $imageAttachments->count();

    // Primary image fallback
    if ($totalImagesCount === 0 && !empty($primaryImage)) {
        $galleryImages = collect([
            (object) [
                'id' => 0,
                'file_path' => $primaryImage,
                'original_name' => $topic->name ?: __('messages.photo'),
                'human_size' => '',
                'url' => $primaryImage,
                'is_single_fallback' => true,
            ]
        ]);
        $totalImagesCount = 1;
    } else {
        $galleryImages = $imageAttachments->map(function($att) {
            return (object) [
                'id' => $att->id,
                'file_path' => $att->file_path,
                'original_name' => $att->original_name,
                'human_size' => $att->human_size,
                'url' => asset($att->file_path),
                'is_single_fallback' => false,
            ];
        });
    }

    $leadImage = $galleryImages->first();
    $isSaved = $isSaved ?? false;
    $isFollowing = $isFollowing ?? false;
    $authorGalleries = $authorGalleries ?? collect();
    $suggestedGalleries = $suggestedGalleries ?? collect();

    // Prepare JSON for Lightbox
    $lightboxImagesJson = json_encode($galleryImages->map(function($img, $idx) use ($topic) {
        return [
            'index' => $idx,
            'id' => $img->id,
            'url' => $img->url,
            'title' => $img->original_name ?: $topic->name,
            'size' => $img->human_size ?? '',
        ];
    })->values());

    // Structured JSON-LD Schema (Built via PHP to prevent Blade directive collision on @context)
    $schemaData = [
        '@context' => 'https://schema.org',
        '@type' => 'ImageGallery',
        'name' => $topic->name ?: __('messages.photos_gallery'),
        'headline' => $topic->name ?: __('messages.photos_gallery'),
        'description' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $topic->txt))), 200, ''),
        'url' => route('forum.topic', $topic->id),
        'datePublished' => \Carbon\Carbon::createFromTimestamp($status->date)->toIso8601String(),
        'dateModified' => \Carbon\Carbon::createFromTimestamp($topic->date ?: $status->date)->toIso8601String(),
        'author' => [
            '@type' => 'Person',
            'name' => $topic->user?->username ?? 'User',
            'url' => $topic->user ? route('profile.show', $topic->user->username) : url('/'),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('app.name', 'MyAds'),
            'url' => url('/'),
        ],
        'image' => $galleryImages->map(function($gImg) use ($topic) {
            return [
                '@type' => 'ImageObject',
                'contentUrl' => $gImg->url,
                'thumbnail' => $gImg->url,
                'name' => $gImg->original_name ?: $topic->name,
            ];
        })->values()->all(),
        'interactionStatistic' => [
            [
                '@type' => 'InteractionCounter',
                'interactionType' => 'https://schema.org/LikeAction',
                'userInteractionCount' => (int) $topic->likes()->count(),
            ],
            [
                '@type' => 'InteractionCounter',
                'interactionType' => 'https://schema.org/CommentAction',
                'userInteractionCount' => (int) $topic->comments()->count(),
            ],
        ],
    ];

    $authorGalleriesCount = \App\Models\Status::where('uid', $topic->uid)->where('s_type', 4)->count();
    $authorFollowersCount = \App\Models\Like::where('sid', $topic->uid)->where('type', 1)->count();
    $authorTotalPostsCount = \App\Models\Status::where('uid', $topic->uid)->count();
@endphp

<!-- STRUCTURED DATA: JSON-LD FOR SEARCH ENGINES (SEO) -->
<script type="application/ld+json">
{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

<div class="forum-rdx forum-rdx-gallery-page">
    <!-- TOP ADS -->
    @include('theme::partials.ads', ['id' => 5])

    @if($group)
        <div class="gallery-group-banner mb-3">
            @include('theme::partials.groups.badge', ['groupBadge' => $group])
        </div>
    @endif

    <div class="row g-4 gallery-main-row">
        <!-- PRIMARY CONTENT COLUMN (Post + Showcase + Actions + Comments) -->
        <div class="col-lg-8 col-xl-8">
            <article class="gallery-card-shell shadow-sm post{{ $status->id }}" id="galleryCardShell">
                <!-- 1. AUTHOR & META HEADER -->
                <header class="gallery-card-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                        <!-- CREATOR INFO -->
                        <div class="d-flex align-items-center gap-3">
                            <a class="gallery-avatar-link" href="{{ route('profile.show', $topic->user->username) }}">
                                <div class="gallery-user-avatar {{ $topic->user->isOnline() ? 'online' : 'offline' }}">
                                    <img src="{{ $topic->user ? $topic->user->avatarUrl() : asset('upload/_avatar.png') }}"
                                         alt="{{ $topic->user->username }}"
                                         class="gallery-avatar-img">
                                    @if($topic->user->hasVerifiedBadge())
                                        <span class="gallery-verified-badge" title="حساب موثق">
                                            <i class="fa fa-check"></i>
                                        </span>
                                    @endif
                                </div>
                            </a>

                            <div class="gallery-header-meta">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a class="gallery-author-name" href="{{ route('profile.show', $topic->user->username) }}">
                                        {{ $topic->user->username }}
                                    </a>

                                    @if($showForumRoleBadges && $topic->user->forumRoleLabel($topicCategoryId))
                                        <span class="gallery-role-pill">
                                            {{ $topic->user->forumRoleLabel($topicCategoryId) }}
                                        </span>
                                    @endif

                                    @if($topic->is_pinned)
                                        <span class="gallery-badge-pill badge-pinned">
                                            <i class="fa fa-thumb-tack"></i> {{ __('messages.pinned') }}
                                        </span>
                                    @endif
                                    @if($topic->is_locked)
                                        <span class="gallery-badge-pill badge-locked">
                                            <i class="fa fa-lock"></i> {{ __('messages.locked') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="gallery-submeta d-flex align-items-center gap-2">
                                    <span class="gallery-time" title="{{ \Carbon\Carbon::createFromTimestamp($status->date)->toDayDateTimeString() }}">
                                        <i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}
                                    </span>
                                    <span class="gallery-meta-dot">•</span>
                                    @if($topic->category)
                                        <a href="{{ route('forum.category', $topic->cat) }}" class="gallery-category-pill">
                                            <i class="fa fa-folder-open-o"></i> {{ $topic->category->name }}
                                        </a>
                                        <span class="gallery-meta-dot">•</span>
                                    @endif
                                    <span class="gallery-photos-pill" id="galleryPhotoBadge">
                                        <i class="fa fa-camera"></i> <span id="galleryPhotoCount">{{ $totalImagesCount }}</span> {{ $totalImagesCount == 1 ? (Lang::has('messages.photo') ? __('messages.photo') : 'صورة') : (Lang::has('messages.photos') ? __('messages.photos') : 'صور') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- ACTIONS: FOLLOW + MENU -->
                        <div class="d-flex align-items-center gap-2">
                            @auth
                                @if((int) auth()->id() !== (int) $topic->uid)
                                    <button type="button"
                                            class="btn-gallery-follow {{ $isFollowing ? 'is-following' : '' }}"
                                            id="btnGalleryFollow"
                                            onclick="toggleAjaxFollow('{{ $topic->user->username }}', this)">
                                        <i class="fa {{ $isFollowing ? 'fa-check' : 'fa-user-plus' }}"></i>
                                        <span class="follow-label">{{ $isFollowing ? __('messages.following') : __('messages.follow') }}</span>
                                    </button>
                                @endif
                            @endauth

                            <!-- POST OPTIONS DROPDOWN (3 DOTS) -->
                            <div class="gallery-dropdown-wrap position-relative">
                                <button type="button"
                                        class="btn-gallery-icon"
                                        onclick="toggleGalleryMenu('galleryOptionsDropdown')"
                                        title="{{ __('messages.options') ?? 'خيارات' }}">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>

                                <div class="gallery-menu-dropdown shadow-lg" id="galleryOptionsDropdown" style="display: none;">
                                    @if($canEditTopic)
                                        @if((int) $topic->cat === 0)
                                            <button type="button" class="gallery-menu-item" onclick="postEdit({{ $topic->id }}, {{ $status->s_type }})">
                                                <i class="fa fa-edit text-primary"></i> {{ __('messages.edit') }}
                                            </button>
                                        @else
                                            <a class="gallery-menu-item text-decoration-none" href="{{ route('forum.edit', $topic->id) }}">
                                                <i class="fa fa-edit text-primary"></i> {{ __('messages.edit') }}
                                            </a>
                                        @endif
                                    @endif

                                    @if($canDeleteTopic)
                                        <button type="button" class="gallery-menu-item text-danger" onclick="deletePost({{ $topic->id }}, 4)">
                                            <i class="fa fa-trash"></i> {{ __('messages.delete') }}
                                        </button>
                                        <button type="button" class="gallery-menu-item text-danger" onclick="ajaxClearGallery({{ $topic->id }})">
                                            <i class="fa fa-trash-o"></i> {{ __('messages.clear_gallery') ?? 'تفريغ كافة الصور' }}
                                        </button>
                                    @endif

                                    @include('theme::partials.activity.promotion_link', ['activity' => $status])

                                    @if($canPinTopic && $topic->cat > 0)
                                        <form method="POST" action="{{ route('forum.pin', $topic->id) }}">
                                            @csrf
                                            <button type="submit" class="gallery-menu-item w-100 text-start border-0 bg-transparent">
                                                <i class="fa fa-thumb-tack text-warning"></i>
                                                {{ $topic->is_pinned ? __('messages.unpin_topic') : __('messages.pin_topic') }}
                                            </button>
                                        </form>
                                    @endif

                                    @if($canLockTopic && $topic->cat > 0)
                                        <form method="POST" action="{{ route('forum.lock', $topic->id) }}">
                                            @csrf
                                            <button type="submit" class="gallery-menu-item w-100 text-start border-0 bg-transparent">
                                                <i class="fa {{ $topic->is_locked ? 'fa-unlock text-success' : 'fa-lock text-secondary' }}"></i>
                                                {{ $topic->is_locked ? __('messages.unlock_topic') : __('messages.lock_topic') }}
                                            </button>
                                        </form>
                                    @endif

                                    <div class="gallery-menu-divider"></div>

                                    <button type="button" class="gallery-menu-item" onclick="reportPost({{ $topic->id }}, 2)">
                                        <i class="fa fa-flag text-danger"></i> {{ __('messages.report') }}
                                    </button>
                                    <button type="button" class="gallery-menu-item" onclick="reportUser({{ $topic->uid }})">
                                        <i class="fa fa-user-times text-danger"></i> {{ __('messages.report') }} {{ __('messages.author') }}
                                    </button>
                                    <button type="button" class="gallery-menu-item" onclick="copyPostLink('{{ route('forum.topic', $topic->id) }}')">
                                        <i class="fa fa-link text-info"></i> {{ __('messages.copy_link') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOPIC TITLE -->
                    @if(!empty($topic->name) && !in_array(strtolower(trim($topic->name)), ['gallery', 'image', 'photos', 'photo']))
                        <h1 class="gallery-post-title mt-3 mb-1">{{ $topic->name }}</h1>
                    @endif

                    <!-- FORMATTED DESCRIPTION / TEXT -->
                    @if(!empty($topic->txt))
                        <div class="gallery-post-caption mt-2" id="galleryCaptionWrap">
                            <div class="gallery-caption-content textpost" id="galleryCaptionText">
                                {!! \App\Support\ContentFormatter::formatForum($topic->txt) !!}
                            </div>
                            <button type="button" class="btn-caption-toggle d-none" id="btnCaptionToggle" onclick="toggleCaptionExpand()">
                                {{ __('messages.show_more') ?? 'عرض المزيد' }}
                            </button>
                        </div>
                    @endif
                </header>

                <!-- 2. HIGH-IMPACT IMMERSIVE PHOTO SHOWCASE (STAGE) -->
                <div class="gallery-stage-container" id="galleryStageContainer">
                    @if($totalImagesCount > 0)
                        <div class="gallery-mosaic-grid gallery-count-{{ min($totalImagesCount, 5) }}" id="galleryMosaicGrid">
                            @foreach($galleryImages as $index => $image)
                                @php
                                    $isLead = $index === 0;
                                    $isExtra = $index === 4 && $totalImagesCount > 5;
                                    $remainingExtra = $totalImagesCount - 5;
                                @endphp
                                <div class="gallery-item-tile item-pos-{{ $index }} {{ $isExtra ? 'tile-has-extra' : '' }}"
                                     data-index="{{ $index }}"
                                     data-id="{{ $image->id }}"
                                     id="galleryItem_{{ $image->id }}">
                                    <!-- AMBIENT GLOW ON LEAD -->
                                    @if($isLead && $totalImagesCount === 1)
                                        <div class="gallery-ambient-glow" style="background-image: url('{{ $image->url }}');"></div>
                                    @endif

                                    <div class="gallery-img-wrapper" onclick="openGalleryLightbox({{ $index }})">
                                        <img src="{{ $image->url }}"
                                             alt="{{ $image->original_name ?: $topic->name }}"
                                             class="gallery-tile-img"
                                             loading="{{ $isLead ? 'eager' : 'lazy' }}"
                                             fetchpriority="{{ $isLead ? 'high' : 'auto' }}"
                                             decoding="async">

                                        <!-- HOVER OVERLAY -->
                                        <div class="gallery-tile-overlay">
                                            <div class="tile-action-badge">
                                                <i class="fa fa-expand"></i>
                                            </div>
                                            <span class="tile-number-badge">{{ $index + 1 }} / {{ $totalImagesCount }}</span>
                                        </div>

                                        <!-- +N MORE OVERLAY BADGE (IF > 5 IMAGES) -->
                                        @if($isExtra)
                                            <div class="gallery-extra-overlay">
                                                <span class="extra-count-text">+{{ $remainingExtra + 1 }}</span>
                                                <span class="extra-count-label">{{ __('messages.more_photos') ?? 'صور إضافية' }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- OWNER PHOTO CONTROLS (DELETE & DRAG) -->
                                    @if($canEditTopic && !$image->is_single_fallback)
                                        <div class="gallery-tile-tools">
                                            <button type="button"
                                                    class="btn-tile-tool btn-tile-delete"
                                                    onclick="ajaxDeleteGalleryImage({{ $image->id }}, this, event)"
                                                    title="{{ Lang::has('messages.delete') ? __('messages.delete') : 'حذف' }}">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                            @if($totalImagesCount > 1)
                                                <span class="btn-tile-tool btn-tile-drag" title="{{ Lang::has('messages.drag_to_reorder') ? __('messages.drag_to_reorder') : 'اسحب للترتيب' }}">
                                                    <i class="fa fa-arrows"></i>
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Cap displayed tiles at 5 for the mosaic, remaining accessible via lightbox or view all --}}
                                @if($index >= 4 && $totalImagesCount > 5)
                                    @break
                                @endif
                            @endforeach
                        </div>
                    @else
                        <!-- EMPTY STATE -->
                        <div class="gallery-empty-state" id="galleryEmptyState">
                            <div class="empty-icon-circle">
                                <i class="fa fa-picture-o"></i>
                            </div>
                            <h4 class="mt-3 fw-bold">{{ __('messages.no_photos_yet') ?? 'لا توجد صور في هذا المعرض بعد' }}</h4>
                            <p class="text-muted small">{{ __('messages.add_photos_to_showcase') ?? 'يمكن لكاتب المنشور إضافة ما يصل إلى 10 صور عالية الدقة.' }}</p>
                        </div>
                    @endif

                    <!-- QUICK STAGE TOOLBAR (FULLSCREEN SLIDESHOW TRIGGER + VIEW SWITCHER) -->
                    @if($totalImagesCount > 0)
                        <div class="gallery-quick-toolbar">
                            <div class="gallery-toolbar-group">
                                <button type="button" class="btn-gallery-chip" onclick="openGalleryLightbox(0)">
                                    <i class="fa fa-arrows-alt text-primary"></i>
                                    <span>{{ Lang::has('messages.view_slideshow') ? __('messages.view_slideshow') : 'عرض ملء الشاشة' }}</span>
                                </button>
                                <span class="gallery-chip-badge">{{ $totalImagesCount }} {{ $totalImagesCount == 1 ? (Lang::has('messages.photo') ? __('messages.photo') : 'صورة') : (Lang::has('messages.photos') ? __('messages.photos') : 'صور') }}</span>
                            </div>

                            @if($canEditTopic && $totalImagesCount < 10)
                                <div class="gallery-toolbar-group">
                                    <label for="ajaxAddPhotosInput" class="btn-gallery-chip btn-gallery-chip-accent mb-0" id="btnUploadGalleryLabel">
                                        <i class="fa fa-plus"></i>
                                        <span>{{ Lang::has('messages.add_photos') ? __('messages.add_photos') : 'إضافة صور' }} (<span id="galleryRemainingSlot">{{ 10 - $totalImagesCount }}</span>)</span>
                                    </label>
                                    <input type="file"
                                           id="ajaxAddPhotosInput"
                                           multiple
                                           accept="image/*"
                                           style="display: none;"
                                           onchange="ajaxUploadGalleryImages(this, {{ $topic->id }})">
                                </div>
                            @endif
                        </div>
                    @elseif($canEditTopic)
                        <div class="p-3 text-center">
                            <label for="ajaxAddPhotosInput" class="btn btn-primary rounded-pill px-4" id="btnUploadGalleryLabel">
                                <i class="fa fa-cloud-upload me-2"></i> {{ Lang::has('messages.upload_photos') ? __('messages.upload_photos') : 'رفع صور للمعرض' }}
                            </label>
                            <input type="file"
                                   id="ajaxAddPhotosInput"
                                   multiple
                                   accept="image/*"
                                   style="display: none;"
                                   onchange="ajaxUploadGalleryImages(this, {{ $topic->id }})">
                        </div>
                    @endif

                    <!-- UPLOAD PROGRESS BAR (AJAX) -->
                    <div class="gallery-upload-progress d-none" id="galleryUploadProgress">
                        <div class="d-flex justify-content-between align-items-center mb-1 small">
                            <span class="fw-bold text-primary">
                                <i class="fa fa-spinner fa-spin me-1"></i> {{ __('messages.uploading_photos') ?? 'جاري رفع ومعالجة الصور...' }}
                            </span>
                            <span id="galleryUploadPercent">0%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                 id="galleryUploadBar"
                                 role="progressbar"
                                 style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- NON-IMAGE ATTACHMENTS (FILES/DOCS ATTACHED ALONG WITH GALLERY) -->
                @if($nonImageAttachments->isNotEmpty())
                    <div class="gallery-attachments-list px-4 py-2 border-top">
                        <p class="small text-muted fw-bold mb-2">
                            <i class="fa fa-paperclip me-1"></i> {{ __('messages.attachments') ?? 'مرفقات أخرى' }} ({{ $nonImageAttachments->count() }}):
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($nonImageAttachments as $att)
                                <a href="{{ route('forum.attachment.download', $att->id) }}" class="attachment-file-chip">
                                    <i class="fa fa-file-text-o text-primary"></i>
                                    <span class="attachment-name">{{ $att->original_name }}</span>
                                    <span class="attachment-size">({{ $att->human_size }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 3. INTERACTIVE SOCIAL ACTION BAR -->
                <div class="gallery-action-bar">
                    <!-- LEFT ACTIONS: REACTIONS, COMMENTS, BOOKMARK, SHARE -->
                    <div class="gallery-actions-left">
                        <!-- REACTIONS FLYOUT BUTTON -->
                        @auth
                            <div class="gallery-flyout-wrap position-relative">
                                <button type="button"
                                        class="btn-gallery-action reaction-trigger-btn"
                                        onclick="toggleReactionDropdown(this)">
                                    <div id="reaction_image{{ $status->id }}" class="d-inline-flex align-items-center gap-2">
                                        @if($viewerReaction)
                                            <img class="reaction-active-img"
                                                 src="{{ theme_asset('img/reaction/'.$viewerReactionType.'.png') }}"
                                                 width="22"
                                                 alt="{{ $viewerReactionType }}">
                                            <span class="reaction_txt{{ $status->id }} fw-bold text-info">
                                                {{ ucfirst($viewerReactionType) }}
                                            </span>
                                        @else
                                            <i class="fa fa-thumbs-up text-primary"></i>
                                            <span class="reaction_txt{{ $status->id }}">
                                                {{ Lang::has('messages.react') ? __('messages.react') : 'تفاعل' }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="action-count-badge" id="reactionsCountBadge">
                                        {{ $topic->likes()->count() }}
                                    </span>
                                </button>

                                <!-- REACTION EMOJIS FLYOUT -->
                                <div class="reaction-options reaction-options-dropdown shadow-lg"
                                     id="reactionDropdown{{ $topic->id }}"
                                     style="position: absolute; z-index: 99999; bottom: 48px; inset-inline-start: 0; display: none;">
                                    @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
                                        <div class="reaction-option text-tooltip-tft reaction_4_{{ $topic->id }}"
                                             data-title="{{ $reaction }}"
                                             onclick="ajaxPostReaction({{ $topic->id }}, '{{ $reaction }}', {{ $status->id }})">
                                            <img class="reaction-option-image"
                                                 src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}"
                                                 alt="{{ $reaction }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <button type="button" class="btn-gallery-action" onclick="showToast('يرجى تسجيل الدخول للتفاعل')">
                                <i class="fa fa-thumbs-up text-primary"></i>
                                <span>{{ Lang::has('messages.reactions') ? __('messages.reactions') : 'تفاعل' }}</span>
                                <span class="action-count-badge">{{ $topic->likes()->count() }}</span>
                            </button>
                        @endauth

                        <!-- COMMENT BUTTON -->
                        <button type="button" class="btn-gallery-action" onclick="focusCommentSection({{ $topic->id }})">
                            <i class="fa fa-commenting-o text-success"></i>
                            <span>{{ Lang::has('messages.comment') ? __('messages.comment') : 'تعليق' }}</span>
                            <span class="action-count-badge">{{ $topic->comments()->count() }}</span>
                        </button>

                        <!-- BOOKMARK / SAVE (AJAX) -->
                        @auth
                            <button type="button"
                                    class="btn-gallery-action {{ $isSaved ? 'is-saved' : '' }}"
                                    id="btnSaveGallery"
                                    onclick="ajaxToggleSaveStatus({{ $status->id }}, this)">
                                <i class="fa {{ $isSaved ? 'fa-bookmark text-primary' : 'fa-bookmark-o' }}"></i>
                                <span class="save-btn-label">{{ $isSaved ? (Lang::has('messages.saved') ? __('messages.saved') : 'محفوظ') : (Lang::has('messages.save') ? __('messages.save') : 'حفظ') }}</span>
                            </button>
                        @endauth

                        <!-- SHARE FLYOUT -->
                        <div class="gallery-flyout-wrap position-relative">
                            <button type="button"
                                    class="btn-gallery-action"
                                    onclick="toggleGalleryMenu('shareFlyoutMenu')">
                                <i class="fa fa-share-alt text-warning"></i>
                                <span>{{ Lang::has('messages.share') ? __('messages.share') : 'مشاركة' }}</span>
                            </button>

                            <div class="gallery-share-flyout shadow-lg" id="shareFlyoutMenu" style="display: none;">
                                <div class="share-flyout-header small text-muted px-3 py-2 border-bottom">
                                    {{ Lang::has('messages.share_post') ? __('messages.share_post') : 'مشاركة المعرض' }}
                                </div>
                                <div class="p-2 d-flex flex-column gap-1">
                                    <button type="button" class="gallery-menu-item" onclick="copyPostLink('{{ route('forum.topic', $topic->id) }}'); toggleGalleryMenu('shareFlyoutMenu');">
                                        <i class="fa fa-link text-primary"></i> {{ Lang::has('messages.copy_link') ? __('messages.copy_link') : 'نسخ الرابط' }}
                                    </button>
                                    @php
                                        $shareUrl = route('forum.topic', $topic->id);
                                        $shareTitle = $topic->name ?: config('app.name');
                                    @endphp
                                    <button type="button" class="gallery-menu-item" onclick="sharePost('twitter', '{{ $shareUrl }}', '{{ e($shareTitle) }}'); toggleGalleryMenu('shareFlyoutMenu');">
                                        <i class="fa fa-twitter text-info"></i> تويتر (X)
                                    </button>
                                    <button type="button" class="gallery-menu-item" onclick="sharePost('facebook', '{{ $shareUrl }}', '{{ e($shareTitle) }}'); toggleGalleryMenu('shareFlyoutMenu');">
                                        <i class="fa fa-facebook text-primary"></i> فيسبوك
                                    </button>
                                    <button type="button" class="gallery-menu-item" onclick="sharePost('telegram', '{{ $shareUrl }}', '{{ e($shareTitle) }}'); toggleGalleryMenu('shareFlyoutMenu');">
                                        <i class="fa fa-paper-plane text-info"></i> تيليجرام
                                    </button>
                                    <button type="button" class="gallery-menu-item" onclick="sharePost('whatsapp', '{{ $shareUrl }}', '{{ e($shareTitle) }}'); toggleGalleryMenu('shareFlyoutMenu');">
                                        <i class="fa fa-whatsapp text-success"></i> واتساب
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT ACTIONS: TOTAL VIEWS -->
                    <div class="gallery-actions-right text-muted small">
                        <span class="gallery-views-stat" title="عدد المشاهدات">
                            <i class="fa fa-eye me-1"></i> {{ number_format($topic->vu ?? 0) }}
                        </span>
                    </div>
                </div>

                <!-- 4. COMMENTS SECTION (INTEGRATED AJAX) -->
                <div class="gallery-comments-section" id="galleryCommentsContainer">
                    <div class="comments-section-header d-flex align-items-center justify-content-between mb-3">
                        <h3 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="fa fa-comments text-primary"></i>
                            <span>{{ __('messages.comments') }} (<span id="commentsCountHeader">{{ $topic->comments()->count() }}</span>)</span>
                        </h3>
                    </div>

                    <div class="post-comment-list post-comment-list-{{ $topic->id }} comment_4_{{ $topic->id }}">
                        @include('theme::partials.activity.comments', [
                            'comments' => $topic->comments()->orderBy('id', 'desc')->get(),
                            'id' => $topic->id,
                            'type' => 'forum',
                            'limit' => 100,
                            'hide_form' => $topic->is_locked && !$canCommentWhenLocked,
                            'locked_topic' => (bool) $topic->is_locked,
                            'forum_category_id' => $topicCategoryId
                        ])
                    </div>
                </div>
            </article>
        </div>

        <!-- SIDEBAR COLUMN (Creator Bio, Author Galleries, Suggested Galleries, Ads) -->
        <aside class="col-lg-4 col-xl-4 gallery-sidebar-col">
            <!-- 1. CREATOR SPOTLIGHT CARD -->
            <div class="gallery-sidebar-card shadow-sm mb-4">
                <div class="creator-card-cover"></div>
                <div class="creator-card-body text-center">
                    <a href="{{ route('profile.show', $topic->user->username) }}" class="creator-avatar-wrap">
                        <img src="{{ $topic->user ? $topic->user->avatarUrl() : asset('upload/_avatar.png') }}"
                             alt="{{ $topic->user->username }}"
                             class="creator-big-avatar">
                        @if($topic->user->hasVerifiedBadge())
                            <span class="creator-verified-badge" title="موثق">
                                <i class="fa fa-check"></i>
                            </span>
                        @endif
                    </a>

                    <h4 class="creator-name mt-2 mb-0">
                        <a href="{{ route('profile.show', $topic->user->username) }}">{{ $topic->user->username }}</a>
                    </h4>
                    <span class="creator-username text-muted small" dir="ltr">&#64;{{ $topic->user->username }}</span>

                    @if(!empty($topic->user->sig))
                        <p class="creator-bio mt-2 text-muted small">{{ Str::limit($topic->user->sig, 120) }}</p>
                    @endif

                    <div class="creator-stats-strip mt-3 py-2 border-top border-bottom d-flex justify-content-around">
                        <div class="creator-stat-item">
                            <span class="stat-value fw-bold">{{ $authorGalleriesCount }}</span>
                            <span class="stat-label text-muted d-block small">{{ Lang::has('messages.galleries') ? __('messages.galleries') : 'معارض' }}</span>
                        </div>
                        <div class="creator-stat-item">
                            <span class="stat-value fw-bold">{{ $authorFollowersCount }}</span>
                            <span class="stat-label text-muted d-block small">{{ Lang::has('messages.followers') ? __('messages.followers') : 'متابعون' }}</span>
                        </div>
                        <div class="creator-stat-item">
                            <span class="stat-value fw-bold">{{ $authorTotalPostsCount }}</span>
                            <span class="stat-label text-muted d-block small">{{ Lang::has('messages.posts') ? __('messages.posts') : 'منشورات' }}</span>
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('profile.show', $topic->user->username) }}" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-bold">
                            <i class="fa fa-user me-1"></i> {{ Lang::has('messages.view_profile') ? __('messages.view_profile') : 'عرض الملف الشخصي' }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. AUTHOR'S OTHER GALLERIES -->
            @if($authorGalleries->isNotEmpty())
                <div class="gallery-sidebar-card shadow-sm mb-4">
                    <div class="sidebar-card-header d-flex align-items-center justify-content-between p-3 border-bottom">
                        <h4 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="fa fa-camera-retro text-primary"></i>
                            <span>{{ Lang::has('messages.more_from_author') ? __('messages.more_from_author', ['name' => $topic->user->username]) : 'المزيد من أعمال ' . $topic->user->username }}</span>
                        </h4>
                    </div>
                    <div class="sidebar-card-body p-3">
                        <div class="author-galleries-mini-grid">
                            @foreach($authorGalleries as $aStatus)
                                @php
                                     $aTopic = $aStatus->forumTopic;
                                     if(!$aTopic) continue;
                                     $aFirstImg = $aTopic->attachments->first(fn($a) => $a->isImage());
                                     $aThumb = $aFirstImg ? asset($aFirstImg->file_path) : ($aTopic->image_url ?: asset('upload/_avatar.png'));
                                     $aCount = $aTopic->attachments->filter(fn($a) => $a->isImage())->count();
                                 @endphp
                                 <a href="{{ route('forum.topic', $aTopic->id) }}" class="mini-gallery-thumb-item" title="{{ $aTopic->name }}">
                                     <img src="{{ $aThumb }}" alt="{{ $aTopic->name }}" loading="lazy">
                                     <div class="mini-thumb-overlay">
                                         <span class="mini-count-badge"><i class="fa fa-clone"></i> {{ $aCount ?: 1 }}</span>
                                     </div>
                                 </a>
                             @endforeach
                         </div>
                     </div>
                 </div>
             @endif

             <!-- 3. SUGGESTED GALLERIES (DISCOVERY) -->
             @if($suggestedGalleries->isNotEmpty())
                 <div class="gallery-sidebar-card shadow-sm mb-4">
                     <div class="sidebar-card-header d-flex align-items-center justify-content-between p-3 border-bottom">
                         <h4 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                             <i class="fa fa-compass text-info"></i>
                             <span>{{ Lang::has('messages.suggested_galleries') ? __('messages.suggested_galleries') : 'معارض صور مقترحة' }}</span>
                         </h4>
                     </div>
                    <div class="sidebar-card-body p-2">
                        <div class="d-flex flex-column gap-2">
                            @foreach($suggestedGalleries as $sStatus)
                                @php
                                    $sTopic = $sStatus->forumTopic;
                                    if(!$sTopic) continue;
                                    $sFirstImg = $sTopic->attachments->first(fn($a) => $a->isImage());
                                    $sThumb = $sFirstImg ? asset($sFirstImg->file_path) : ($sTopic->image_url ?: asset('upload/_avatar.png'));
                                    $sCount = $sTopic->attachments->filter(fn($a) => $a->isImage())->count();
                                @endphp
                                <a href="{{ route('forum.topic', $sTopic->id) }}" class="suggested-gallery-row-card text-decoration-none">
                                    <div class="suggested-row-thumb">
                                        <img src="{{ $sThumb }}" alt="{{ $sTopic->name }}" loading="lazy">
                                        <span class="row-count-badge">{{ $sCount ?: 1 }}</span>
                                    </div>
                                    <div class="suggested-row-info">
                                        <h5 class="row-title">{{ Str::limit($sTopic->name ?: __('messages.photos_gallery'), 45) }}</h5>
                                        <div class="row-meta text-muted small d-flex align-items-center gap-2">
                                            <span>{{ $sTopic->user?->username }}</span>
                                            <span>•</span>
                                            <span><i class="fa fa-thumbs-up"></i> {{ $sTopic->likes()->count() }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- SIDEBAR ADS -->
            @include('theme::partials.ads', ['id' => 5])
        </aside>
    </div>
</div>

<!-- ============================================================ -->
<!-- IMMERSIVE HIGH-PERFORMANCE LIGHTBOX MODAL (ZERO-DEPENDENCY) -->
<!-- ============================================================ -->
<div class="gallery-lightbox-modal" id="galleryLightboxModal" role="dialog" aria-hidden="true" style="display: none;">
    <div class="lightbox-backdrop" onclick="closeGalleryLightbox()"></div>

    <!-- TOP CONTROL BAR -->
    <div class="lightbox-top-bar">
        <div class="lightbox-info-group">
            <span class="lightbox-counter" id="lightboxCounter">1 / 1</span>
            <span class="lightbox-title text-truncate" id="lightboxTitle"></span>
        </div>

        <div class="lightbox-tools-group">
            <button type="button" class="btn-lightbox-tool" onclick="zoomLightboxImage(0.25)" title="تكبير (+)">
                <i class="fa fa-search-plus"></i>
            </button>
            <button type="button" class="btn-lightbox-tool" onclick="zoomLightboxImage(-0.25)" title="تصغير (-)">
                <i class="fa fa-search-minus"></i>
            </button>
            <button type="button" class="btn-lightbox-tool" onclick="resetLightboxZoom()" title="إعادة تعيين الحجم">
                <i class="fa fa-refresh"></i>
            </button>
            <button type="button" class="btn-lightbox-tool" onclick="toggleLightboxFullscreen()" title="ملء الشاشة">
                <i class="fa fa-arrows-alt"></i>
            </button>
            <a href="#" class="btn-lightbox-tool text-decoration-none" id="lightboxDownloadBtn" download title="تحميل الصورة">
                <i class="fa fa-download"></i>
            </a>
            <button type="button" class="btn-lightbox-tool" onclick="copyLightboxImageUrl()" title="نسخ رابط الصورة">
                <i class="fa fa-link"></i>
            </button>
            <button type="button" class="btn-lightbox-tool btn-lightbox-close" onclick="closeGalleryLightbox()" title="إغلاق (Esc)">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>

    <!-- MAIN LIGHTBOX STAGE (CANVAS) -->
    <div class="lightbox-canvas-area" id="lightboxCanvasArea">
        <button type="button" class="lightbox-nav-btn nav-prev" onclick="prevLightboxImage()" title="السابق (←)">
            <i class="fa fa-chevron-right"></i>
        </button>

        <div class="lightbox-img-viewport" id="lightboxImgViewport">
            <img src="" alt="" id="lightboxActiveImg" class="lightbox-active-img" draggable="false">
        </div>

        <button type="button" class="lightbox-nav-btn nav-next" onclick="nextLightboxImage()" title="التالي (→)">
            <i class="fa fa-chevron-left"></i>
        </button>
    </div>

    <!-- BOTTOM THUMBNAIL STRIP -->
    <div class="lightbox-thumbs-bar" id="lightboxThumbsBar">
        <div class="thumbs-scroll-track" id="thumbsScrollTrack"></div>
    </div>
</div>

<!-- ============================================================ -->
<!-- SCOPED CSS STYLES FOR THE MODERN PHOTO GALLERY PAGE -->
<!-- ============================================================ -->
<style>
/* 1. CONTAINER & THEME TOKENS */
.forum-rdx-gallery-page {
    --gallery-surface: var(--rdx-surface, #ffffff);
    --gallery-surface-alt: var(--rdx-surface-alt, #f8f9fc);
    --gallery-text: var(--rdx-text, #283c50);
    --gallery-muted: var(--rdx-text-muted, #7587a7);
    --gallery-border: var(--rdx-border, #eaeaf5);
    --gallery-accent: var(--rdx-violet, #615dfa);
    --gallery-accent-soft: rgba(97, 93, 250, 0.12);
    --gallery-cyan: #00c7d9;
    --gallery-radius: 20px;
    --gallery-shadow: 0 14px 34px rgba(15, 23, 42, 0.05);
    margin-top: 10px;
}

[data-bs-theme="dark"] .forum-rdx-gallery-page,
.dark .forum-rdx-gallery-page,
html.app-skin-dark .forum-rdx-gallery-page {
    --gallery-surface: #1d2333;
    --gallery-surface-alt: #161b28;
    --gallery-text: #eef2ff;
    --gallery-muted: #9aa4bf;
    --gallery-border: rgba(255, 255, 255, 0.08);
    --gallery-shadow: 0 16px 36px rgba(0, 0, 0, 0.35);
}

/* 1.1 FLEX & LAYOUT UTILITIES (COMPATIBILITY) */
.forum-rdx-gallery-page .d-flex { display: flex !important; }
.forum-rdx-gallery-page .d-inline-flex { display: inline-flex !important; }
.forum-rdx-gallery-page .align-items-center { align-items: center !important; }
.forum-rdx-gallery-page .justify-content-between { justify-content: space-between !important; }
.forum-rdx-gallery-page .justify-content-around { justify-content: space-around !important; }
.forum-rdx-gallery-page .flex-wrap { flex-wrap: wrap !important; }
.forum-rdx-gallery-page .flex-column { flex-direction: column !important; }
.forum-rdx-gallery-page .gap-1 { gap: 4px !important; }
.forum-rdx-gallery-page .gap-2 { gap: 8px !important; }
.forum-rdx-gallery-page .gap-3 { gap: 16px !important; }
.forum-rdx-gallery-page .w-100 { width: 100% !important; }
.forum-rdx-gallery-page .mb-0 { margin-bottom: 0 !important; }
.forum-rdx-gallery-page .mb-1 { margin-bottom: 4px !important; }
.forum-rdx-gallery-page .mb-2 { margin-bottom: 8px !important; }
.forum-rdx-gallery-page .mb-3 { margin-bottom: 16px !important; }
.forum-rdx-gallery-page .mb-4 { margin-bottom: 24px !important; }
.forum-rdx-gallery-page .mt-2 { margin-top: 8px !important; }
.forum-rdx-gallery-page .mt-3 { margin-top: 16px !important; }
.forum-rdx-gallery-page .p-2 { padding: 8px !important; }
.forum-rdx-gallery-page .p-3 { padding: 16px !important; }
.forum-rdx-gallery-page .d-none { display: none !important; }
.forum-rdx-gallery-page .text-truncate {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

/* 2. CARD SHELL & STRUCTURE */
.gallery-card-shell,
.gallery-sidebar-card {
    background: var(--gallery-surface);
    border: 1px solid var(--gallery-border);
    border-radius: var(--gallery-radius);
    overflow: visible;
    transition: box-shadow 0.25s ease;
}

.gallery-card-header {
    padding: 24px 26px 18px;
    border-bottom: 1px solid var(--gallery-border);
}

/* 3. AUTHOR HEADER */
.gallery-user-avatar {
    position: relative;
    width: 48px;
    height: 48px;
    border-radius: 50%;
}
.gallery-avatar-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--gallery-surface);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.gallery-user-avatar.online::after {
    content: '';
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    background: #17c666;
    border: 2px solid var(--gallery-surface);
    border-radius: 50%;
}
.gallery-verified-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 18px;
    height: 18px;
    background: #3454d1;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    border: 2px solid var(--gallery-surface);
}

.gallery-author-name {
    font-weight: 700;
    color: var(--gallery-text);
    font-size: 1.05rem;
    text-decoration: none;
    transition: color 0.2s;
}
.gallery-author-name:hover {
    color: var(--gallery-accent);
}

.gallery-role-pill {
    font-size: 0.75rem;
    background: var(--gallery-accent-soft);
    color: var(--gallery-accent);
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 600;
}

.gallery-badge-pill {
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 600;
}
.badge-pinned { background: rgba(255, 162, 29, 0.15); color: #ffa21d; }
.badge-locked { background: rgba(148, 163, 184, 0.2); color: var(--gallery-muted); }

.gallery-submeta {
    font-size: 0.82rem;
    color: var(--gallery-muted);
    margin-top: 4px;
}
.gallery-category-pill {
    color: var(--gallery-muted);
    text-decoration: none;
    font-weight: 500;
}
.gallery-category-pill:hover {
    color: var(--gallery-accent);
}
.gallery-photos-pill {
    background: var(--gallery-surface-alt);
    border: 1px solid var(--gallery-border);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.76rem;
    font-weight: 600;
    color: var(--gallery-accent);
}

/* 4. BUTTONS & CONTROLS */
.btn-gallery-follow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border-radius: 24px;
    font-size: 0.84rem;
    font-weight: 600;
    background: var(--gallery-accent-soft);
    color: var(--gallery-accent);
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.25s ease;
}
.btn-gallery-follow:hover {
    background: var(--gallery-accent);
    color: #fff;
    transform: translateY(-1px);
}
.btn-gallery-follow.is-following {
    background: var(--gallery-surface-alt);
    border-color: var(--gallery-border);
    color: var(--gallery-muted);
}
.btn-gallery-follow.is-following:hover {
    border-color: #ea4d4d;
    color: #ea4d4d;
    background: rgba(234, 77, 77, 0.08);
}

.btn-gallery-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--gallery-surface-alt);
    border: 1px solid var(--gallery-border);
    color: var(--gallery-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-gallery-icon:hover {
    background: var(--gallery-accent-soft);
    color: var(--gallery-accent);
    border-color: var(--gallery-accent);
}

.gallery-menu-dropdown,
.gallery-share-flyout {
    position: absolute;
    top: calc(100% + 8px);
    inset-inline-end: 0;
    min-width: 220px;
    background: var(--gallery-surface);
    border: 1px solid var(--gallery-border);
    border-radius: 14px;
    padding: 6px;
    z-index: 9999;
    box-shadow: 0 16px 36px rgba(0,0,0,0.15);
}
.gallery-menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 8px 12px;
    font-size: 0.86rem;
    color: var(--gallery-text);
    background: transparent;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-align: start;
    transition: background 0.15s ease, color 0.15s ease;
}
.gallery-menu-item:hover {
    background: var(--gallery-surface-alt);
    color: var(--gallery-accent);
}
.gallery-menu-divider {
    height: 1px;
    background: var(--gallery-border);
    margin: 4px 0;
}

/* 5. TITLE & CAPTION */
.gallery-post-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--gallery-text);
    line-height: 1.35;
    letter-spacing: -0.02em;
}
.gallery-post-caption {
    font-size: 0.95rem;
    color: var(--gallery-text);
    line-height: 1.7;
}
.gallery-caption-content.collapsed {
    max-height: 120px;
    overflow: hidden;
    position: relative;
    mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
    -webkit-mask-image: linear-gradient(to bottom, black 60%, transparent 100%);
}
.btn-caption-toggle {
    background: none;
    border: none;
    color: var(--gallery-accent);
    font-weight: 700;
    font-size: 0.85rem;
    padding: 4px 0;
    cursor: pointer;
}

/* 6. IMMERSIVE PHOTO SHOWCASE (MOSAIC GRIDS) */
.gallery-stage-container {
    padding: 16px 24px;
    background: var(--gallery-surface-alt);
    border-bottom: 1px solid var(--gallery-border);
}

.gallery-mosaic-grid {
    display: grid;
    gap: 8px;
    border-radius: 16px;
    overflow: hidden;
    position: relative;
}

/* 1 IMAGE: Cinematic Wide Stage with Ambient Glow */
.gallery-count-1 {
    grid-template-columns: 1fr;
    max-height: 600px;
}
.gallery-count-1 .gallery-item-tile {
    max-height: 600px;
    aspect-ratio: 16 / 10;
}

/* 2 IMAGES: Duo Split */
.gallery-count-2 {
    grid-template-columns: 1fr 1fr;
    aspect-ratio: 16 / 10;
}

/* 3 IMAGES: 1 Dominant + 2 Stacked */
.gallery-count-3 {
    grid-template-columns: 2fr 1fr;
    grid-template-rows: 1fr 1fr;
    aspect-ratio: 16 / 10;
}
.gallery-count-3 .item-pos-0 {
    grid-row: span 2;
}

/* 4 IMAGES: 1 Lead + 3 Stacked / Split */
.gallery-count-4 {
    grid-template-columns: 2fr 1fr;
    grid-template-rows: repeat(3, 1fr);
    aspect-ratio: 16 / 10;
}
.gallery-count-4 .item-pos-0 {
    grid-row: span 3;
}

/* 5+ IMAGES: Editorial 2 Top + 3 Bottom Collage */
.gallery-count-5 {
    grid-template-columns: repeat(6, 1fr);
    grid-template-rows: repeat(2, 220px);
}
.gallery-count-5 .item-pos-0 { grid-column: span 3; grid-row: span 1; }
.gallery-count-5 .item-pos-1 { grid-column: span 3; grid-row: span 1; }
.gallery-count-5 .item-pos-2 { grid-column: span 2; grid-row: span 1; }
.gallery-count-5 .item-pos-3 { grid-column: span 2; grid-row: span 1; }
.gallery-count-5 .item-pos-4 { grid-column: span 2; grid-row: span 1; }

.gallery-item-tile {
    position: relative;
    overflow: hidden;
    background: #000;
    cursor: pointer;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.gallery-item-tile:hover {
    z-index: 2;
}
.gallery-img-wrapper {
    width: 100%;
    height: 100%;
    position: relative;
}
.gallery-tile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease, filter 0.3s ease;
}
.gallery-item-tile:hover .gallery-tile-img {
    transform: scale(1.035);
}

.gallery-tile-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.55) 0%, transparent 60%);
    opacity: 0;
    transition: opacity 0.25s ease;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    padding: 12px;
}
.gallery-item-tile:hover .gallery-tile-overlay {
    opacity: 1;
}

.tile-action-badge {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(8px);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    border: 1px solid rgba(255,255,255,0.2);
}
.tile-number-badge {
    background: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(8px);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.2);
}

.gallery-extra-overlay {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(4px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #fff;
    transition: background 0.2s ease;
}
.gallery-item-tile:hover .gallery-extra-overlay {
    background: rgba(97, 93, 250, 0.85);
}
.extra-count-text {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
}
.extra-count-label {
    font-size: 0.78rem;
    font-weight: 600;
    opacity: 0.9;
    margin-top: 4px;
}

/* AMBIENT GLOW ON SINGLE HERO */
.gallery-ambient-glow {
    position: absolute;
    inset: -20px;
    background-size: cover;
    background-position: center;
    filter: blur(30px);
    opacity: 0.35;
    pointer-events: none;
    z-index: 0;
}

/* TILE MANAGEMENT TOOLS (DELETE & DRAG) */
.gallery-tile-tools {
    position: absolute;
    top: 10px;
    inset-inline-end: 10px;
    z-index: 10;
    display: flex;
    gap: 6px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s ease;
    transform: translateY(-4px);
}
.gallery-item-tile:hover .gallery-tile-tools {
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
}
.btn-tile-tool {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(15, 23, 42, 0.85);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(8px);
    transition: all 0.2s ease;
}
.btn-tile-delete:hover {
    background: #ea4d4d;
    border-color: #ea4d4d;
    transform: scale(1.1);
}
.btn-tile-drag {
    cursor: grab;
}

/* QUICK TOOLBAR & CHIPS */
.gallery-quick-toolbar {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid var(--gallery-border);
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 12px !important;
}
.gallery-toolbar-group {
    display: inline-flex !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
}
.btn-gallery-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--gallery-surface);
    border: 1px solid var(--gallery-border);
    color: var(--gallery-text);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-gallery-chip:hover {
    border-color: var(--gallery-accent);
    color: var(--gallery-accent);
}
.btn-gallery-chip-accent {
    background: var(--gallery-accent-soft);
    border-color: transparent;
    color: var(--gallery-accent);
}
.btn-gallery-chip-accent:hover {
    background: var(--gallery-accent);
    color: #fff;
}
.gallery-chip-badge {
    background: var(--gallery-surface);
    border: 1px solid var(--gallery-border);
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    color: var(--gallery-muted);
    font-weight: 600;
}

/* EMPTY STATE */
.gallery-empty-state {
    padding: 48px 24px;
    text-align: center;
}
.empty-icon-circle {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: var(--gallery-accent-soft);
    color: var(--gallery-accent);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

/* 7. SOCIAL ACTION BAR */
.forum-rdx-gallery-page .gallery-action-bar {
    padding: 14px 24px !important;
    border-bottom: 1px solid var(--gallery-border) !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 12px !important;
    background: var(--gallery-surface) !important;
}

.forum-rdx-gallery-page .gallery-actions-left {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
    width: auto !important;
}

.forum-rdx-gallery-page .gallery-actions-right {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.forum-rdx-gallery-page .gallery-flyout-wrap {
    position: relative !important;
    display: inline-flex !important;
    width: auto !important;
    flex: 0 0 auto !important;
}

.forum-rdx-gallery-page .btn-gallery-action {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    background: var(--gallery-surface-alt) !important;
    border: 1px solid var(--gallery-border) !important;
    color: var(--gallery-text) !important;
    padding: 8px 18px !important;
    border-radius: 24px !important;
    font-size: 0.86rem !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    width: auto !important;
    min-width: auto !important;
    max-width: none !important;
    flex: 0 0 auto !important;
    height: 40px !important;
    line-height: normal !important;
    white-space: nowrap !important;
    box-shadow: none !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.forum-rdx-gallery-page .btn-gallery-action:hover {
    background: var(--gallery-accent-soft) !important;
    border-color: var(--gallery-accent) !important;
    color: var(--gallery-accent) !important;
    transform: translateY(-2px) !important;
}

.forum-rdx-gallery-page .btn-gallery-action.is-saved {
    background: var(--gallery-accent-soft) !important;
    border-color: var(--gallery-accent) !important;
    color: var(--gallery-accent) !important;
}

.forum-rdx-gallery-page .action-count-badge {
    background: var(--gallery-surface) !important;
    color: var(--gallery-muted) !important;
    padding: 1px 7px !important;
    border-radius: 10px !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    border: 1px solid var(--gallery-border) !important;
}

/* REACTION POPUP OVERRIDE */
.forum-rdx-gallery-page .reaction-options-dropdown:not(.active) {
    display: none !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
}

.forum-rdx-gallery-page .reaction-options-dropdown.active {
    display: flex !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    animation: bounceInReaction 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes bounceInReaction {
    from {
        opacity: 0;
        transform: translateY(10px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* 8. COMMENTS SECTION */
.gallery-comments-section {
    padding: 24px 26px;
}

/* 9. SIDEBAR CARDS */
.creator-card-cover {
    height: 90px;
    background: linear-gradient(135deg, #615dfa 0%, #3454d1 100%);
    border-radius: var(--gallery-radius) var(--gallery-radius) 0 0;
}
.creator-card-body {
    padding: 0 20px 20px;
    position: relative;
}
.creator-avatar-wrap {
    display: inline-block;
    position: relative;
    margin-top: -42px;
}
.creator-big-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--gallery-surface);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.creator-verified-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 22px;
    height: 22px;
    background: #3454d1;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    border: 2px solid var(--gallery-surface);
}
.creator-name a {
    color: var(--gallery-text);
    font-weight: 800;
    font-size: 1.15rem;
    text-decoration: none;
}
.creator-stat-item .stat-value {
    font-size: 1.1rem;
    color: var(--gallery-text);
}

/* MINI GALLERIES GRID */
.author-galleries-mini-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}
.mini-gallery-thumb-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
    display: block;
    background: #000;
}
.mini-gallery-thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.mini-gallery-thumb-item:hover img {
    transform: scale(1.08);
}
.mini-thumb-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 60%);
    display: flex;
    align-items: flex-end;
    padding: 8px;
}
.mini-count-badge {
    background: rgba(0,0,0,0.7);
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 8px;
}

/* SUGGESTED ROW CARDS */
.suggested-gallery-row-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px;
    border-radius: 12px;
    transition: background 0.2s ease;
}
.suggested-gallery-row-card:hover {
    background: var(--gallery-surface-alt);
}
.suggested-row-thumb {
    position: relative;
    width: 64px;
    height: 64px;
    border-radius: 10px;
    overflow: hidden;
    flex-shrink: 0;
    background: #000;
}
.suggested-row-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.row-count-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    background: rgba(0,0,0,0.75);
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 4px;
    border-radius: 4px;
}
.suggested-row-info .row-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--gallery-text);
    margin-bottom: 3px;
    line-height: 1.3;
}

/* 10. IMMERSIVE LIGHTBOX CSS */
.gallery-lightbox-modal {
    position: fixed;
    inset: 0;
    z-index: 100000;
    display: flex;
    flex-direction: column;
    background: rgba(5, 7, 15, 0.96);
    backdrop-filter: blur(16px);
    overflow: hidden;
}
.lightbox-backdrop {
    position: absolute;
    inset: 0;
    z-index: 1;
}

.lightbox-top-bar {
    position: relative;
    z-index: 10;
    height: 64px;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent);
    color: #fff;
}
.lightbox-counter {
    background: rgba(255, 255, 255, 0.15);
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.82rem;
    margin-inline-end: 12px;
}
.lightbox-title {
    font-size: 0.95rem;
    font-weight: 600;
    max-width: 400px;
}

.lightbox-tools-group {
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-lightbox-tool {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.15);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    cursor: pointer;
    backdrop-filter: blur(8px);
    transition: all 0.2s ease;
}
.btn-lightbox-tool:hover {
    background: var(--gallery-accent);
    color: #fff;
    transform: scale(1.1);
}
.btn-lightbox-close:hover {
    background: #ea4d4d;
}

.lightbox-canvas-area {
    position: relative;
    z-index: 5;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.lightbox-img-viewport {
    max-width: 90vw;
    max-height: 75vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    user-select: none;
}
.lightbox-active-img {
    max-width: 100%;
    max-height: 75vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.8);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.lightbox-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(12px);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    cursor: pointer;
    z-index: 10;
    transition: all 0.25s ease;
}
.lightbox-nav-btn:hover {
    background: var(--gallery-accent);
    color: #fff;
    transform: translateY(-50%) scale(1.12);
}
.nav-prev { inset-inline-start: 24px; }
.nav-next { inset-inline-end: 24px; }

.lightbox-thumbs-bar {
    position: relative;
    z-index: 10;
    height: 90px;
    padding: 10px 24px;
    background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
    display: flex;
    align-items: center;
    justify-content: center;
}
.thumbs-scroll-track {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    max-width: 100%;
    padding: 4px;
    scrollbar-width: thin;
}
.lightbox-thumb-item {
    width: 64px;
    height: 64px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    opacity: 0.5;
    flex-shrink: 0;
    transition: all 0.2s ease;
}
.lightbox-thumb-item.active {
    border-color: var(--gallery-accent);
    opacity: 1;
    transform: scale(1.08);
}
.lightbox-thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* RESPONSIVE DESIGN */
@media (max-width: 991px) {
    .gallery-mosaic-grid {
        grid-template-columns: 1fr !important;
        grid-template-rows: auto !important;
    }
    .gallery-item-tile {
        grid-column: span 1 !important;
        grid-row: span 1 !important;
        max-height: 400px;
        aspect-ratio: 4 / 3;
    }
    .lightbox-nav-btn {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    .nav-prev { inset-inline-start: 8px; }
    .nav-next { inset-inline-end: 8px; }
}
</style>

<!-- ============================================================ -->
<!-- INTERACTIVE JAVASCRIPT: AJAX GALLERY LOGIC & LIGHTBOX -->
<!-- ============================================================ -->
<script>
// 1. DATA STATE & LIGHTBOX CONTROLLER
let galleryImagesList = {!! $lightboxImagesJson !!};
let currentLightboxIndex = 0;
let currentLightboxZoom = 1;

function openGalleryLightbox(index) {
    if (!galleryImagesList || galleryImagesList.length === 0) return;
    currentLightboxIndex = (index >= 0 && index < galleryImagesList.length) ? index : 0;
    currentLightboxZoom = 1;

    const modal = document.getElementById('galleryLightboxModal');
    if (!modal) return;

    renderLightboxThumbs();
    updateLightboxActiveImage();

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeGalleryLightbox() {
    const modal = document.getElementById('galleryLightboxModal');
    if (!modal) return;
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

function updateLightboxActiveImage() {
    const current = galleryImagesList[currentLightboxIndex];
    if (!current) return;

    const imgEl = document.getElementById('lightboxActiveImg');
    const counterEl = document.getElementById('lightboxCounter');
    const titleEl = document.getElementById('lightboxTitle');
    const downloadBtn = document.getElementById('lightboxDownloadBtn');

    if (imgEl) {
        imgEl.style.transform = `scale(${currentLightboxZoom})`;
        imgEl.src = current.url;
        imgEl.alt = current.title;
    }
    if (counterEl) {
        counterEl.textContent = `${currentLightboxIndex + 1} / ${galleryImagesList.length}`;
    }
    if (titleEl) {
        titleEl.textContent = current.title || '';
    }
    if (downloadBtn) {
        downloadBtn.href = current.url;
    }

    // Update active thumbnail
    document.querySelectorAll('.lightbox-thumb-item').forEach((el, idx) => {
        if (idx === currentLightboxIndex) {
            el.classList.add('active');
            el.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        } else {
            el.classList.remove('active');
        }
    });
}

function renderLightboxThumbs() {
    const track = document.getElementById('thumbsScrollTrack');
    if (!track) return;
    track.innerHTML = '';

    galleryImagesList.forEach((item, idx) => {
        const thumb = document.createElement('div');
        thumb.className = `lightbox-thumb-item ${idx === currentLightboxIndex ? 'active' : ''}`;
        thumb.onclick = () => {
            currentLightboxIndex = idx;
            currentLightboxZoom = 1;
            updateLightboxActiveImage();
        };
        thumb.innerHTML = `<img src="${item.url}" alt="${item.title}">`;
        track.appendChild(thumb);
    });
}

function nextLightboxImage() {
    if (galleryImagesList.length <= 1) return;
    currentLightboxIndex = (currentLightboxIndex + 1) % galleryImagesList.length;
    currentLightboxZoom = 1;
    updateLightboxActiveImage();
}

function prevLightboxImage() {
    if (galleryImagesList.length <= 1) return;
    currentLightboxIndex = (currentLightboxIndex - 1 + galleryImagesList.length) % galleryImagesList.length;
    currentLightboxZoom = 1;
    updateLightboxActiveImage();
}

function zoomLightboxImage(delta) {
    currentLightboxZoom = Math.min(Math.max(0.5, currentLightboxZoom + delta), 3.0);
    const imgEl = document.getElementById('lightboxActiveImg');
    if (imgEl) imgEl.style.transform = `scale(${currentLightboxZoom})`;
}

function resetLightboxZoom() {
    currentLightboxZoom = 1;
    const imgEl = document.getElementById('lightboxActiveImg');
    if (imgEl) imgEl.style.transform = `scale(1)`;
}

function toggleLightboxFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(() => {});
    } else {
        if (document.exitFullscreen) document.exitFullscreen().catch(() => {});
    }
}

function copyLightboxImageUrl() {
    const current = galleryImagesList[currentLightboxIndex];
    if (current && current.url) {
        navigator.clipboard.writeText(current.url);
        showToast('تم نسخ رابط الصورة المباشر بنجاح');
    }
}

// KEYBOARD SHORTCUTS FOR LIGHTBOX
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('galleryLightboxModal');
    if (!modal || modal.style.display === 'none') return;

    if (e.key === 'Escape') closeGalleryLightbox();
    else if (e.key === 'ArrowRight') prevLightboxImage();
    else if (e.key === 'ArrowLeft') nextLightboxImage();
    else if (e.key === '+' || e.key === '=') zoomLightboxImage(0.25);
    else if (e.key === '-') zoomLightboxImage(-0.25);
});

// 2. REAL-TIME AJAX GALLERY MANAGEMENT
function ajaxUploadGalleryImages(input, topicId) {
    if (!input.files || input.files.length === 0) return;

    const formData = new FormData();
    for (let i = 0; i < input.files.length; i++) {
        formData.append('images[]', input.files[i]);
    }

    const progressBox = document.getElementById('galleryUploadProgress');
    const progressBar = document.getElementById('galleryUploadBar');
    const progressPercent = document.getElementById('galleryUploadPercent');
    const label = document.getElementById('btnUploadGalleryLabel');

    if (progressBox) progressBox.classList.remove('d-none');
    if (label) label.style.pointerEvents = 'none';

    // XHR to track progress smoothly
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("status.gallery.add_images", ":id") }}'.replace(':id', topicId));
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            if (progressBar) progressBar.style.width = percent + '%';
            if (progressPercent) progressPercent.textContent = percent + '%';
        }
    };

    xhr.onload = function() {
        if (progressBox) progressBox.classList.add('d-none');
        if (label) label.style.pointerEvents = '';
        input.value = '';

        try {
            const res = JSON.parse(xhr.responseText);
            if (res.success && res.attachments) {
                // Re-sync local gallery state
                galleryImagesList = res.attachments.map((att, idx) => ({
                    index: idx,
                    id: att.id,
                    url: att.url,
                    title: att.name,
                    size: att.size || ''
                }));

                // Update DOM counters
                const countBadge = document.getElementById('galleryPhotoCount');
                if (countBadge) countBadge.textContent = res.count;
                const slotBadge = document.getElementById('galleryRemainingSlot');
                if (slotBadge) slotBadge.textContent = Math.max(0, 10 - res.count);

                showToast(res.message || 'تمت إضافة الصور بنجاح');
                // Soft refresh grid via location reload or instant re-render
                setTimeout(() => location.reload(), 600);
            } else {
                alert(res.message || 'حدث خطأ أثناء رفع الصور');
            }
        } catch(e) {
            alert('حدث خطأ أثناء معالجة الصور');
        }
    };

    xhr.onerror = function() {
        if (progressBox) progressBox.classList.add('d-none');
        if (label) label.style.pointerEvents = '';
        alert('فشل الاتصال بالخادم أثناء رفع الصور');
    };

    xhr.send(formData);
}

function ajaxDeleteGalleryImage(attachmentId, btn, event) {
    if (event) event.stopPropagation();
    if (!confirm('{{ __("messages.confirm_delete_image") ?? "هل أنت متأكد من حذف هذه الصورة؟" }}')) return;

    const tile = document.getElementById('galleryItem_' + attachmentId);
    if (tile) tile.style.opacity = '0.4';

    fetch('{{ route("status.gallery.delete_image", ":id") }}'.replace(':id', attachmentId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (tile) {
                tile.style.transform = 'scale(0.8)';
                tile.style.opacity = '0';
                setTimeout(() => tile.remove(), 250);
            }
            galleryImagesList = galleryImagesList.filter(item => item.id != attachmentId);
            const countBadge = document.getElementById('galleryPhotoCount');
            if (countBadge) countBadge.textContent = data.remaining_count;
            const slotBadge = document.getElementById('galleryRemainingSlot');
            if (slotBadge) slotBadge.textContent = Math.max(0, 10 - data.remaining_count);

            showToast(data.message || 'تم حذف الصورة بنجاح');
        } else {
            alert(data.message || 'خطأ أثناء حذف الصورة');
            if (tile) tile.style.opacity = '1';
        }
    })
    .catch(() => {
        alert('خطأ أثناء حذف الصورة');
        if (tile) tile.style.opacity = '1';
    });
}

function ajaxClearGallery(topicId) {
    if (!confirm('{{ __("messages.confirm_clear_gallery") ?? "هل أنت متأكد من تفريغ كافة الصور من هذا المعرض؟" }}')) return;

    fetch('{{ route("status.gallery.clear", ":id") }}'.replace(':id', topicId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('تم تفريغ كافة صور المعرض');
            setTimeout(() => location.reload(), 500);
        } else {
            alert(data.message || 'حدث خطأ أثناء تفريغ المعرض');
        }
    })
    .catch(() => alert('حدث خطأ أثناء تفريغ المعرض'));
}

// 3. SOCIAL ENGAGEMENT: FOLLOW, SAVE, REACTIONS, SHARE
function toggleAjaxFollow(username, btn) {
    btn.disabled = true;
    fetch('{{ route("profile.follow", ":username") }}'.replace(':username', username), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            const icon = btn.querySelector('i');
            const label = btn.querySelector('.follow-label');
            if (data.following) {
                btn.classList.add('is-following');
                if (icon) icon.className = 'fa fa-check';
                if (label) label.textContent = '{{ __("messages.following") ?? "متابع" }}';
                showToast('أصبحت الآن تتابع هذا المستخدم');
            } else {
                btn.classList.remove('is-following');
                if (icon) icon.className = 'fa fa-user-plus';
                if (label) label.textContent = '{{ __("messages.follow") ?? "متابعة" }}';
                showToast('تم إلغاء المتابعة');
            }
        }
    })
    .catch(() => {
        btn.disabled = false;
    });
}

function ajaxToggleSaveStatus(statusId, btn) {
    btn.disabled = true;
    fetch('{{ route("status.save_toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ status_id: statusId })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            const icon = btn.querySelector('i');
            const label = btn.querySelector('.save-btn-label');
            if (data.saved) {
                btn.classList.add('is-saved');
                if (icon) icon.className = 'fa fa-bookmark text-primary';
                if (label) label.textContent = '{{ __("messages.saved") ?? "محفوظ" }}';
                showToast(data.message || 'تم حفظ المنشور في محفوظاتك');
            } else {
                btn.classList.remove('is-saved');
                if (icon) icon.className = 'fa fa-bookmark-o';
                if (label) label.textContent = '{{ __("messages.save") ?? "حفظ" }}';
                showToast(data.message || 'تمت الإزالة من المحفوظات');
            }
        }
    })
    .catch(() => {
        btn.disabled = false;
    });
}

function toggleReactionDropdown(btn) {
    const wrap = btn.closest('.gallery-flyout-wrap');
    if (!wrap) return;
    const dropdown = wrap.querySelector('.reaction-options-dropdown');
    if (!dropdown) return;
    const isCurrentlyActive = dropdown.classList.contains('active');

    // Close all other dropdowns
    document.querySelectorAll('.gallery-menu-dropdown, .gallery-share-flyout').forEach(el => {
        el.style.display = 'none';
    });
    document.querySelectorAll('.reaction-options-dropdown').forEach(el => {
        el.classList.remove('active');
        el.style.display = 'none';
    });

    if (!isCurrentlyActive) {
        dropdown.classList.add('active');
        dropdown.style.display = 'flex';
    }
}

function ajaxPostReaction(topicId, reactionName, statusId) {
    fetch('{{ route("reaction.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ id: topicId, type: 'forum', reaction: reactionName })
    })
    .then(r => r.json())
    .then(data => {
        if (data.html) {
            const container = document.getElementById('reaction_image' + statusId);
            if (container) container.innerHTML = data.html;

            const textEl = document.querySelector('.reaction_txt' + statusId);
            if (textEl) {
                if (data.action === 'added' || data.action === 'updated') {
                    textEl.className = 'reaction_txt' + statusId + ' fw-bold text-info';
                    textEl.textContent = reactionName.charAt(0).toUpperCase() + reactionName.slice(1);
                } else {
                    textEl.className = 'reaction_txt' + statusId;
                    textEl.textContent = '{{ Lang::has("messages.react") ? __("messages.react") : "تفاعل" }}';
                }
            }

            const badge = document.getElementById('reactionsCountBadge');
            if (badge && data.count !== undefined) {
                badge.textContent = data.count;
            }
        }
        // Hide reaction flyout
        document.querySelectorAll('.reaction-options-dropdown').forEach(el => {
            el.classList.remove('active');
            el.style.display = 'none';
        });
    });
}

function focusCommentSection(topicId) {
    const commentWrap = document.getElementById('galleryCommentsContainer');
    if (commentWrap) {
        commentWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(() => {
            const textarea = commentWrap.querySelector('textarea, input[type="text"]');
            if (textarea) textarea.focus();
        }, 400);
    }
}

function toggleGalleryMenu(menuId) {
    const target = document.getElementById(menuId);
    if (!target) return;
    const isVisible = target.style.display === 'block';

    document.querySelectorAll('.gallery-menu-dropdown, .gallery-share-flyout').forEach(el => {
        el.style.display = 'none';
    });

    target.style.display = isVisible ? 'none' : 'block';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.gallery-dropdown-wrap') && !e.target.closest('.gallery-flyout-wrap')) {
        document.querySelectorAll('.gallery-menu-dropdown, .gallery-share-flyout').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelectorAll('.reaction-options-dropdown').forEach(el => {
            el.classList.remove('active');
            el.style.display = 'none';
        });
    }
});

function copyPostLink(url) {
    navigator.clipboard.writeText(url);
    showToast('{{ __("messages.link_copied") ?? "تم نسخ الرابط بنجاح" }}');
}

function showToast(msg) {
    let box = document.getElementById('galleryGlobalToastBox');
    if (!box) {
        box = document.createElement('div');
        box.id = 'galleryGlobalToastBox';
        box.style.cssText = 'position:fixed; bottom:24px; inset-inline-start:24px; z-index:999999; display:flex; flex-direction:column; gap:8px;';
        document.body.appendChild(box);
    }
    const toast = document.createElement('div');
    toast.className = 'shadow-lg px-4 py-3 text-white rounded-3';
    toast.style.cssText = 'background:#615dfa; font-size:0.9rem; font-weight:600; display:flex; align-items:center; gap:8px; animation:slideToastIn 0.3s cubic-bezier(0.16,1,0.3,1);';
    toast.innerHTML = '<i class="fa fa-check-circle"></i> ' + msg;
    box.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3200);
}

function toggleCaptionExpand() {
    const textEl = document.getElementById('galleryCaptionText');
    const btn = document.getElementById('btnCaptionToggle');
    if (!textEl || !btn) return;
    if (textEl.classList.contains('collapsed')) {
        textEl.classList.remove('collapsed');
        btn.textContent = '{{ __("messages.show_less") ?? "عرض أقل" }}';
    } else {
        textEl.classList.add('collapsed');
        btn.textContent = '{{ __("messages.show_more") ?? "عرض المزيد" }}';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const textEl = document.getElementById('galleryCaptionText');
    const btn = document.getElementById('btnCaptionToggle');
    if (textEl && textEl.scrollHeight > 140) {
        textEl.classList.add('collapsed');
        if (btn) btn.classList.remove('d-none');
    }
});
</script>

@include('theme::forum.scripts')
@endsection
