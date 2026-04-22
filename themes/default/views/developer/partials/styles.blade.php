@once
<style>
    body[data-theme="css"] {
        --dev-surface: #ffffff;
        --dev-surface-soft: #f7f8ff;
        --dev-surface-accent: linear-gradient(135deg, rgba(97, 93, 250, 0.14), rgba(35, 210, 226, 0.12));
        --dev-border: rgba(97, 93, 250, 0.14);
        --dev-shadow: 0 20px 44px rgba(94, 92, 154, 0.1);
        --dev-title: #22263f;
        --dev-text: #5f6480;
        --dev-muted: #8e93b4;
        --dev-accent: #615dfa;
        --dev-accent-alt: #23d2e2;
        --dev-success: #1f9d62;
        --dev-warning: #cf8b16;
        --dev-danger: #d94b63;
        --dev-code-bg: #1d2333;
        --dev-code-border: rgba(255, 255, 255, 0.08);
        --dev-code-text: #eef2ff;
        --dev-code-muted: #94a3c6;
        --dev-chip-bg: #eef1ff;
    }

    body[data-theme="css_d"] {
        --dev-surface: #1f2637;
        --dev-surface-soft: #242d40;
        --dev-surface-accent: linear-gradient(135deg, rgba(97, 93, 250, 0.22), rgba(35, 210, 226, 0.16));
        --dev-border: #313a53;
        --dev-shadow: 0 22px 48px rgba(0, 0, 0, 0.28);
        --dev-title: #ffffff;
        --dev-text: #c4cee7;
        --dev-muted: #95a0bf;
        --dev-accent: #8c8aff;
        --dev-accent-alt: #4ff461;
        --dev-success: #59d59d;
        --dev-warning: #ffbe5c;
        --dev-danger: #ff7d93;
        --dev-code-bg: #161b28;
        --dev-code-border: rgba(255, 255, 255, 0.08);
        --dev-code-text: #eef2ff;
        --dev-code-muted: #9cadcf;
        --dev-chip-bg: #2a3248;
    }

    .dev-shell,
    .dev-side-stack,
    .dev-form-layout,
    .dev-doc-grid,
    .dev-app-list,
    .dev-rule-list {
        display: grid;
        gap: 18px;
    }

    .dev-panel {
        overflow: hidden;
        border: 1px solid var(--dev-border);
        box-shadow: var(--dev-shadow);
        background: var(--dev-surface);
    }

    .dev-surface-header,
    .dev-card-head,
    .dev-inline-actions,
    .dev-app-card-head,
    .dev-summary-head,
    .dev-meta-row,
    .dev-credential-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: wrap;
    }

    .dev-kicker {
        color: var(--dev-muted);
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .dev-title,
    .dev-card-title,
    .dev-section-title {
        color: var(--dev-title);
        font-size: 1.12rem;
        font-weight: 800;
        margin: 0;
    }

    .dev-section-title {
        font-size: 1.24rem;
    }

    .dev-card-copy,
    .dev-summary-copy,
    .dev-note p,
    .dev-scope-copy,
    .dev-rule-value,
    .dev-app-description {
        color: var(--dev-text);
        font-size: 0.94rem;
        line-height: 1.7;
    }

    .dev-chip-row,
    .dev-stat-grid,
    .dev-app-meta,
    .dev-scope-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .dev-chip,
    .dev-stat-card,
    .dev-mini-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        background: var(--dev-chip-bg);
        color: var(--dev-title);
        border: 1px solid var(--dev-border);
        font-size: 0.82rem;
        font-weight: 700;
    }

    .dev-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        width: 100%;
    }

    .dev-stat-grid--compact {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .dev-stat-card {
        align-items: flex-start;
        flex-direction: column;
        border-radius: 18px;
        background: var(--dev-surface-soft);
    }

    .dev-stat-card span {
        color: var(--dev-muted);
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .dev-stat-card strong {
        color: var(--dev-title);
        font-size: 1.18rem;
        font-weight: 900;
    }

    .dev-nav-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        width: 100%;
        padding: 14px 18px;
        border-top: 1px solid var(--dev-border);
        color: var(--dev-text);
        font-weight: 700;
        text-decoration: none;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .dev-nav-link:first-child {
        border-top: 0;
    }

    .dev-nav-link:hover {
        background: rgba(97, 93, 250, 0.06);
        color: var(--dev-accent);
        text-decoration: none;
    }

    .dev-nav-link.is-active {
        background: var(--dev-surface-accent);
        color: var(--dev-accent);
    }

    .dev-nav-link i:last-child {
        color: var(--dev-muted);
        font-size: 0.8rem;
    }

    .dev-doc-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .dev-doc-card .widget-box-content,
    .dev-app-card .widget-box-content {
        padding: 28px;
    }

    .dev-doc-icon {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--dev-surface-accent);
        color: var(--dev-accent);
        font-size: 1.15rem;
        margin-bottom: 18px;
    }

    .dev-code-block {
        margin-top: 18px;
        border-radius: 20px;
        background: var(--dev-code-bg);
        border: 1px solid var(--dev-code-border);
        overflow: hidden;
    }

    .dev-code-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        color: var(--dev-code-muted);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .dev-copy-btn,
    .dev-inline-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 38px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.04);
        color: #fff;
        cursor: pointer;
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .dev-inline-icon-btn {
        min-height: 44px;
        padding: 0 16px;
        background: var(--dev-surface-soft);
        border-color: var(--dev-border);
        color: var(--dev-title);
    }

    .dev-copy-btn:hover,
    .dev-inline-icon-btn:hover {
        transform: translateY(-1px);
        background: rgba(255, 255, 255, 0.1);
    }

    .dev-inline-icon-btn:hover {
        background: rgba(97, 93, 250, 0.08);
    }

    .dev-copy-btn[data-copied="true"] {
        background: rgba(79, 244, 97, 0.18);
        border-color: rgba(79, 244, 97, 0.28);
    }

    .dev-code-block pre {
        margin: 0;
        padding: 18px 20px;
        color: var(--dev-code-text);
        background: transparent;
        font-size: 0.84rem;
        line-height: 1.75;
        font-family: Consolas, "Courier New", monospace;
        white-space: pre-wrap;
        word-break: break-word;
        overflow-x: auto;
    }

    .dev-code-block pre code,
    .dev-code-block code {
        display: block;
        padding: 0;
        margin: 0;
        border: 0;
        background: transparent !important;
        color: var(--dev-code-text) !important;
        font-size: inherit;
        line-height: inherit;
        font-family: inherit;
        white-space: inherit;
        word-break: inherit;
        opacity: 1;
        text-shadow: none;
    }

    .dev-list-reset {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 12px;
    }

    .dev-list-reset li {
        display: flex;
        gap: 10px;
        color: var(--dev-text);
        line-height: 1.6;
    }

    .dev-list-reset li i {
        color: var(--dev-accent);
        margin-top: 3px;
    }

    .dev-state-list {
        display: grid;
        gap: 12px;
    }

    .dev-state-list a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 18px;
        background: var(--dev-surface-soft);
        border: 1px solid var(--dev-border);
        color: var(--dev-title);
        text-decoration: none;
    }

    .dev-state-list a:hover {
        text-decoration: none;
        border-color: rgba(97, 93, 250, 0.28);
        transform: translateY(-1px);
    }

    .dev-note {
        display: grid;
        gap: 10px;
        margin-bottom: 18px;
        padding: 18px 20px;
        border-radius: 18px;
        border: 1px solid transparent;
    }

    .dev-note--info {
        background: rgba(97, 93, 250, 0.08);
        border-color: rgba(97, 93, 250, 0.16);
    }

    .dev-note--warning {
        background: rgba(249, 180, 77, 0.1);
        border-color: rgba(249, 180, 77, 0.22);
    }

    .dev-note--danger {
        background: rgba(233, 75, 95, 0.1);
        border-color: rgba(233, 75, 95, 0.2);
    }

    .dev-note strong {
        color: var(--dev-title);
        font-size: 0.95rem;
    }

    .dev-form-layout {
        gap: 24px;
    }

    .dev-form-section {
        display: grid;
        gap: 16px;
        padding: 22px;
        border-radius: 22px;
        background: var(--dev-surface-soft);
        border: 1px solid var(--dev-border);
    }

    .dev-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .dev-form-grid .dev-form-grid__full {
        grid-column: 1 / -1;
    }

    .dev-field {
        display: grid;
        gap: 8px;
    }

    .dev-field label {
        color: var(--dev-title);
        font-size: 0.86rem;
        font-weight: 800;
    }

    .dev-control {
        width: 100%;
        min-height: 50px;
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px solid var(--dev-border);
        background: var(--dev-surface);
        color: var(--dev-title);
        box-shadow: none;
    }

    .dev-control::placeholder {
        color: var(--dev-muted);
    }

    .dev-control:focus {
        border-color: rgba(97, 93, 250, 0.34);
        box-shadow: 0 0 0 0.2rem rgba(97, 93, 250, 0.08);
    }

    .dev-control--textarea {
        min-height: 140px;
        resize: vertical;
    }

    .dev-error {
        color: var(--dev-danger);
        font-size: 0.8rem;
        font-weight: 700;
    }

    .dev-help-text {
        color: var(--dev-muted);
        font-size: 0.82rem;
        line-height: 1.6;
    }

    .dev-scope-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .dev-scope-card {
        position: relative;
        display: flex;
        gap: 14px;
        padding: 18px;
        border-radius: 20px;
        background: var(--dev-surface);
        border: 1px solid var(--dev-border);
        min-height: 100%;
    }

    .dev-scope-card .form-check-input {
        margin-top: 4px;
        flex-shrink: 0;
    }

    .dev-scope-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--dev-title);
        font-size: 0.92rem;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .dev-scope-card code {
        display: inline-block;
        margin-top: 8px;
        padding: 4px 8px;
        border-radius: 999px;
        background: var(--dev-chip-bg);
        color: var(--dev-accent);
        font-size: 0.76rem;
    }

    .dev-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 800;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .dev-status-pill.is-active {
        background: rgba(31, 157, 98, 0.12);
        border-color: rgba(31, 157, 98, 0.2);
        color: var(--dev-success);
    }

    .dev-status-pill.is-draft {
        background: rgba(142, 147, 180, 0.14);
        border-color: rgba(142, 147, 180, 0.18);
        color: var(--dev-muted);
    }

    .dev-status-pill.is-pending_review {
        background: rgba(249, 180, 77, 0.14);
        border-color: rgba(249, 180, 77, 0.22);
        color: var(--dev-warning);
    }

    .dev-status-pill.is-rejected,
    .dev-status-pill.is-suspended {
        background: rgba(233, 75, 95, 0.12);
        border-color: rgba(233, 75, 95, 0.2);
        color: var(--dev-danger);
    }

    .dev-app-list {
        grid-template-columns: minmax(0, 1fr);
    }

    .dev-app-name {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: var(--dev-title);
        font-size: 1.02rem;
        font-weight: 800;
        text-decoration: none;
    }

    .dev-app-name:hover {
        color: var(--dev-accent);
        text-decoration: none;
    }

    .dev-app-domain {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--dev-muted);
        font-size: 0.84rem;
        font-weight: 700;
    }

    .dev-credential-field {
        display: grid;
        gap: 8px;
    }

    .dev-credential-field label {
        color: var(--dev-muted);
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .dev-credential-input {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .dev-credential-input input {
        font-family: Consolas, "Courier New", monospace;
    }

    .dev-rule-list {
        margin-top: 16px;
    }

    .dev-rule-item {
        display: grid;
        gap: 6px;
        padding: 14px 0;
        border-top: 1px solid var(--dev-border);
    }

    .dev-rule-item:first-child {
        border-top: 0;
        padding-top: 0;
    }

    .dev-rule-item strong {
        color: var(--dev-title);
        font-size: 0.9rem;
    }

    .dev-empty {
        padding: 34px 26px;
        border-radius: 22px;
        text-align: center;
        background: var(--dev-surface-soft);
        border: 1px dashed rgba(97, 93, 250, 0.24);
    }

    .dev-empty i {
        font-size: 2rem;
        color: var(--dev-accent);
        margin-bottom: 12px;
    }

    .dev-form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .dev-divider {
        height: 1px;
        background: var(--dev-border);
        margin: 4px 0;
    }

    @media screen and (max-width: 1024px) {
        .dev-scope-grid,
        .dev-form-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media screen and (max-width: 680px) {
        .dev-doc-card .widget-box-content,
        .dev-app-card .widget-box-content {
            padding: 22px;
        }

        .dev-form-section {
            padding: 18px;
        }

        .dev-stat-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .dev-copy-btn,
        .dev-inline-icon-btn {
            width: 100%;
        }

        .dev-credential-input {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
@endonce
