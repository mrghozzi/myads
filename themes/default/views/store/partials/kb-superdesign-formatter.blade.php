{{-- Superdesign Code & Diagram Formatter for Knowledgebase (/kb/*) --}}
@once
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
<style>
    /* ============================================================
       Superdesign Code Windows & Mermaid Diagrams for Knowledgebase
       ============================================================ */

    /* Container base */
    .kb-article-body,
    .markdown-content,
    .markdown-content-preview {
        color: var(--store-shell-text, #5d607a);
        line-height: 1.8;
        font-size: 0.98rem;
    }

    body[data-theme="css_d"] .kb-article-body,
    body[data-theme="css_d"] .markdown-content,
    body[data-theme="css_d"] .markdown-content-preview {
        color: var(--store-shell-text, #c8d1e8);
    }

    .markdown-content h1, .markdown-content-preview h1 { font-size: 1.75rem; font-weight: 800; margin: 1.8rem 0 1rem; color: var(--store-shell-title, #3e3f5e); }
    .markdown-content h2, .markdown-content-preview h2 { font-size: 1.45rem; font-weight: 800; margin: 1.5rem 0 0.85rem; color: var(--store-shell-title, #3e3f5e); }
    .markdown-content h3, .markdown-content-preview h3 { font-size: 1.2rem; font-weight: 700; margin: 1.3rem 0 0.75rem; color: var(--store-shell-title, #3e3f5e); }
    .markdown-content p, .markdown-content-preview p { margin-bottom: 1.15rem; }

    /* Inline Code */
    .markdown-content :not(pre) > code,
    .markdown-content-preview :not(pre) > code {
        display: inline-block;
        padding: 0.18rem 0.5rem;
        margin: 0 0.2rem;
        border-radius: 6px;
        background: rgba(97, 93, 250, 0.08);
        border: 1px solid rgba(97, 93, 250, 0.18);
        color: var(--store-shell-accent, #615dfa);
        font-family: 'JetBrains Mono', 'Fira Code', Consolas, Monaco, monospace;
        font-size: 0.86em;
        font-weight: 600;
        direction: ltr !important;
        unicode-bidi: embed;
    }

    body[data-theme="css_d"] .markdown-content :not(pre) > code,
    body[data-theme="css_d"] .markdown-content-preview :not(pre) > code {
        background: rgba(140, 138, 255, 0.14);
        border-color: rgba(140, 138, 255, 0.24);
        color: #a5b4fc;
    }

    /* Superdesign macOS-style Code Window Card */
    .kb-code-window {
        position: relative;
        margin: 1.5rem 0 1.75rem;
        border-radius: 16px;
        background: #161b28;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.22), 0 2px 6px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        direction: ltr !important;
        text-align: left !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .kb-code-window:hover {
        border-color: rgba(97, 93, 250, 0.35);
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.28), 0 0 0 1px rgba(97, 93, 250, 0.15);
    }

    /* Window Header / Toolbar */
    .kb-code-window__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 10px 16px;
        background: rgba(255, 255, 255, 0.03);
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        user-select: none;
    }

    .kb-code-window__left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* macOS Window Action Dots */
    .kb-code-window__dots {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .kb-code-window__dot {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        display: inline-block;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .kb-code-window__dot--close { background: #ff5f56; box-shadow: 0 0 6px rgba(255, 95, 86, 0.4); }
    .kb-code-window__dot--min   { background: #ffbd2e; box-shadow: 0 0 6px rgba(255, 189, 46, 0.4); }
    .kb-code-window__dot--max   { background: #27c93f; box-shadow: 0 0 6px rgba(39, 201, 63, 0.4); }

    .kb-code-window:hover .kb-code-window__dot {
        transform: scale(1.05);
    }

    /* Language Badge */
    .kb-code-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        font-family: 'JetBrains Mono', Consolas, monospace;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        border: 1px solid transparent;
        line-height: 1.3;
    }

    .kb-code-badge i { font-size: 0.8rem; }

    /* Language specific colors */
    .kb-code-badge--php {
        background: rgba(136, 146, 190, 0.15);
        color: #a5b4fc;
        border-color: rgba(136, 146, 190, 0.3);
    }

    .kb-code-badge--blade {
        background: rgba(240, 83, 64, 0.16);
        color: #fb7185;
        border-color: rgba(240, 83, 64, 0.35);
    }

    .kb-code-badge--html {
        background: rgba(227, 79, 38, 0.16);
        color: #fb923c;
        border-color: rgba(227, 79, 38, 0.35);
    }

    .kb-code-badge--css {
        background: rgba(38, 77, 228, 0.16);
        color: #60a5fa;
        border-color: rgba(38, 77, 228, 0.35);
    }

    .kb-code-badge--json {
        background: rgba(245, 158, 11, 0.16);
        color: #fcd34d;
        border-color: rgba(245, 158, 11, 0.35);
    }

    .kb-code-badge--text {
        background: rgba(148, 163, 184, 0.14);
        color: #cbd5e1;
        border-color: rgba(148, 163, 184, 0.28);
    }

    .kb-code-badge--code {
        background: rgba(97, 93, 250, 0.16);
        color: #c4b5fd;
        border-color: rgba(97, 93, 250, 0.35);
    }

    .kb-code-badge--mermaid {
        background: rgba(35, 210, 226, 0.16);
        color: #38bdf8;
        border-color: rgba(35, 210, 226, 0.35);
    }

    /* Copy Button */
    .kb-copy-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #cbd5e1;
        font-family: inherit;
        font-size: 0.74rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
    }

    .kb-copy-btn:hover {
        background: rgba(97, 93, 250, 0.22);
        border-color: rgba(97, 93, 250, 0.4);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .kb-copy-btn.copied {
        background: rgba(34, 197, 94, 0.2) !important;
        border-color: rgba(34, 197, 94, 0.45) !important;
        color: #4ade80 !important;
    }

    /* Window Body */
    .kb-code-window__body {
        display: flex;
        overflow-x: auto;
        padding: 0;
        margin: 0;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
    }

    .kb-code-window__body::-webkit-scrollbar {
        height: 6px;
    }
    .kb-code-window__body::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.18);
        border-radius: 4px;
    }
    .kb-code-window__body::-webkit-scrollbar-track {
        background: transparent;
    }

    /* Line Numbers Gutter */
    .kb-code-gutter {
        display: flex;
        flex-direction: column;
        padding: 16px 12px 16px 16px;
        color: rgba(255, 255, 255, 0.25);
        font-family: 'JetBrains Mono', 'Fira Code', Consolas, Monaco, monospace;
        font-size: 0.84rem;
        line-height: 1.75;
        text-align: right;
        user-select: none;
        border-right: 1px solid rgba(255, 255, 255, 0.06);
        background: rgba(0, 0, 0, 0.15);
        min-width: 44px;
    }

    .kb-code-content {
        flex: 1;
        margin: 0;
        padding: 16px 20px;
        background: transparent !important;
        color: #e2e8f0;
        font-family: 'JetBrains Mono', 'Fira Code', Consolas, Monaco, monospace;
        font-size: 0.86rem;
        line-height: 1.75;
        white-space: pre;
        word-break: normal;
        overflow-x: auto;
        direction: ltr !important;
        text-align: left !important;
    }

    .kb-code-content code {
        display: block;
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 0 !important;
        color: inherit !important;
        font-family: inherit !important;
        font-size: inherit !important;
        line-height: inherit !important;
        direction: ltr !important;
        text-align: left !important;
    }

    /* Blade Syntax Custom Highlighting Extensions */
    .hljs-blade-directive {
        color: #f43f5e;
        font-weight: 700;
    }
    .hljs-blade-echo {
        color: #fb923c;
        font-weight: 600;
    }

    /* ============================================================
       Superdesign Mermaid Diagram Card
       ============================================================ */
    .kb-mermaid-card {
        position: relative;
        margin: 1.6rem 0 2rem;
        border-radius: 20px;
        background: var(--store-shell-surface, #ffffff);
        border: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.18));
        box-shadow: 0 16px 40px rgba(94, 92, 154, 0.08);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    body[data-theme="css_d"] .kb-mermaid-card {
        background: var(--store-shell-surface, #1f2637);
        border-color: var(--store-shell-border, #2f3850);
        box-shadow: 0 20px 48px rgba(0, 0, 0, 0.32);
    }

    .kb-mermaid-card:hover {
        border-color: rgba(97, 93, 250, 0.35);
        box-shadow: 0 22px 50px rgba(97, 93, 250, 0.12);
    }

    .kb-mermaid-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 20px;
        background: var(--store-shell-soft, #f7f8fd);
        border-bottom: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.18));
    }

    body[data-theme="css_d"] .kb-mermaid-card__header {
        background: var(--store-shell-soft, #242d40);
    }

    .kb-mermaid-card__title-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .kb-mermaid-card__title {
        font-size: 0.92rem;
        font-weight: 800;
        color: var(--store-shell-title, #3e3f5e);
        margin: 0;
    }

    body[data-theme="css_d"] .kb-mermaid-card__title {
        color: #ffffff;
    }

    .kb-mermaid-card__actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .kb-mermaid-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        background: var(--store-shell-chip-bg, #ffffff);
        border: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.2));
        color: var(--store-shell-title, #3e3f5e);
        font-size: 0.76rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    body[data-theme="css_d"] .kb-mermaid-toggle-btn {
        background: #1f2637;
        color: #c8d1e8;
    }

    .kb-mermaid-toggle-btn:hover,
    .kb-mermaid-toggle-btn.active {
        background: var(--store-shell-accent, #615dfa);
        border-color: var(--store-shell-accent, #615dfa);
        color: #ffffff;
    }

    /* Diagram Viewport */
    .kb-mermaid-viewport {
        padding: 28px 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 180px;
        background: radial-gradient(circle at center, rgba(97, 93, 250, 0.04) 0%, transparent 70%);
        overflow-x: auto;
        direction: ltr !important;
        text-align: center;
    }

    body[data-theme="css_d"] .kb-mermaid-viewport {
        background: radial-gradient(circle at center, rgba(97, 93, 250, 0.08) 0%, transparent 70%);
    }

    .kb-mermaid-viewport svg {
        max-width: 100% !important;
        height: auto !important;
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.05));
    }

    .kb-mermaid-source-view {
        display: none;
        margin: 0;
        border: 0;
        border-radius: 0;
        box-shadow: none;
    }

    .kb-mermaid-card.show-source .kb-mermaid-viewport {
        display: none;
    }
    .kb-mermaid-card.show-source .kb-mermaid-source-view {
        display: block;
    }

    .kb-mermaid-loading {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--store-shell-muted, #8f91ac);
        font-size: 0.88rem;
        font-weight: 600;
    }

    .kb-mermaid-error {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 16px;
        color: #ef4444;
        font-size: 0.86rem;
        font-weight: 600;
        text-align: center;
    }

    /* ============================================================
       Editor Quick-Insert Snippets Toolbar
       ============================================================ */
    .kb-snippets-toolbar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 12px 14px;
        margin-bottom: 12px;
        background: var(--store-shell-soft, #f7f8fd);
        border: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.18));
        border-radius: 14px;
    }

    body[data-theme="css_d"] .kb-snippets-toolbar {
        background: var(--store-shell-soft, #242d40);
    }

    .kb-snippets-toolbar__label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.76rem;
        font-weight: 800;
        color: var(--store-shell-muted, #8f91ac);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-inline-end: 4px;
    }

    .kb-snippet-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 11px;
        border-radius: 8px;
        background: var(--store-shell-surface, #ffffff);
        border: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.2));
        color: var(--store-shell-title, #3e3f5e);
        font-size: 0.76rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body[data-theme="css_d"] .kb-snippet-btn {
        background: #1f2637;
        color: #c8d1e8;
    }

    .kb-snippet-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(97, 93, 250, 0.18);
        border-color: var(--store-shell-accent, #615dfa);
        color: var(--store-shell-accent, #615dfa);
    }

    .kb-snippet-btn i { font-size: 0.8rem; }
</style>

{{-- Highlighting and Mermaid CDN libraries --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/json.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/bash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/sql.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mermaid@10.9.1/dist/mermaid.min.js"></script>

<script>
    (function () {
        // Configure Mermaid
        let mermaidInitialized = false;
        function getMermaidTheme() {
            const isDark = (document.documentElement.dataset.theme === 'css_d') ||
                           (window.__themeMode === 'css_d') ||
                           document.documentElement.classList.contains('app-skin-dark');
            return isDark ? 'dark' : 'default';
        }

        function initMermaidLib() {
            if (typeof mermaid !== 'undefined') {
                try {
                    mermaid.initialize({
                        startOnLoad: false,
                        theme: getMermaidTheme(),
                        securityLevel: 'loose',
                        fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
                        flowchart: { curve: 'basis', htmlLabels: true },
                        sequence: { showSequenceNumbers: true }
                    });
                    mermaidInitialized = true;
                } catch (err) {
                    console.warn('[Mermaid] init warning:', err);
                }
            }
        }

        initMermaidLib();

        // Custom Blade Highlighting for Highlight.js
        function highlightBlade(code) {
            // First highlight as PHP / HTML
            let highlighted = '';
            try {
                highlighted = hljs.highlight(code, { language: 'php', ignoreIllegals: true }).value;
            } catch(e) {
                highlighted = escapeHtml(code);
            }

            // Enhance Blade directives
            highlighted = highlighted.replace(/(@(?:extends|section|endsection|yield|include|if|elseif|else|endif|foreach|endforeach|for|endfor|while|endwhile|empty|endempty|auth|endauth|guest|endguest|switch|case|break|default|endswitch|push|endpush|stack|php|endphp|csrf|method|props|aware|slot|endslot|class|checked|disabled|selected|readonly|required|error|enderror|once|endonce|livewire|vite)\b)(?:\((.*?)\))?/g, function(match, dir, args) {
                let res = '<span class="hljs-blade-directive">' + dir + '</span>';
                if (args !== undefined) {
                    res += '(' + args + ')';
                }
                return res;
            });

            // Enhance Blade echo statements
            const echoRegex = new RegExp('(\\{' + '\\{[\\s\\S]*?\\}' + '\\})', 'g');
            const rawEchoRegex = new RegExp('(\\{!' + '![\\s\\S]*?!' + '!\\})', 'g');
            highlighted = highlighted.replace(echoRegex, '<span class="hljs-blade-echo">$1</span>');
            highlighted = highlighted.replace(rawEchoRegex, '<span class="hljs-blade-echo">$1</span>');

            return highlighted;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function getLanguageMeta(langClass) {
            const raw = (langClass || '').toLowerCase().replace(/^language-/, '').trim();
            switch (raw) {
                case 'mermaid':
                    return { key: 'mermaid', label: 'Mermaid', icon: 'fa fa-sitemap', badgeClass: 'kb-code-badge--mermaid' };
                case 'php':
                    return { key: 'php', label: 'PHP', icon: 'fa fa-code', badgeClass: 'kb-code-badge--php' };
                case 'blade':
                case 'laravel':
                    return { key: 'blade', label: 'Blade', icon: 'fa fa-bolt', badgeClass: 'kb-code-badge--blade' };
                case 'html':
                case 'xml':
                case 'svg':
                    return { key: 'html', label: 'HTML', icon: 'fa fa-html5', badgeClass: 'kb-code-badge--html' };
                case 'css':
                case 'scss':
                case 'less':
                    return { key: 'css', label: 'CSS', icon: 'fa fa-css3', badgeClass: 'kb-code-badge--css' };
                case 'json':
                    return { key: 'json', label: 'JSON', icon: 'fa fa-code', badgeClass: 'kb-code-badge--json' };
                case 'text':
                case 'txt':
                case 'plain':
                    return { key: 'text', label: 'Text', icon: 'fa fa-file-text-o', badgeClass: 'kb-code-badge--text' };
                case 'js':
                case 'javascript':
                    return { key: 'javascript', label: 'JavaScript', icon: 'fa fa-code', badgeClass: 'kb-code-badge--code' };
                case 'sql':
                    return { key: 'sql', label: 'SQL', icon: 'fa fa-database', badgeClass: 'kb-code-badge--code' };
                case 'bash':
                case 'sh':
                case 'shell':
                    return { key: 'bash', label: 'Bash', icon: 'fa fa-terminal', badgeClass: 'kb-code-badge--code' };
                case 'code':
                default:
                    return { key: raw || 'code', label: raw ? raw.toUpperCase() : 'Code', icon: 'fa fa-code', badgeClass: 'kb-code-badge--code' };
            }
        }

        // Main Enhancement Function
        window.enhanceSuperdesignKbContent = function (container) {
            if (!container) return;

            const preBlocks = container.querySelectorAll('pre');
            preBlocks.forEach((pre, idx) => {
                if (pre.getAttribute('data-superdesign-enhanced')) return;
                pre.setAttribute('data-superdesign-enhanced', 'true');

                const codeEl = pre.querySelector('code');
                const rawCode = codeEl ? (codeEl.textContent || codeEl.innerText) : (pre.textContent || pre.innerText);
                const classAttr = (codeEl ? codeEl.getAttribute('class') : '') || pre.getAttribute('class') || '';
                const match = classAttr.match(/language-([a-zA-Z0-9_\-]+)/);
                const detectedLang = match ? match[1] : '';
                const langMeta = getLanguageMeta(detectedLang);

                // --- 1. MERMAID DIAGRAMS ---
                if (langMeta.key === 'mermaid') {
                    const uniqueId = 'mermaid-' + Math.random().toString(36).substring(2, 9) + '-' + idx;
                    const card = document.createElement('div');
                    card.className = 'kb-mermaid-card';
                    card.innerHTML = `
                        <div class="kb-mermaid-card__header">
                            <div class="kb-mermaid-card__title-wrap">
                                <span class="kb-code-badge kb-code-badge--mermaid"><i class="fa fa-sitemap"></i> Mermaid</span>
                                <span class="kb-mermaid-card__title">{{ __('messages.diagram') ?? 'Diagram' }}</span>
                            </div>
                            <div class="kb-mermaid-card__actions">
                                <button type="button" class="kb-mermaid-toggle-btn js-mermaid-toggle" data-state="diagram">
                                    <i class="fa fa-code"></i>&nbsp;<span>{{ __('messages.code') ?? 'Code' }}</span>
                                </button>
                                <button type="button" class="kb-copy-btn js-kb-copy" data-clipboard-text="${escapeHtml(rawCode.trim())}">
                                    <i class="fa fa-clone"></i>&nbsp;<span>{{ __('messages.copy') ?? 'Copy' }}</span>
                                </button>
                            </div>
                        </div>
                        <div class="kb-mermaid-viewport" id="${uniqueId}-viewport">
                            <div class="kb-mermaid-loading"><i class="fa fa-spinner fa-spin"></i> {{ __('messages.loading') ?? 'Rendering diagram...' }}</div>
                        </div>
                        <div class="kb-code-window kb-mermaid-source-view">
                            <div class="kb-code-window__header">
                                <div class="kb-code-window__left">
                                    <div class="kb-code-window__dots">
                                        <span class="kb-code-window__dot kb-code-window__dot--close"></span>
                                        <span class="kb-code-window__dot kb-code-window__dot--min"></span>
                                        <span class="kb-code-window__dot kb-code-window__dot--max"></span>
                                    </div>
                                    <span class="kb-code-badge kb-code-badge--mermaid"><i class="fa fa-sitemap"></i> Mermaid Source</span>
                                </div>
                            </div>
                            <div class="kb-code-window__body">
                                <pre class="kb-code-content"><code>${escapeHtml(rawCode.trim())}</code></pre>
                            </div>
                        </div>
                    `;

                    pre.parentNode.replaceChild(card, pre);

                    // Toggle logic
                    const toggleBtn = card.querySelector('.js-mermaid-toggle');
                    toggleBtn.addEventListener('click', function () {
                        const isShowingSource = card.classList.toggle('show-source');
                        if (isShowingSource) {
                            toggleBtn.classList.add('active');
                            toggleBtn.innerHTML = '<i class="fa fa-eye"></i>&nbsp;<span>{{ __("messages.preview") ?? "Diagram" }}</span>';
                        } else {
                            toggleBtn.classList.remove('active');
                            toggleBtn.innerHTML = '<i class="fa fa-code"></i>&nbsp;<span>{{ __("messages.code") ?? "Code" }}</span>';
                        }
                    });

                    // Render Mermaid SVG asynchronously
                    if (typeof mermaid !== 'undefined') {
                        const cleanGraphDef = rawCode.trim();
                        try {
                            mermaid.render(uniqueId + '-svg', cleanGraphDef).then(result => {
                                const viewport = document.getElementById(uniqueId + '-viewport');
                                if (viewport) {
                                    viewport.innerHTML = result.svg;
                                }
                            }).catch(err => {
                                console.error('[Mermaid Render Error]', err);
                                const viewport = document.getElementById(uniqueId + '-viewport');
                                if (viewport) {
                                    viewport.innerHTML = `
                                        <div class="kb-mermaid-error">
                                            <i class="fa fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
                                            <span>{{ __('messages.mermaid_render_error') ?? 'Could not render Mermaid diagram syntax.' }}</span>
                                            <small style="color: var(--store-shell-muted);">${escapeHtml(err.message || 'Syntax Error')}</small>
                                        </div>
                                    `;
                                    // Default to showing code
                                    card.classList.add('show-source');
                                }
                            });
                        } catch (err) {
                            console.error('[Mermaid Exception]', err);
                            const viewport = document.getElementById(uniqueId + '-viewport');
                            if (viewport) {
                                viewport.innerHTML = `<div class="kb-mermaid-error"><span>${escapeHtml(err.message || 'Error')}</span></div>`;
                            }
                        }
                    }
                    return;
                }

                // --- 2. CODE BLOCKS (PHP, Blade, HTML, CSS, JSON, Text, Code) ---
                const cleanCode = rawCode.replace(/\r\n/g, '\n').replace(/\r/g, '\n').trimEnd();
                const lines = cleanCode.split('\n');
                const lineCount = lines.length;

                // Build line numbers gutter
                let gutterHtml = '';
                for (let i = 1; i <= lineCount; i++) {
                    gutterHtml += `<span>${i}</span>`;
                }

                // Highlight code according to language
                let highlightedCode = '';
                if (langMeta.key === 'blade') {
                    highlightedCode = highlightBlade(cleanCode);
                } else if (langMeta.key === 'text') {
                    highlightedCode = escapeHtml(cleanCode);
                } else if (typeof hljs !== 'undefined') {
                    try {
                        if (langMeta.key && hljs.getLanguage(langMeta.key)) {
                            highlightedCode = hljs.highlight(cleanCode, { language: langMeta.key, ignoreIllegals: true }).value;
                        } else {
                            highlightedCode = hljs.highlightAuto(cleanCode).value;
                        }
                    } catch (e) {
                        highlightedCode = escapeHtml(cleanCode);
                    }
                } else {
                    highlightedCode = escapeHtml(cleanCode);
                }

                const windowCard = document.createElement('div');
                windowCard.className = 'kb-code-window';
                windowCard.innerHTML = `
                    <div class="kb-code-window__header">
                        <div class="kb-code-window__left">
                            <div class="kb-code-window__dots">
                                <span class="kb-code-window__dot kb-code-window__dot--close"></span>
                                <span class="kb-code-window__dot kb-code-window__dot--min"></span>
                                <span class="kb-code-window__dot kb-code-window__dot--max"></span>
                            </div>
                            <span class="kb-code-badge ${langMeta.badgeClass}">
                                <i class="${langMeta.icon}"></i> ${langMeta.label}
                            </span>
                        </div>
                        <div class="kb-code-window__actions">
                            <button type="button" class="kb-copy-btn js-kb-copy" data-clipboard-text="${escapeHtml(cleanCode)}">
                                <i class="fa fa-clone"></i>&nbsp;<span>{{ __('messages.copy') ?? 'Copy' }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="kb-code-window__body">
                        <div class="kb-code-gutter">${gutterHtml}</div>
                        <pre class="kb-code-content"><code>${highlightedCode}</code></pre>
                    </div>
                `;

                pre.parentNode.replaceChild(windowCard, pre);
            });

            // Bind Copy Buttons inside container
            container.querySelectorAll('.js-kb-copy').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const textToCopy = this.getAttribute('data-clipboard-text') || '';
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(textToCopy).then(() => {
                            showCopiedFeedback(btn);
                        }).catch(() => fallbackCopy(textToCopy, btn));
                    } else {
                        fallbackCopy(textToCopy, btn);
                    }
                });
            });
        };

        function showCopiedFeedback(btn) {
            btn.classList.add('copied');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-check"></i>&nbsp;<span>{{ __("messages.copied") ?? "Copied!" }}</span>';
            setTimeout(() => {
                btn.classList.remove('copied');
                btn.innerHTML = originalHtml;
            }, 2200);
        }

        function fallbackCopy(text, btn) {
            const temp = document.createElement('textarea');
            temp.value = text;
            temp.style.position = 'fixed';
            temp.style.opacity = '0';
            document.body.appendChild(temp);
            temp.select();
            try {
                document.execCommand('copy');
                showCopiedFeedback(btn);
            } catch (err) {
                console.error('Fallback copy failed', err);
            }
            document.body.removeChild(temp);
        }

        // ============================================================
        // Snippets Insertion for Knowledgebase Editor
        // ============================================================
        window.initKbSnippetsToolbar = function(textareaId) {
            const textarea = document.getElementById(textareaId);
            if (!textarea || textarea.getAttribute('data-snippets-initialized')) return;
            textarea.setAttribute('data-snippets-initialized', 'true');

            const toolbar = document.createElement('div');
            toolbar.className = 'kb-snippets-toolbar';
            toolbar.innerHTML = `
                <span class="kb-snippets-toolbar__label"><i class="fa fa-magic"></i> {{ __('messages.insert_code_template') ?? 'Quick Code & Diagram:' }}</span>
                <button type="button" class="kb-snippet-btn" data-snippet="mermaid"><i class="fa fa-sitemap" style="color: #38bdf8;"></i> Mermaid</button>
                <button type="button" class="kb-snippet-btn" data-snippet="php"><i class="fa fa-code" style="color: #a5b4fc;"></i> PHP</button>
                <button type="button" class="kb-snippet-btn" data-snippet="blade"><i class="fa fa-bolt" style="color: #fb7185;"></i> Blade</button>
                <button type="button" class="kb-snippet-btn" data-snippet="html"><i class="fa fa-html5" style="color: #fb923c;"></i> HTML</button>
                <button type="button" class="kb-snippet-btn" data-snippet="css"><i class="fa fa-css3" style="color: #60a5fa;"></i> CSS</button>
                <button type="button" class="kb-snippet-btn" data-snippet="json"><i class="fa fa-code" style="color: #fcd34d;"></i> JSON</button>
                <button type="button" class="kb-snippet-btn" data-snippet="text"><i class="fa fa-file-text-o" style="color: #cbd5e1;"></i> Text</button>
                <button type="button" class="kb-snippet-btn" data-snippet="code"><i class="fa fa-terminal" style="color: #c4b5fd;"></i> Code</button>
            `;

            textarea.parentNode.insertBefore(toolbar, textarea);

            const snippetTemplates = {
                mermaid: "\n```mermaid\ngraph TD\n    A[Start / البداية] --> B{Process / المعالجة}\n    B -->|Success / نجاح| C[Result / النتيجة]\n    B -->|Error / خطأ| D[Retry / إعادة المحاولة]\n```\n",
                php: "\n```php\n<" + "?php\n\nnamespace App\\Services;\n\nclass ExampleService\n{\n    public function handle(string $data): array\n    {\n        return ['status' => true, 'payload' => $data];\n    }\n}\n```\n",
                blade: "\n```blade\n@" + "extends('theme::layouts.master')\n\n@" + "section('content')\n    <div class=\"container\">\n        <h1>{" + "{ $title }" + "}</h1>\n        @" + "if(count($items) > 0)\n            @" + "foreach($items as $item)\n                <p>{" + "{ $item->name }" + "}</p>\n            @" + "endforeach\n        @" + "else\n            <p>{" + "{ __('messages.no_results_found') }" + "}</p>\n        @" + "endif\n    </div>\n@" + "endsection\n```\n",
                html: "\n```html\n<div class=\"card shadow-sm p-4 rounded-4\">\n    <h3 class=\"card-title\">Title</h3>\n    <p class=\"text-muted\">Your content goes here...</p>\n    <button type=\"button\" class=\"button primary\">Click me</button>\n</div>\n```\n",
                css: "\n```css\n.custom-card {\n    display: flex;\n    align-items: center;\n    gap: 16px;\n    padding: 20px;\n    background: #ffffff;\n    border-radius: 16px;\n    border: 1px solid rgba(97, 93, 250, 0.15);\n    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);\n}\n```\n",
                json: "\n```json\n{\n  \"status\": \"success\",\n  \"code\": 200,\n  \"message\": \"Operation completed successfully\",\n  \"data\": {\n    \"id\": 101,\n    \"name\": \"Example Topic\",\n    \"is_active\": true\n  }\n}\n```\n",
                text: "\n```text\n# Sample plain text configuration or log output\n[SERVER_STATUS]: Operational\n[PORT]: 8080\n[DATABASE]: Connected\n```\n",
                code: "\n```code\n// Generic Code Snippet\nfunction calculateTotal(price, tax) {\n    return price + (price * tax);\n}\nconsole.log(calculateTotal(100, 0.15));\n```\n"
            };

            toolbar.querySelectorAll('.kb-snippet-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const snippetKey = this.getAttribute('data-snippet');
                    const textToInsert = snippetTemplates[snippetKey] || "\n```" + snippetKey + "\n\n```\n";
                    insertSnippetAtCursor(textarea, textToInsert);
                });
            });
        };

        function insertSnippetAtCursor(textarea, text) {
            textarea.focus();
            const startPos = textarea.selectionStart;
            const endPos = textarea.selectionEnd;
            const originalVal = textarea.value;

            textarea.value = originalVal.substring(0, startPos) + text + originalVal.substring(endPos, originalVal.length);
            textarea.selectionStart = startPos + text.length;
            textarea.selectionEnd = startPos + text.length;
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
        }
    })();
</script>
@endonce
