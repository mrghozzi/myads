@auth
<div id="forumComposer" class="forum-modern-composer" aria-hidden="true">
    <div class="forum-modern-composer-header" onclick="ForumComposer.minimize()">
        <div class="forum-modern-composer-title">
            <i class="fa-solid fa-pen-to-square fa-edit text-primary"></i>
            <span id="forumComposerTitleLabel">{{ __('messages.w_new_tpc') ?? 'Start a Discussion' }}</span>
        </div>
        <div class="forum-modern-composer-tools" onclick="event.stopPropagation()">
            <button type="button" class="forum-modern-composer-tool-btn" onclick="ForumComposer.togglePreview()" title="{{ __('messages.composer_preview') }}">
                <i class="fa fa-eye"></i>
            </button>
            <button type="button" class="forum-modern-composer-tool-btn" onclick="ForumComposer.toggleFullscreen()" title="{{ __('messages.composer_fullscreen') }}">
                <i class="fa fa-arrows-alt"></i>
            </button>
            <button type="button" class="forum-modern-composer-tool-btn" onclick="ForumComposer.minimize()" title="{{ __('messages.composer_minimize') }}">
                <i class="fa fa-minus"></i>
            </button>
            <button type="button" class="forum-modern-composer-tool-btn" onclick="ForumComposer.close()" title="{{ __('messages.composer_close') }}">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>

    <div class="forum-modern-composer-body">
        <form id="forumComposerForm" method="POST" action="{{ route('forum.store') }}" enctype="multipart/form-data">
            @csrf
            <div id="forumComposerAlert" class="alert alert-danger d-none mb-3 py-2 px-3 fs-13"></div>

            <div class="forum-modern-composer-inputs mb-3">
                <select id="forumComposerCategory" name="cat" class="forum-modern-composer-cat" required>
                    <option value="">{{ __('messages.composer_select_category') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" class="fw-bold">{{ $cat->name }}</option>
                        @if($cat->children && $cat->children->isNotEmpty())
                            @foreach($cat->children as $sub)
                                <option value="{{ $sub->id }}">&nbsp;&nbsp;&nbsp;↳ {{ $sub->name }}</option>
                            @endforeach
                        @endif
                    @endforeach
                </select>

                <input 
                    type="text" 
                    id="forumComposerTitle" 
                    name="name" 
                    class="forum-modern-composer-title-input" 
                    placeholder="{{ __('messages.composer_discussion_title') }}" 
                    required 
                    autocomplete="off"
                >
            </div>

            <!-- Forum Markdown Editor Box -->
            <div class="forum-modern-editor-box mb-3">
                <div class="forum-modern-editor-toolbar">
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('forumComposerText'), '**', '**', 'bold text')" title="Bold"><i class="fa fa-bold"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('forumComposerText'), '*', '*', 'italic text')" title="Italic"><i class="fa fa-italic"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('forumComposerText'), '~~', '~~', 'strikethrough')" title="Strike"><i class="fa fa-strikethrough"></i></button>
                    <div class="forum-modern-tool-separator"></div>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('forumComposerText'), '# ', '', 'Heading')" title="Heading"><i class="fa fa-heading"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('forumComposerText'), '`', '`', 'code')" title="Code"><i class="fa fa-code"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('forumComposerText'), '\n```\n', '\n```\n', 'code block')" title="Code block"><i class="fa-regular fa-file-code fa-file-code-o"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('forumComposerText'), '> ', '', 'quote text')" title="Quote"><i class="fa fa-quote-left"></i></button>
                    <div class="forum-modern-tool-separator"></div>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('forumComposerText'), '[', '](https://)', 'link title')" title="Link"><i class="fa fa-link"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.wrap(document.getElementById('forumComposerText'), '![alt](', ')', 'https://')" title="Image"><i class="fa fa-image"></i></button>
                    <button type="button" class="forum-modern-tool-btn" onclick="ForumEditor.insert(document.getElementById('forumComposerText'), '\n- Item 1\n- Item 2\n')" title="List"><i class="fa fa-list-ul"></i></button>
                </div>

                <div class="forum-modern-editor-split">
                    <textarea 
                        id="forumComposerText" 
                        name="txt" 
                        class="forum-modern-editor-textarea" 
                        placeholder="{{ __('messages.forum_post_composer_placeholder') ?? 'Write your discussion content using Markdown...' }}" 
                        rows="8" 
                        required
                    ></textarea>
                    <div id="forumComposerPreview" class="forum-modern-editor-preview"></div>
                </div>
            </div>

            <!-- Composer Footer Actions -->
            <div class="forum-modern-composer-footer">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fs-12"><i class="fa-brands fa-markdown fab fa-markdown me-1"></i>Markdown & Emojis supported</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-light btn-sm fw-bold px-3" onclick="ForumComposer.close()">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit" id="forumComposerSubmitBtn" class="forum-modern-composer-submit-btn">
                        <i class="fa fa-paper-plane"></i>
                        <span>{{ __('messages.post') ?? 'Post Discussion' }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const composerForm = document.getElementById('forumComposerForm');
    if (!composerForm) return;

    composerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('forumComposerSubmitBtn');
        const alertBox = document.getElementById('forumComposerAlert');
        
        alertBox.classList.add('d-none');
        alertBox.textContent = '';
        submitBtn.disabled = true;
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{ __("messages.posting") ?? "Posting..." }}';

        const formData = new FormData(composerForm);

        fetch(composerForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else if (data.topic_id) {
                window.location.href = '{{ url("/t") }}' + data.topic_id;
            } else {
                window.location.reload();
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            let msg = '{{ __("messages.request_failed") ?? "Failed to publish discussion." }}';
            if (err && err.errors) {
                msg = Object.values(err.errors).flat().join('<br>');
            } else if (err && err.message) {
                msg = err.message;
            }
            alertBox.innerHTML = msg;
            alertBox.classList.remove('d-none');
        });
    });
});
</script>
@endauth
