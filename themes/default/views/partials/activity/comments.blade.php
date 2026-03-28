@php
    $showForumRoleBadges = (int) \App\Support\ForumSettings::get('show_role_badges', 1) === 1;
@endphp

@foreach($comments as $comment)
    @php
        $user = $type === 'forum' ? $comment->user : \App\Models\User::find($comment->o_order);
        $rawText = $type === 'forum' ? $comment->txt : $comment->o_valuer;
        $date = $type === 'forum' ? $comment->date : $comment->o_mode;
        $formattedText = \App\Support\ForumCommentFormatter::format($rawText);

        $commentReactionType = 0;
        $reactionTypeString = '';
        if ($type === 'forum') {
            $commentReactionType = 4;
            $reactionTypeString = 'forum_comment';
        } elseif ($type === 'directory') {
            $commentReactionType = 44;
            $reactionTypeString = 'directory_comment';
        } elseif ($type === 'store') {
            $commentReactionType = 444;
            $reactionTypeString = 'store_comment';
        } elseif ($type === 'order') {
            $commentReactionType = 66;
            $reactionTypeString = 'order_comment';
        }

        $forumCategoryId = $forum_category_id ?? null;
        if ($type === 'forum' && $forumCategoryId === null) {
            $forumCategoryId = (int) optional(\App\Models\ForumTopic::find($comment->tid))->cat;
        }

        $myCommentReaction = null;
        $myCommentReactionOption = null;
        if (auth()->check()) {
            $myCommentReaction = \App\Models\Like::where('uid', auth()->id())
                ->where('sid', $comment->id)
                ->where('type', $commentReactionType)
                ->first();

            if ($myCommentReaction) {
                $myCommentReactionOption = \App\Models\Option::where('o_parent', $myCommentReaction->id)
                    ->where('o_type', 'data_reaction')
                    ->first();
            }
        }

        $isOwner = auth()->check() && $user && (int) auth()->id() === (int) $user->id;
        $canDeleteAsForumModerator = auth()->check()
            && $type === 'forum'
            && $forumCategoryId
            && auth()->user()->canModerateForum('delete_comments', (int) $forumCategoryId);
        $canDeleteComment = auth()->check() && ($isOwner || auth()->user()->isAdmin() || $canDeleteAsForumModerator);
    @endphp

    <div class="post-comment coment{{ $comment->id }}" id="comment_{{ $comment->id }}">
        @if($user)
            <a class="user-avatar small no-outline {{ $user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $user->username) }}">
                <div class="user-avatar-content">
                    <div class="hexagon-image-30-32" data-src="{{ $user->img ? asset($user->img) : theme_asset('img/avatar/default.png') }}" style="width: 30px; height: 32px; position: relative;">
                        <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                    </div>
                </div>
                <div class="user-avatar-progress-border">
                    <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;">
                        <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                    </div>
                </div>
                @if($user->isAdmin())
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
        @else
            <div class="user-avatar small no-outline offline">
                <div class="user-avatar-content">
                    <div class="hexagon-image-30-32" data-src="{{ theme_asset('img/avatar/default.png') }}" style="width: 30px; height: 32px; position: relative;">
                        <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                    </div>
                </div>
                <div class="user-avatar-progress-border">
                    <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;">
                        <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                    </div>
                </div>
            </div>
        @endif

        <div class="post-comment-text">
            @if($user)
                <a class="post-comment-text-author" href="{{ route('profile.show', $user->username) }}">{{ $user->username }}</a>
                @if($type === 'forum' && $showForumRoleBadges)
                    <span style="display: block; font-size: 11px; color: #7f85a3;">
                        {{ $user->forumRoleLabel($forumCategoryId ?: null) }}
                    </span>
                @endif
                
                @if($type === 'order' && isset($order))
                    @if($order->best_offer_id == $comment->id)
                        <span class="status-type-label" style="background: #23d2e2; color: #fff; padding: 1px 6px; border-radius: 4px; font-size: 9px; margin-inline-start: 8px;">
                            <i class="fa fa-trophy"></i> {{ __('messages.best_offer') }}
                        </span>
                    @endif
                    
                    @php $rating = (int) $comment->o_mode; @endphp
                    @if($rating > 0)
                        <div class="comment-rating" style="color: #ffc107; font-size: 11px; margin-top: 2px;">
                            @for($i=1; $i<=5; $i++)
                                <i class="fa fa-star{{ $i <= $rating ? '' : '-o' }}"></i>
                            @endfor
                        </div>
                    @endif
                @endif
            @else
                <span class="post-comment-text-author">{{ __('messages.deleted_user') }}</span>
            @endif

            <div class="forum-rdx-comment-body">{!! $formattedText !!}</div>

            @if($type === 'order' && isset($order) && auth()->check() && auth()->id() == $order->uid && $order->statu == 1)
                <div class="order-comment-actions" style="margin-top: 10px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    @if($order->best_offer_id != $comment->id)
                        <form action="{{ route('orders.select_best', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="offer_id" value="{{ $comment->id }}">
                            <button type="submit" class="button white small" style="padding: 0 12px; height: 28px;">
                                <i class="fa fa-check"></i> {{ __('messages.select_best_offer') }}
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('orders.rate', $order->id) }}" method="POST" style="display: flex; gap: 5px; align-items: center;">
                        @csrf
                        <input type="hidden" name="offer_id" value="{{ $comment->id }}">
                        <select name="rating" onchange="this.form.submit()" style="height: 28px; border-radius: 4px; border: 1px solid #eaeaf5; font-size: 11px; padding: 0 8px;">
                            <option value="0">{{ __('messages.rate_offer') }}</option>
                            @for($i=1; $i<=5; $i++)
                                <option value="{{ $i }}" {{ $rating == $i ? 'selected' : '' }}>{{ $i }} {{ __('messages.stars') ?? 'Stars' }}</option>
                            @endfor
                        </select>
                    </form>
                </div>
            @endif
        </div>

        <div class="content-actions">
            <div class="content-action">
                <div class="meta-line">
                    <p class="meta-line-timestamp">{{ \Carbon\Carbon::createFromTimestamp((int) $date)->diffForHumans() }}</p>
                </div>

                @if($canDeleteComment)
                    <div class="meta-line trash_comment{{ $comment->id }}">
                        <a class="meta-line-timestamp" href="javascript:void(0);" onclick="deleteComment({{ $comment->id }}, '{{ $type }}')">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>
                    </div>
                @endif

                @if(auth()->check())
                    <div class="meta-line">
                        <div class="post-option-wrap" style="position: relative;">
                            <a class="meta-line-timestamp" href="javascript:void(0);" id="reaction-btn-comment-{{ $comment->id }}" onclick="toggleReaction({{ $comment->id }}, '{{ $reactionTypeString }}', 'like')">
                                @if($myCommentReactionOption)
                                    <img class="reaction-option-image" src="{{ theme_asset('img/reaction/like.png') }}" width="16" alt="reaction">
                                @else
                                    <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                                @endif
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach

@if(!isset($hide_form) || !$hide_form)
    <div class="comment_form{{ $id }}">
        @auth
            <div class="post-comment-form forum-rdx-comment-form-shell">
                <div class="user-avatar small no-outline {{ auth()->user()->isOnline() ? 'online' : 'offline' }}">
                    <div class="user-avatar-content">
                        <div class="hexagon-image-30-32" data-src="{{ auth()->user()->img ? asset(auth()->user()->img) : theme_asset('img/avatar/01.jpg') }}" style="width: 30px; height: 32px; position: relative;">
                            <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                        </div>
                    </div>
                    <div class="user-avatar-progress-border">
                        <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;">
                            <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                        </div>
                    </div>
                </div>
                <div class="form forum-rdx-comment-form-main">
                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small forum-rdx-comment-composer">
                                <div class="forum-rdx-comment-toolbar" aria-label="{{ __('messages.comment') }}">
                                    <button type="button" class="forum-rdx-tool-btn" data-md-action="bold" data-target="txt_comment{{ $id }}" title="{{ __('messages.markdown_bold') }}">
                                        <i class="fa fa-bold" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="forum-rdx-tool-btn" data-md-action="italic" data-target="txt_comment{{ $id }}" title="{{ __('messages.markdown_italic') }}">
                                        <i class="fa fa-italic" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="forum-rdx-tool-btn" data-md-action="quote" data-target="txt_comment{{ $id }}" title="{{ __('messages.markdown_quote') }}">
                                        <i class="fa fa-quote-left" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="forum-rdx-tool-btn" data-md-action="code" data-target="txt_comment{{ $id }}" title="{{ __('messages.markdown_code') }}">
                                        <i class="fa fa-code" aria-hidden="true"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="forum-rdx-tool-btn"
                                        data-md-action="link"
                                        data-target="txt_comment{{ $id }}"
                                        data-link-prompt="{{ __('messages.markdown_link_prompt') }}"
                                        data-link-label-prompt="{{ __('messages.markdown_link_label_prompt') }}"
                                        data-link-default-label="{{ __('messages.markdown_link_default_label') }}"
                                        title="{{ __('messages.markdown_link') }}"
                                    >
                                        <i class="fa fa-link" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="forum-rdx-tool-btn" data-md-action="emoji" data-target="txt_comment{{ $id }}" title="{{ __('messages.markdown_emoji') }}">
                                        <i class="fa fa-smile-o" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <textarea id="txt_comment{{ $id }}" name="comment_text" class="forum-rdx-comment-input" data-md-editor="1" placeholder="{{ __('messages.your_comment') }}"></textarea>
                                <div class="forum-rdx-comment-footer">
                                    <button type="button" class="btn forum-rdx-comment-send" data-comment-submit="{{ $id }}" onclick="postComment({{ $id }}, '{{ $type }}')">
                                        <svg class="interactive-input-icon icon-send-message">
                                            <use xlink:href="#svg-send-message"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </div>
@elseif(($locked_topic ?? false) && $type === 'forum')
    <div class="alert alert-warning" style="margin: 12px 0;">
        {{ __('messages.topic_locked_for_comments') }}
    </div>
@elseif(($hide_form ?? false) && $type === 'order')
    <div class="alert alert-warning" style="margin: 12px 0;">
        {{ __('messages.order_closed_for_comments') }}
    </div>
@endif

<p class="post-comment-heading comment_heading{{ $id }}" onclick="loadComments({{ $id }}, '{{ $type }}', {{ $limit + 5 }})">
    {{ __('messages.load_more_comments') }} <span class="highlighted">+</span>
</p>

<script src="{{ theme_asset('js/global.hexagons.js') }}"></script>
