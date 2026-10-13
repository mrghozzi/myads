@php
    $showForumRoleBadges = (int) \App\Support\ForumSettings::get('show_role_badges', 1) === 1;
    $postNumber = 1; // #1 is the OP (Original Post)
    $viewerUser = auth()->user();
    $canManageTopicComments = false;
    if ($viewerUser && isset($topic) && $topic) {
        $isAuthor = (int) $viewerUser->id === (int) $topic->uid;
        $isModerator = $viewerUser->isAdmin() || $viewerUser->canModerateForum('edit_topics', (int) ($forum_category_id ?? $topic->cat));
        $canManageTopicComments = $isAuthor || $isModerator;
    }
@endphp

<div class="forum-modern-post-stream" id="forumPostStream_{{ $id }}">
    @forelse($comments as $comment)
        @php
            $postNumber++;
            $user = $comment->user;
            $rawText = $comment->txt;
            $date = $comment->date;
            $formattedText = \App\Support\ContentFormatter::formatForum($rawText);

            $myCommentReaction = null;
            $myCommentReactionOption = null;
            if (auth()->check()) {
                $myCommentReaction = \App\Models\Like::where('uid', auth()->id())
                    ->where('sid', $comment->id)
                    ->where('type', 4)
                    ->first();

                if ($myCommentReaction) {
                    $myCommentReactionOption = \App\Models\Option::where('o_parent', $myCommentReaction->id)
                        ->where('o_type', 'data_reaction')
                        ->first();
                }
            }

            $isOwner = auth()->check() && (int) auth()->id() === (int) $comment->uid;
            $catId = (int) ($forum_category_id ?? ($topic->cat ?? 0));
            $isForumModerator = auth()->check() && (
                auth()->user()->isAdmin() ||
                auth()->user()->canModerateForum('delete_comments', $catId ?: null) ||
                auth()->user()->canModerateForum(null, $catId ?: null)
            );
            $canDeleteComment = $isOwner || $isForumModerator;
        @endphp

        <!-- Forum Comment Card -->
        <article class="forum-modern-post-card forum-modern-comment-card coment{{ $comment->id }} {{ $comment->is_pinned ? 'is-pinned-comment' : '' }} {{ $comment->is_best_answer ? 'is-best-answer' : '' }}" id="post-{{ $comment->id }}" data-comment-id="{{ $comment->id }}">
            <header class="forum-modern-post-header">
                <div class="forum-modern-post-author">
                    @if($user)
                        <div class="forum-modern-avatar-wrap">
                            <a class="user-avatar small no-outline {{ $user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.short', $user->publicRouteIdentifier()) }}" title="{{ $user->username }}">
                                <div class="user-avatar-content">
                                    <div class="hexagon-image-30-32" data-src="{{ $user->avatarUrl() }}" style="background-image: url('{{ $user->avatarUrl() }}'); width: 30px; height: 32px; position: relative;">
                                        <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                                    </div>
                                </div>
                                <div class="user-avatar-progress-border">
                                    <div class="hexagon-border-40-44" data-line-color="{{ $user->profileBadgeColor() }}" style="width: 40px; height: 44px; position: relative;">
                                        <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                                    </div>
                                </div>
                                @if($user->hasVerifiedBadge())
                                    <div class="user-avatar-badge">
                                        <div class="user-avatar-badge-border">
                                            <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;">
                                                <canvas style="position: absolute; top: 0px; left: 0px;" width="22" height="24"></canvas>
                                            </div>
                                        </div>
                                        <div class="user-avatar-badge-content">
                                            <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;">
                                                <canvas style="position: absolute; top: 0px; left: 0px;" width="16" height="18"></canvas>
                                            </div>
                                        </div>
                                        <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                                    </div>
                                @endif
                            </a>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('profile.short', $user->publicRouteIdentifier()) }}" class="forum-modern-post-author-name">
                                    {{ $user->username }}
                                </a>
                                @if($showForumRoleBadges)
                                    <span class="forum-modern-post-badge">
                                        {{ $user->forumRoleLabel($forum_category_id ?? null) }}
                                    </span>
                                @endif
                            </div>
                            <div class="forum-modern-post-time" title="{{ \Carbon\Carbon::createFromTimestamp((int) $date)->toDateTimeString() }}">
                                {{ \Carbon\Carbon::createFromTimestamp((int) $date)->diffForHumans() }}
                            </div>
                        </div>
                    @else
                        <div class="forum-modern-avatar-wrap">
                            <div class="user-avatar small no-outline offline">
                                <div class="user-avatar-content">
                                    <div class="hexagon-image-30-32" data-src="{{ asset('upload/_avatar.png') }}" style="background-image: url('{{ asset('upload/_avatar.png') }}'); width: 30px; height: 32px; position: relative;">
                                        <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                                    </div>
                                </div>
                                <div class="user-avatar-progress-border">
                                    <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;">
                                        <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="forum-modern-post-author-name">{{ __('messages.deleted_user') ?? 'Deleted User' }}</span>
                            <div class="forum-modern-post-time">
                                {{ \Carbon\Carbon::createFromTimestamp((int) $date)->diffForHumans() }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if($comment->is_pinned)
                        <span class="forum-modern-badge-pinned-comment" title="{{ __('messages.pinned_comment') ?? 'Pinned Comment' }}">
                            <i class="fa fa-thumb-tack"></i> <span>{{ __('messages.pinned_comment') ?? 'Pinned' }}</span>
                        </span>
                    @endif
                    @if($comment->is_best_answer)
                        <span class="forum-modern-badge-best-answer" title="{{ __('messages.best_answer') ?? 'Best Answer' }}">
                            <i class="fa fa-check-circle"></i> <span>{{ __('messages.best_answer') ?? 'Best Answer' }}</span>
                        </span>
                    @endif

                    <a href="#post-{{ $comment->id }}" class="forum-modern-post-number text-decoration-none" title="Permalink">#{{ $postNumber }}</a>

                    @if($canDeleteComment)
                        <button type="button" class="btn btn-sm text-danger p-0 ms-1 trash_comment{{ $comment->id }}" onclick="deleteForumComment({{ $comment->id }})" title="{{ __('messages.delete') }}">
                            <i class="fa fa-trash"></i>
                        </button>
                    @endif
                </div>
            </header>

            <div class="forum-modern-post-body forum-comment-content">
                {!! $formattedText !!}
            </div>

            <footer class="forum-modern-post-actions">
                @auth
                    <!-- Reaction Button -->
                    <button type="button" class="forum-modern-post-action-btn" id="reaction-btn-comment-{{ $comment->id }}" onclick="toggleReaction({{ $comment->id }}, 'forum_comment', 'like')">
                        @if($myCommentReactionOption)
                            <img class="reaction-option-image" src="{{ theme_asset('img/reaction/like.png') }}" width="16" alt="like">
                        @else
                            <i class="fa-regular fa-thumbs-up fa-thumbs-o-up text-primary"></i>
                        @endif
                        <span>{{ __('messages.like') ?? 'Like' }}</span>
                    </button>

                    <!-- Reply to this Comment (Nested Reply Trigger) -->
                    @if($user)
                        <button type="button" class="forum-modern-post-action-btn forum-modern-reply-btn" onclick="replyToComment('{{ $user->username }}', {{ $id }}, {{ $comment->id }})">
                            <i class="fa fa-reply text-info"></i>
                            <span>{{ __('messages.reply') ?? 'Reply' }}</span>
                        </button>
                    @endif

                    @if($canManageTopicComments)
                        <!-- Pin / Unpin Comment -->
                        <button type="button" class="forum-modern-post-action-btn forum-modern-pin-btn {{ $comment->is_pinned ? 'active' : '' }}" onclick="togglePinComment({{ $comment->id }}, {{ $id }})" title="{{ $comment->is_pinned ? __('messages.unpin_comment') : __('messages.pin_comment') }}">
                            <i class="fa fa-thumb-tack {{ $comment->is_pinned ? 'text-warning' : 'text-muted' }}"></i>
                            <span>{{ $comment->is_pinned ? __('messages.unpin_comment') : __('messages.pin_comment') }}</span>
                        </button>

                        <!-- Best Answer Selection -->
                        <button type="button" class="forum-modern-post-action-btn forum-modern-best-answer-btn {{ $comment->is_best_answer ? 'active' : '' }}" onclick="toggleBestAnswerComment({{ $comment->id }}, {{ $id }})" title="{{ $comment->is_best_answer ? __('messages.unmark_best_answer') : __('messages.choose_best_answer') }}">
                            <i class="{{ $comment->is_best_answer ? 'fa-solid fa-circle-check fa-check-circle text-success' : 'fa-regular fa-circle-check fa-check-circle-o text-muted' }}"></i>
                            <span>{{ $comment->is_best_answer ? __('messages.best_answer') : __('messages.choose_best_answer') }}</span>
                        </button>
                    @endif

                    @if($canDeleteComment)
                        <!-- Delete Comment (صاحب التعليق أو المشرف) -->
                        <button type="button" class="forum-modern-post-action-btn forum-modern-delete-btn text-danger ms-auto" onclick="deleteForumComment({{ $comment->id }})" title="{{ __('messages.delete') ?? 'Delete' }}">
                            <i class="fa fa-trash text-danger"></i>
                            <span>{{ __('messages.delete') ?? 'Delete' }}</span>
                        </button>
                    @endif
                @endauth
            </footer>

            <!-- Nested / Threaded Comments Section (التعليقات المتشعبة) -->
            @if($comment->replies && $comment->replies->count() > 0)
                <div class="forum-modern-comment-replies" id="replies_for_{{ $comment->id }}">
                    <div class="forum-modern-replies-tree">
                        @foreach($comment->replies as $reply)
                            @php
                                $replyUser = $reply->user;
                                $replyDate = $reply->date;
                                $replyFormatted = \App\Support\ContentFormatter::formatForum($reply->txt);
                                $isReplyOwner = auth()->check() && (int) auth()->id() === (int) $reply->uid;
                                $canDeleteReply = $isReplyOwner || $isForumModerator;
                            @endphp
                            <div class="forum-modern-nested-card coment{{ $reply->id }}" id="comment_{{ $reply->id }}">
                                <div class="forum-modern-nested-header">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($replyUser)
                                            <div class="forum-modern-avatar-wrap forum-modern-avatar-mini">
                                                <a class="user-avatar small no-outline {{ $replyUser->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.short', $replyUser->publicRouteIdentifier()) }}" title="{{ $replyUser->username }}">
                                                    <div class="user-avatar-content">
                                                        <div class="hexagon-image-30-32" data-src="{{ $replyUser->avatarUrl() }}" style="background-image: url('{{ $replyUser->avatarUrl() }}'); width: 30px; height: 32px; position: relative;">
                                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="user-avatar-progress-border">
                                                        <div class="hexagon-border-40-44" data-line-color="{{ $replyUser->profileBadgeColor() }}" style="width: 40px; height: 44px; position: relative;">
                                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ route('profile.short', $replyUser->publicRouteIdentifier()) }}" class="forum-modern-post-author-name">
                                                        {{ $replyUser->username }}
                                                    </a>
                                                    @if($showForumRoleBadges)
                                                        <span class="forum-modern-post-badge">
                                                            {{ $replyUser->forumRoleLabel($forum_category_id ?? null) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="forum-modern-post-time">
                                                    {{ \Carbon\Carbon::createFromTimestamp((int) $replyDate)->diffForHumans() }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="forum-modern-avatar-wrap forum-modern-avatar-mini">
                                                <div class="user-avatar small no-outline offline">
                                                    <div class="user-avatar-content">
                                                        <div class="hexagon-image-30-32" data-src="{{ asset('upload/_avatar.png') }}" style="background-image: url('{{ asset('upload/_avatar.png') }}'); width: 30px; height: 32px; position: relative;">
                                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                                                        </div>
                                                    </div>
                                                    <div class="user-avatar-progress-border">
                                                        <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;">
                                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="forum-modern-post-author-name">{{ __('messages.deleted_user') ?? 'Deleted User' }}</span>
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        @if($canDeleteReply)
                                            <button type="button" class="btn btn-sm text-danger p-0 trash_comment{{ $reply->id }}" onclick="deleteForumComment({{ $reply->id }})" title="{{ __('messages.delete') }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="forum-modern-nested-body">
                                    {!! $replyFormatted !!}
                                </div>

                                <div class="forum-modern-nested-actions">
                                    @auth
                                        <button type="button" class="forum-modern-post-action-btn py-1 px-2 fs-12" id="reaction-btn-comment-{{ $reply->id }}" onclick="toggleReaction({{ $reply->id }}, 'forum_comment', 'like')">
                                            <i class="fa-regular fa-thumbs-up fa-thumbs-o-up text-primary"></i>
                                            <span>{{ __('messages.like') ?? 'Like' }}</span>
                                        </button>
                                        @if($replyUser)
                                            <button type="button" class="forum-modern-post-action-btn py-1 px-2 fs-12" onclick="replyToComment('{{ $replyUser->username }}', {{ $id }}, {{ $comment->id }})">
                                                <i class="fa fa-reply text-info"></i>
                                                <span>{{ __('messages.reply') ?? 'Reply' }}</span>
                                            </button>
                                        @endif
                                        @if($canDeleteReply)
                                            <button type="button" class="forum-modern-post-action-btn py-1 px-2 fs-12 text-danger ms-auto" onclick="deleteForumComment({{ $reply->id }})" title="{{ __('messages.delete') }}">
                                                <i class="fa fa-trash text-danger"></i>
                                                <span>{{ __('messages.delete') ?? 'Delete' }}</span>
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </article>
    @empty
        <div class="forum-modern-empty-comments text-center py-5">
            <i class="fa-regular fa-comments fa-comments-o fa-3x text-muted mb-3 opacity-50"></i>
            <h5 class="fw-bold text-muted">{{ __('messages.no_comments_yet') ?? 'No replies yet' }}</h5>
            <p class="fs-13 text-muted">{{ __('messages.be_first_to_reply') ?? 'Be the first to reply to this discussion!' }}</p>
        </div>
    @endforelse
</div>

<!-- Forum Reply Composer Card (محرر كتابة الردود بنمط Forum) -->
<div id="reply" class="forum-modern-reply-target" style="scroll-margin-top: 100px;"></div>
@if(!isset($hide_form) || !$hide_form)
    @auth
        <div class="forum-modern-reply-card mt-4" id="forumReplyBox_{{ $id }}">
            <div class="forum-modern-reply-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="forum-modern-avatar-wrap forum-modern-avatar-mini">
                        <div class="user-avatar small no-outline {{ auth()->user()->isOnline() ? 'online' : 'offline' }}">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ auth()->user()->avatarUrl() }}" style="background-image: url('{{ auth()->user()->avatarUrl() }}'); width: 30px; height: 32px; position: relative;">
                                    <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                                </div>
                            </div>
                            <div class="user-avatar-progress-border">
                                <div class="hexagon-border-40-44" data-line-color="{{ auth()->user()->profileBadgeColor() }}" style="width: 40px; height: 44px; position: relative;">
                                    <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="fw-bold fs-14">{{ __('messages.reply') ?? 'Write a Reply' }}</span>
                </div>

                <!-- Replying To Banner (الرد المتشعب) -->
                <div id="reply_indicator_{{ $id }}" class="forum-modern-replying-banner" style="display: none;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-reply text-primary"></i>
                        <span>{{ __('messages.replying_to') ?? 'Replying to' }} <strong>@<span id="reply_username_{{ $id }}"></span></strong></span>
                    </div>
                    <button type="button" class="forum-modern-reply-cancel-btn" onclick="cancelReply({{ $id }})" title="{{ __('messages.cancel') }}">
                        <i class="fa fa-times me-1"></i> {{ __('messages.cancel') }}
                    </button>
                </div>
            </div>

            <!-- Hidden Inputs -->
            <input type="hidden" id="parent_comment_id_{{ $id }}" name="parent_id" value="">

            <!-- Forum Markdown Editor Box -->
            <div class="forum-modern-editor-box mt-3">
                <div class="forum-modern-editor-toolbar">
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('txt_comment{{ $id }}'), '**', '**', 'bold text')" title="Bold"><i class="fa fa-bold"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('txt_comment{{ $id }}'), '*', '*', 'italic text')" title="Italic"><i class="fa fa-italic"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('txt_comment{{ $id }}'), '~~', '~~', 'strikethrough')" title="Strike"><i class="fa fa-strikethrough"></i></button>
                    <div class="forum-modern-tool-separator"></div>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('txt_comment{{ $id }}'), '# ', '', 'Heading')" title="Heading"><i class="fa fa-header"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('txt_comment{{ $id }}'), '`', '`', 'code')" title="Code"><i class="fa fa-code"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('txt_comment{{ $id }}'), '\n```\n', '\n```\n', 'code block')" title="Code block"><i class="fa-regular fa-file-code fa-file-code-o"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('txt_comment{{ $id }}'), '> ', '', 'quote text')" title="Quote"><i class="fa fa-quote-left"></i></button>
                    <div class="forum-modern-tool-separator"></div>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('txt_comment{{ $id }}'), '[', '](https://)', 'link title')" title="Link"><i class="fa fa-link"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="document.getElementById('comment_media_file_{{ $id }}').click()" title="{{ __('messages.image') ?? 'Attach Image' }}"><i class="fa fa-image"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.insert(document.getElementById('txt_comment{{ $id }}'), '\n- Item 1\n- Item 2\n')" title="List"><i class="fa fa-list-ul"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.insert(document.getElementById('txt_comment{{ $id }}'), ' 😊 ')" title="Emoji"><i class="fa-regular fa-face-smile fa-smile-o"></i></button>

                    <button type="button" class="forum-modern-tool-btn ms-auto" onclick="toggleForumCommentPreview({{ $id }})" id="preview_btn_{{ $id }}" title="{{ __('messages.preview') ?? 'Toggle Preview' }}">
                        <i class="fa fa-eye"></i> <span class="fs-12 ms-1 d-none d-sm-inline">{{ __('messages.preview') ?? 'Preview' }}</span>
                    </button>
                </div>

                <input type="file" id="comment_media_file_{{ $id }}" class="d-none" accept=".jpg,.jpeg,.png,.gif,.webp,.bmp" style="display: none;">

                <div class="forum-modern-editor-split">
                    <textarea 
                        id="txt_comment{{ $id }}" 
                        name="comment" 
                        class="forum-modern-editor-textarea forum-modern-reply-textarea" 
                        placeholder="{{ __('messages.forum_post_composer_placeholder') ?? 'Write your reply using Markdown...' }}" 
                        rows="4"
                    ></textarea>
                    <div id="comment_preview_{{ $id }}" class="forum-modern-editor-preview"></div>
                </div>

                <!-- Attachment preview bar -->
                <div class="forum-rdx-comment-media-preview is-hidden p-2 border-top bg-light d-flex align-items-center gap-2" id="comment_media_preview_{{ $id }}" style="display: none;">
                    <img src="" alt="preview" id="comment_media_thumb_{{ $id }}" class="rounded" style="max-height: 48px; object-fit: cover;">
                    <span class="fs-12 text-muted text-truncate" id="comment_media_name_{{ $id }}"></span>
                    <button type="button" class="btn btn-sm btn-link text-danger ms-auto" id="comment_media_remove_{{ $id }}" onclick="clearCommentMedia({{ $id }})" title="{{ __('messages.delete') ?? 'Remove' }}">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Footer Action Row -->
            <div class="forum-modern-reply-card-footer mt-3 d-flex align-items-center justify-content-between">
                <span class="fs-12 text-muted">
                    <i class="fa fa-info-circle me-1"></i> {{ __('messages.markdown_supported') ?? 'Markdown formatting supported' }}
                </span>

                <button 
                    type="button" 
                    class="forum-modern-composer-submit-btn" 
                    id="btn_post_comment_{{ $id }}" 
                    onclick="postForumComment({{ $id }})"
                >
                    <i class="fa fa-paper-plane me-1"></i>
                    <span>{{ __('messages.reply') ?? 'Post Reply' }}</span>
                </button>
            </div>
        </div>
    @else
        <div class="forum-modern-guest-reply-card text-center p-4 mt-4 rounded-3 border bg-light">
            <h6 class="fw-bold mb-2">{{ __('messages.login_to_participate') ?? 'Log in to join the discussion' }}</h6>
            <p class="fs-13 text-muted mb-3">{{ __('messages.need_account_to_reply') ?? 'You need an account to post replies and join the discussion.' }}</p>
            <a href="{{ route('login') }}" class="btn forum-modern-btn-primary px-4 py-2">
                <i class="fa fa-sign-in me-1"></i> {{ __('messages.login') ?? 'Sign In' }}
            </a>
        </div>
    @endauth
@elseif(($locked_topic ?? false))
    <div class="alert alert-warning mt-4 d-flex align-items-center gap-2">
        <i class="fa fa-lock fa-lg"></i>
        <span>{{ __('messages.topic_locked_for_comments') ?? 'This topic is locked. New replies are not allowed.' }}</span>
    </div>
@endif

<script>
window.postForumComment = function(id) {
    const input = document.getElementById('txt_comment' + id);
    if (!input) return;

    const text = input.value.trim();
    const mediaFileInput = document.getElementById('comment_media_file_' + id);
    const hasFile = mediaFileInput && mediaFileInput.files && mediaFileInput.files.length > 0;
    if (!text && !hasFile) {
        input.focus();
        return;
    }

    const btn = document.getElementById('btn_post_comment_' + id);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> ' + (@json(__('messages.saving')) || 'Posting...');
    }

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const formData = new FormData();
    formData.append('id', id);
    formData.append('type', 'forum');
    formData.append('comment', text);
    formData.append('limit', 100);
    formData.append('view', 'topic_comments');

    const parentInput = document.getElementById('parent_comment_id_' + id);
    if (parentInput && parentInput.value) {
        formData.append('parent_id', parentInput.value);
    }

    if (hasFile) {
        formData.append('attachment', mediaFileInput.files[0]);
    }

    fetch('{{ route("comment.store") }}', {
        method: 'POST',
        headers: {
            'Accept': 'text/html, application/json',
            'X-CSRF-TOKEN': token
        },
        body: formData
    })
    .then(async response => {
        if (!response.ok) {
            const err = await response.json().catch(() => ({}));
            throw new Error(err.error || 'Failed to post reply');
        }
        return response.text();
    })
    .then(html => {
        const streamContainer = document.querySelector('.post-comment-list-' + id);
        if (streamContainer && html) {
            streamContainer.innerHTML = html;
            if (typeof initHexagons === 'function') {
                initHexagons(streamContainer);
            }
        }
        input.value = '';
        if (window.clearCommentMedia) window.clearCommentMedia(id);
        if (window.cancelReply) window.cancelReply(id);
    })
    .catch(err => {
        alert(err.message || 'Error posting reply');
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane me-1"></i> <span>{{ __("messages.reply") ?? "Post Reply" }}</span>';
        }
    });
};

window.replyToComment = function(username, topicId, parentId) {
    const parentInput = document.getElementById('parent_comment_id_' + topicId);
    const indicator = document.getElementById('reply_indicator_' + topicId);
    const userSpan = document.getElementById('reply_username_' + topicId);
    const textarea = document.getElementById('txt_comment' + topicId);

    if (parentInput && parentId) {
        parentInput.value = parentId;
    }
    if (indicator && userSpan) {
        userSpan.textContent = username;
        indicator.style.display = 'flex';
    }
    if (textarea) {
        const mention = '@' + username + ' ';
        if (!textarea.value.includes(mention)) {
            textarea.value = mention + textarea.value;
        }
        textarea.focus();
        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
};

window.cancelReply = function(topicId) {
    const parentInput = document.getElementById('parent_comment_id_' + topicId);
    const indicator = document.getElementById('reply_indicator_' + topicId);
    if (parentInput) parentInput.value = '';
    if (indicator) indicator.style.display = 'none';
};

window.toggleForumCommentPreview = function(id) {
    const textarea = document.getElementById('txt_comment' + id);
    const preview = document.getElementById('comment_preview_' + id);
    const btn = document.getElementById('preview_btn_' + id);
    if (!textarea || !preview) return;

    if (preview.classList.contains('is-active')) {
        preview.classList.remove('is-active');
        preview.style.display = 'none';
        if (btn) btn.classList.remove('text-primary');
    } else {
        const text = textarea.value;
        if (typeof ForumEditor !== 'undefined' && typeof ForumEditor.renderMarkdown === 'function') {
            preview.innerHTML = ForumEditor.renderMarkdown(text);
        } else {
            preview.innerHTML = text.replace(/\n/g, '<br>');
        }
        preview.classList.add('is-active');
        preview.style.display = 'block';
        if (btn) btn.classList.add('text-primary');
    }
};

window.clearCommentMedia = function(id) {
    const fileInput = document.getElementById('comment_media_file_' + id);
    if (fileInput) fileInput.value = '';
    const preview = document.getElementById('comment_media_preview_' + id);
    if (preview) preview.style.display = 'none';
    const thumb = document.getElementById('comment_media_thumb_' + id);
    if (thumb) thumb.src = '';
    const nameEl = document.getElementById('comment_media_name_' + id);
    if (nameEl) nameEl.textContent = '';
};

window.togglePinComment = function(commentId, topicId) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('/forum/comment/' + commentId + '/pin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        }
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.error || 'Failed to toggle pin');
        }
        return data;
    })
    .then(data => {
        reloadForumComments(topicId);
    })
    .catch(err => {
        alert(err.message || 'Error occurred');
    });
};

window.toggleBestAnswerComment = function(commentId, topicId) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('/forum/comment/' + commentId + '/best-answer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        }
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.error || 'Failed to toggle best answer');
        }
        return data;
    })
    .then(data => {
        reloadForumComments(topicId);
    })
    .catch(err => {
        alert(err.message || 'Error occurred');
    });
};

window.reloadForumComments = function(topicId) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('{{ route("comment.load") }}?id=' + topicId + '&type=forum&limit=100&view=topic_comments', {
        headers: {
            'Accept': 'text/html, application/json',
            'X-CSRF-TOKEN': token
        }
    })
    .then(response => response.text())
    .then(html => {
        const container = document.querySelector('.post-comment-list-' + topicId);
        if (container && html) {
            container.innerHTML = html;
            if (typeof initHexagons === 'function') {
                initHexagons(container);
            }
        }
    })
    .catch(err => {
        console.error('Error reloading comments:', err);
    });
};

window.deleteForumComment = function(commentId) {
    if (!confirm(@json(__('messages.confirm_delete') ?? 'Are you sure you want to delete this comment?'))) {
        return;
    }
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('{{ route("comment.delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
            trashid: commentId,
            type: 'forum'
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || (data.status !== 'success' && !data.success)) {
            throw new Error(data.error || 'Failed to delete comment');
        }
        return data;
    })
    .then(data => {
        const el = document.getElementById('post-' + commentId) || document.getElementById('comment_' + commentId) || document.querySelector('.coment' + commentId);
        if (el) {
            el.style.transition = 'all 0.3s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-10px)';
            setTimeout(() => el.remove(), 300);
        }
    })
    .catch(err => {
        alert(err.message || 'Error occurred');
    });
};

(function() {
    const id = {{ $id }};
    const fileInput = document.getElementById('comment_media_file_' + id);
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                const preview = document.getElementById('comment_media_preview_' + id);
                const thumb = document.getElementById('comment_media_thumb_' + id);
                const nameEl = document.getElementById('comment_media_name_' + id);
                if (nameEl) nameEl.textContent = file.name;
                if (thumb && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        thumb.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
                if (preview) preview.style.display = 'flex';
            }
        });
    }
})();
</script>
