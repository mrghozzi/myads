<style>
    body[data-theme="css"] {
        --store-shell-bg: linear-gradient(135deg, rgba(97, 93, 250, 0.1), rgba(35, 210, 226, 0.14));
        --store-shell-surface: #ffffff;
        --store-shell-border: #e8eaf6;
        --store-shell-shadow: 0 24px 58px rgba(94, 92, 154, 0.14);
        --store-shell-title: #3e3f5e;
        --store-shell-text: #5d607a;
        --store-shell-muted: #8f94b5;
        --store-shell-soft: #f7f8fd;
        --store-shell-soft-strong: #eef1ff;
        --store-shell-accent: #615dfa;
        --store-shell-accent-alt: #23d2e2;
        --store-shell-accent-soft: rgba(97, 93, 250, 0.14);
        --store-shell-danger-soft: rgba(233, 75, 95, 0.14);
        --store-shell-chip-bg: rgba(255, 255, 255, 0.9);
        --store-shell-chip-border: rgba(97, 93, 250, 0.14);
    }

    body[data-theme="css_d"] {
        --store-shell-bg: linear-gradient(135deg, rgba(97, 93, 250, 0.22), rgba(35, 210, 226, 0.16));
        --store-shell-surface: #1f2637;
        --store-shell-border: #2f3850;
        --store-shell-shadow: 0 24px 58px rgba(0, 0, 0, 0.28);
        --store-shell-title: #ffffff;
        --store-shell-text: #c8d1e8;
        --store-shell-muted: #95a0bf;
        --store-shell-soft: #242d40;
        --store-shell-soft-strong: #293249;
        --store-shell-accent: #8c8aff;
        --store-shell-accent-alt: #4ff461;
        --store-shell-accent-soft: rgba(140, 138, 255, 0.18);
        --store-shell-danger-soft: rgba(255, 91, 115, 0.18);
        --store-shell-chip-bg: rgba(31, 38, 55, 0.86);
        --store-shell-chip-border: rgba(255, 255, 255, 0.08);
    }

    .store-detail-page,
    .knowledgebase-page {
        display: grid;
        gap: 24px;
    }

    .store-shell-card,
    .store-content-card,
    .kb-shell-card,
    .kb-topic-card,
    .kb-main-card,
    .kb-side-card,
    .kb-form-card,
    .kb-helper-card {
        overflow: visible;
        border: 1px solid var(--store-shell-border);
        box-shadow: var(--store-shell-shadow);
    }

    .store-hero,
    .kb-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.8fr) 320px;
        gap: 24px;
        padding: 28px;
        border-radius: 24px;
        background: var(--store-shell-bg);
    }

    .store-hero__main,
    .kb-hero__main {
        display: flex;
        gap: 24px;
        min-width: 0;
    }

    .store-hero__media,
    .kb-hero__media {
        flex-shrink: 0;
        width: 220px;
        min-width: 220px;
        border-radius: 24px;
        overflow: hidden;
        background: var(--store-shell-soft-strong);
        border: 1px solid rgba(255, 255, 255, 0.14);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.06);
    }

    .store-hero__media img,
    .kb-hero__media img {
        display: block;
        width: 100%;
        height: 100%;
        min-height: 220px;
        object-fit: cover;
    }

    .store-hero__content,
    .kb-hero__content {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .store-badge-row,
    .kb-badge-row,
    .store-stat-grid,
    .kb-stat-grid,
    .kb-topic-card__meta,
    .store-inline-actions,
    .kb-inline-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .store-pill,
    .kb-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: var(--store-shell-chip-bg);
        border: 1px solid var(--store-shell-chip-border);
        color: var(--store-shell-title);
        font-size: 0.75rem;
        font-weight: 700;
        line-height: 1;
    }

    .store-pill strong,
    .kb-pill strong {
        color: var(--store-shell-accent);
        font-size: 0.9rem;
    }

    .store-title,
    .kb-title {
        margin-top: 18px;
        color: var(--store-shell-title);
        font-size: 2rem;
        font-weight: 900;
        line-height: 1.08;
    }

    .store-subtitle,
    .kb-subtitle,
    .store-summary,
    .kb-summary,
    .store-card-muted,
    .kb-card-muted,
    .kb-topic-card__summary {
        color: var(--store-shell-text);
    }

    .store-subtitle,
    .kb-subtitle {
        margin-top: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.7;
    }

    .store-summary,
    .kb-summary {
        margin-top: 16px;
        font-size: 0.95rem;
        line-height: 1.75;
    }

    .store-stat-grid,
    .kb-stat-grid {
        margin-top: 20px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .store-stat-card,
    .kb-stat-card {
        padding: 16px 18px;
        border-radius: 20px;
        background: var(--store-shell-chip-bg);
        border: 1px solid var(--store-shell-chip-border);
    }

    .store-stat-card span,
    .kb-stat-card span {
        display: block;
        color: var(--store-shell-muted);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .store-stat-card strong,
    .kb-stat-card strong {
        display: block;
        margin-top: 10px;
        color: var(--store-shell-title);
        font-size: 1.25rem;
        font-weight: 900;
    }

    .store-inline-actions,
    .kb-inline-actions {
        margin-top: 22px;
    }

    .store-aside,
    .kb-aside {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .store-aside-card,
    .kb-aside-card {
        padding: 22px;
        border-radius: 24px;
        background: var(--store-shell-surface);
        border: 1px solid var(--store-shell-border);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
    }

    .store-aside-card__header,
    .kb-aside-card__header,
    .kb-topic-card__header,
    .kb-main-card__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .store-aside-card__label,
    .kb-aside-card__label,
    .kb-topic-card__label {
        color: var(--store-shell-muted);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .store-aside-card__title,
    .kb-aside-card__title,
    .kb-topic-card__title {
        margin-top: 8px;
        color: var(--store-shell-title);
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.35;
    }

    .store-aside-card__meta,
    .kb-aside-card__meta {
        margin-top: 14px;
        display: grid;
        gap: 10px;
    }

    .store-meta-row,
    .kb-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: var(--store-shell-text);
        font-size: 0.88rem;
        font-weight: 600;
    }

    .store-meta-row span:first-child,
    .kb-meta-row span:first-child {
        color: var(--store-shell-muted);
    }

    .store-action-menu,
    .kb-action-menu {
        position: relative;
        flex-shrink: 0;
    }

    .store-action-menu__trigger,
    .kb-action-menu__trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 42px;
        height: 42px;
        padding: 0 14px;
        border: 1px solid var(--store-shell-chip-border);
        border-radius: 999px;
        background: var(--store-shell-chip-bg);
        color: var(--store-shell-title);
        font-size: 0.8rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .store-action-menu__trigger:hover,
    .kb-action-menu__trigger:hover {
        transform: translateY(-1px);
        border-color: var(--store-shell-accent-soft);
    }

    .store-action-menu__panel,
    .kb-action-menu__panel {
        position: absolute;
        top: calc(100% + 10px);
        inset-inline-end: 0;
        min-width: 220px;
        opacity: 0;
        visibility: hidden;
        transform: translate(0, 20px);
        z-index: 9999;
    }

    .store-dropdown-button {
        width: 100%;
        border: 0;
        background: transparent;
        padding: 0;
        text-align: start;
        cursor: pointer;
    }

    .store-inline-report {
        margin-top: 16px;
    }

    .store-content-card,
    .kb-main-card,
    .kb-side-card,
    .kb-form-card,
    .kb-helper-card {
        background: var(--store-shell-surface);
    }

    .store-content-card .widget-box-content,
    .kb-main-card .widget-box-content,
    .kb-side-card .widget-box-content,
    .kb-form-card .widget-box-content,
    .kb-helper-card .widget-box-content {
        padding-top: 0;
    }

    .store-tabs .tab-box-options {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        background: transparent;
    }

    .store-tabs .tab-box-option {
        border: 1px solid var(--store-shell-border);
        border-radius: 18px;
        background: var(--store-shell-soft);
        padding: 14px 18px;
        cursor: pointer;
        transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
    }

    .store-tabs .tab-box-option.active {
        border-color: var(--store-shell-accent-soft);
        background: var(--store-shell-accent-soft);
        transform: translateY(-2px);
    }

    .store-tabs .tab-box-option-title {
        color: var(--store-shell-title);
        font-size: 0.9rem;
        font-weight: 800;
    }

    .store-tabs .tab-box-item-content,
    .kb-main-card .widget-box-content {
        color: var(--store-shell-text);
    }

    .store-rich-text,
    .kb-article-body {
        color: var(--store-shell-text);
        font-size: 0.96rem;
        line-height: 1.82;
    }

    .store-rich-text img,
    .kb-article-body img {
        max-width: 100%;
        height: auto;
        margin-top: 20px;
        border-radius: 18px;
    }

    .store-version-table td,
    .store-version-table th {
        vertical-align: middle;
    }

    .kb-shell-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 4px;
    }

    .kb-shell-nav__link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 999px;
        background: var(--store-shell-soft);
        border: 1px solid var(--store-shell-border);
        color: var(--store-shell-title);
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
    }

    .kb-shell-nav__link:hover,
    .kb-shell-nav__link.active {
        text-decoration: none;
        transform: translateY(-1px);
        background: var(--store-shell-accent-soft);
        border-color: var(--store-shell-accent-soft);
    }

    .kb-topic-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 18px;
    }

    .kb-topic-card {
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 24px;
        background: var(--store-shell-surface);
    }

    .kb-topic-card__title {
        margin: 10px 0 0;
    }

    .kb-topic-card__title a {
        color: inherit;
    }

    .kb-topic-card__summary {
        margin-top: 16px;
        font-size: 0.92rem;
        line-height: 1.72;
    }

    .kb-topic-card__footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: auto;
        padding-top: 18px;
    }

    .kb-topic-layout,
    .kb-review-layout,
    .kb-editor-layout {
        display: grid;
        gap: 24px;
    }

    .kb-topic-layout {
        grid-template-columns: minmax(0, 1.65fr) 320px;
    }

    .kb-review-layout {
        grid-template-columns: 340px minmax(0, 1fr);
    }

    .kb-editor-layout {
        grid-template-columns: minmax(0, 1.6fr) 320px;
    }

    .kb-main-card .widget-box-title,
    .kb-side-card .widget-box-title,
    .kb-form-card .widget-box-title,
    .kb-helper-card .widget-box-title {
        color: var(--store-shell-title);
    }

    .kb-main-card__header {
        margin-bottom: 18px;
    }

    .kb-main-card__subtitle {
        margin-top: 10px;
        color: var(--store-shell-muted);
        font-size: 0.84rem;
        font-weight: 700;
    }

    .kb-empty-state {
        padding: 28px;
        border-radius: 24px;
        background: var(--store-shell-soft);
        border: 1px dashed var(--store-shell-border);
        text-align: center;
        color: var(--store-shell-muted);
        font-weight: 700;
    }

    .kb-side-card__actions {
        display: grid;
        gap: 10px;
        margin-top: 18px;
    }

    .kb-form-card .form-input input[readonly] {
        opacity: 0.8;
    }

    .kb-form-card .sceditor-container {
        width: 100% !important;
    }

    .kb-review-card__table .table {
        margin-bottom: 0;
    }

    .store-empty-table {
        color: var(--store-shell-muted);
        text-align: center;
        font-weight: 700;
    }

    @media screen and (max-width: 1100px) {
        .store-hero,
        .kb-hero,
        .kb-topic-layout,
        .kb-review-layout,
        .kb-editor-layout {
            grid-template-columns: 1fr;
        }

        .store-aside,
        .kb-aside {
            order: -1;
        }
    }

    @media screen and (max-width: 760px) {
        .store-hero,
        .kb-hero,
        .kb-topic-card {
            padding: 20px;
        }

        .store-hero__main,
        .kb-hero__main {
            flex-direction: column;
        }

        .store-hero__media,
        .kb-hero__media {
            width: 100%;
            min-width: 0;
        }

        .store-stat-grid,
        .kb-stat-grid,
        .store-tabs .tab-box-options {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media screen and (max-width: 540px) {
        .store-stat-grid,
        .kb-stat-grid,
        .store-tabs .tab-box-options {
            grid-template-columns: 1fr;
        }

        .store-inline-actions,
        .kb-inline-actions,
        .kb-topic-card__footer {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
