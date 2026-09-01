@once
    @php
        $mode = \Illuminate\Support\Facades\Cookie::get('modedark', 'css');
        $cssPath = $mode === 'css_d' ? 'css_d' : 'css';
    @endphp

    @include('theme::store.partials.kb-superdesign-formatter')

    @push('head')
        <link
            id="theme-forum-redesign"
            data-theme-link="true"
            href="{{ theme_asset($cssPath . '/forum-redesign.css') }}"
            rel="stylesheet"
            type="text/css"
        />
    @endpush

    @push('scripts')
        <script src="{{ theme_asset('js/forum-redesign.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                function enhanceForumCodeBlocks() {
                    if (window.enhanceSuperdesignKbContent) {
                        document.querySelectorAll('.forum-post-paragraph, .forum-content, .activity-post-card, .markdown-content, .post-comment-list, .comment-box').forEach(function(el) {
                            window.enhanceSuperdesignKbContent(el);
                        });
                    }
                }
                enhanceForumCodeBlocks();
                setTimeout(enhanceForumCodeBlocks, 500);
                setTimeout(enhanceForumCodeBlocks, 1500);
            });
        </script>
    @endpush
@endonce
