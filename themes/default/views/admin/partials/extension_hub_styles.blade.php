<style>
    .extension-hub {
        --extension-hub-accent: #3454d1;
        --extension-hub-accent-rgb: 52, 84, 209;
        --extension-hub-accent-strong: #4338ca;
        --extension-hub-surface-shadow: 0 24px 60px rgba(15, 23, 42, 0.1);
    }

    .extension-hub--plugins {
        --extension-hub-accent: #6366f1;
        --extension-hub-accent-rgb: 99, 102, 241;
        --extension-hub-accent-strong: #3454d1;
    }

    .extension-hub--themes {
        --extension-hub-accent: #f59e0b;
        --extension-hub-accent-rgb: 245, 158, 11;
        --extension-hub-accent-strong: #d97706;
    }

    .extension-hub__hero {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 2.5rem;
        color: #fff;
        background: linear-gradient(135deg, var(--extension-hub-accent) 0%, var(--extension-hub-accent-strong) 100%);
        box-shadow: 0 28px 60px rgba(var(--extension-hub-accent-rgb), 0.24);
    }

    .extension-hub__hero::before,
    .extension-hub__hero::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        pointer-events: none;
    }

    .extension-hub__hero::before {
        width: 220px;
        height: 220px;
        top: -80px;
        right: -60px;
    }

    .extension-hub__hero::after {
        width: 140px;
        height: 140px;
        bottom: -36px;
        left: min(14%, 120px);
        background: rgba(255, 255, 255, 0.08);
    }

    .extension-hub__hero-icon {
        position: absolute;
        top: -12px;
        right: 1.5rem;
        font-size: clamp(5rem, 12vw, 8rem);
        opacity: 0.12;
        line-height: 1;
        transform: rotate(-12deg);
        pointer-events: none;
    }

    .extension-hub__hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .extension-hub__hero-title {
        margin: 0 0 0.75rem;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .extension-hub__hero-desc {
        max-width: 620px;
        margin: 0;
        color: rgba(255, 255, 255, 0.88);
        font-size: 1rem;
        line-height: 1.8;
    }

    .extension-hub__hero-panel {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: flex-start;
        gap: 1rem;
        min-width: min(100%, 320px);
        padding: 1.25rem 1.35rem;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .extension-hub__hero-panel-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.18);
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .extension-hub__hero-panel-label {
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.74);
    }

    .extension-hub__hero-panel-value {
        display: block;
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1.3;
        color: #fff;
    }

    .extension-hub__stats {
        position: relative;
        z-index: 2;
        margin-top: -2.2rem;
    }

    .extension-hub__stat {
        height: 100%;
        padding: 1.3rem 1.5rem;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.72);
        background: rgba(255, 255, 255, 0.82);
        backdrop-filter: blur(12px);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
    }

    .extension-hub__stat-label {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        margin-bottom: 0.75rem;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .extension-hub__stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--extension-hub-accent);
        background: rgba(var(--extension-hub-accent-rgb), 0.12);
        border: 1px solid rgba(var(--extension-hub-accent-rgb), 0.15);
        flex-shrink: 0;
    }

    .extension-hub__stat-value {
        font-size: clamp(1.5rem, 2vw, 2rem);
        font-weight: 900;
        line-height: 1;
        color: #0f172a;
    }

    .extension-hub__surface {
        border-radius: 24px;
        border: 1px solid rgba(226, 232, 240, 0.95);
        background: rgba(255, 255, 255, 0.84);
        backdrop-filter: blur(10px);
        box-shadow: var(--extension-hub-surface-shadow);
    }

    .extension-hub__section-title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }

    .extension-hub__section-subtitle {
        margin: 0.4rem 0 0;
        color: #64748b;
        font-size: 0.95rem;
    }

    .extension-hub__count-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.7rem 1rem;
        border-radius: 999px;
        color: var(--extension-hub-accent);
        background: rgba(var(--extension-hub-accent-rgb), 0.08);
        border: 1px solid rgba(var(--extension-hub-accent-rgb), 0.14);
        font-weight: 700;
    }

    .extension-hub__tabs {
        gap: 0.75rem;
        border-bottom: 0;
    }

    .extension-hub__tab {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.85rem 1.1rem;
        border: 1px solid rgba(var(--extension-hub-accent-rgb), 0.14);
        border-radius: 14px;
        background: rgba(var(--extension-hub-accent-rgb), 0.06);
        color: #334155;
        font-weight: 700;
    }

    .extension-hub__tab:hover,
    .extension-hub__tab:focus {
        color: var(--extension-hub-accent);
        border-color: rgba(var(--extension-hub-accent-rgb), 0.2);
    }

    .extension-hub__tab.active {
        color: #fff;
        background: linear-gradient(135deg, var(--extension-hub-accent) 0%, var(--extension-hub-accent-strong) 100%);
        border-color: transparent;
        box-shadow: 0 18px 36px rgba(var(--extension-hub-accent-rgb), 0.22);
    }

    .extension-hub__list-card,
    .extension-hub__theme-card {
        height: 100%;
        padding: 1.5rem;
        border-radius: 22px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(var(--extension-hub-accent-rgb), 0.03));
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.06);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .extension-hub__list-card:hover,
    .extension-hub__theme-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 22px 48px rgba(15, 23, 42, 0.1);
        border-color: rgba(var(--extension-hub-accent-rgb), 0.18);
    }

    .extension-hub__card-head {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .extension-hub__thumbnail {
        width: 68px;
        height: 68px;
        border-radius: 20px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(var(--extension-hub-accent-rgb), 0.1);
        border: 1px solid rgba(var(--extension-hub-accent-rgb), 0.14);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        overflow: hidden;
        font-size: 1.35rem;
        font-weight: 900;
        color: var(--extension-hub-accent);
        text-transform: uppercase;
    }

    .extension-hub__thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .extension-hub__badge-stack {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .extension-hub__slug {
        margin-top: 0.35rem;
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        word-break: break-word;
    }

    .extension-hub__card-title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }

    .extension-hub__card-description {
        margin: 0.85rem 0 1rem;
        color: #475569;
        line-height: 1.75;
        min-height: 3.5rem;
    }

    .extension-hub__token-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        margin-bottom: 1.25rem;
    }

    .extension-hub__token {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.6rem 0.8rem;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: rgba(248, 250, 252, 0.88);
        color: #334155;
        font-size: 0.84rem;
        line-height: 1.5;
    }

    .extension-hub__token i {
        color: var(--extension-hub-accent);
    }

    .extension-hub__status-badge,
    .extension-hub__update-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.42rem 0.75rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .extension-hub__status-badge--active {
        color: #166534;
        background: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.18);
    }

    .extension-hub__status-badge--inactive {
        color: #475569;
        background: rgba(148, 163, 184, 0.14);
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .extension-hub__update-badge {
        color: #854d0e;
        background: rgba(245, 158, 11, 0.14);
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .extension-hub__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.7rem;
        margin-top: auto;
    }

    .btn-extension-glass {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        min-height: 42px;
        padding: 0.7rem 1rem;
        border-radius: 14px;
        border: 1px solid transparent;
        background: rgba(var(--extension-hub-accent-rgb), 0.08);
        color: var(--extension-hub-accent);
        font-weight: 700;
        transition: transform 0.22s ease, box-shadow 0.22s ease, background-color 0.22s ease;
    }

    .btn-extension-glass:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(var(--extension-hub-accent-rgb), 0.18);
    }

    .btn-extension-glass--primary {
        color: var(--extension-hub-accent);
        background: rgba(var(--extension-hub-accent-rgb), 0.1);
        border-color: rgba(var(--extension-hub-accent-rgb), 0.12);
    }

    .btn-extension-glass--success {
        color: #166534;
        background: rgba(34, 197, 94, 0.12);
        border-color: rgba(34, 197, 94, 0.16);
    }

    .btn-extension-glass--warning {
        color: #854d0e;
        background: rgba(245, 158, 11, 0.14);
        border-color: rgba(245, 158, 11, 0.16);
    }

    .btn-extension-glass--danger {
        color: #b91c1c;
        background: rgba(239, 68, 68, 0.12);
        border-color: rgba(239, 68, 68, 0.16);
    }

    .btn-extension-glass--muted {
        color: #475569;
        background: rgba(148, 163, 184, 0.12);
        border-color: rgba(148, 163, 184, 0.16);
    }

    .btn-extension-glass[disabled] {
        opacity: 0.72;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    .extension-hub__theme-preview {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        min-height: 220px;
        margin-bottom: 1.25rem;
        background: rgba(var(--extension-hub-accent-rgb), 0.08);
        border: 1px solid rgba(var(--extension-hub-accent-rgb), 0.1);
    }

    .extension-hub__theme-preview img {
        width: 100%;
        height: 100%;
        min-height: 220px;
        object-fit: cover;
        display: block;
    }

    .extension-hub__theme-fallback {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(var(--extension-hub-accent-rgb), 0.62);
        font-size: 3rem;
    }

    .extension-hub__theme-overlay {
        position: absolute;
        inset: auto 1rem 1rem 1rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .extension-hub__market-alert {
        border-radius: 18px;
        border-color: rgba(245, 158, 11, 0.28);
        background: rgba(255, 251, 235, 0.92);
        color: #854d0e;
    }

    .extension-hub__market-card {
        min-height: 100%;
    }

    .extension-hub__market-visual {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        min-height: 180px;
        margin-bottom: 1.1rem;
        background: rgba(var(--extension-hub-accent-rgb), 0.08);
        border: 1px solid rgba(var(--extension-hub-accent-rgb), 0.12);
    }

    .extension-hub__market-visual img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }

    .extension-hub__market-fallback {
        min-height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.75rem;
        font-weight: 900;
        color: var(--extension-hub-accent);
        text-transform: uppercase;
    }

    .extension-hub__empty {
        padding: 4rem 1.5rem;
        text-align: center;
    }

    .extension-hub__empty-icon {
        width: 84px;
        height: 84px;
        margin: 0 auto 1rem;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--extension-hub-accent);
        background: rgba(var(--extension-hub-accent-rgb), 0.1);
        border: 1px solid rgba(var(--extension-hub-accent-rgb), 0.14);
        font-size: 2rem;
    }

    .extension-hub__modal-pre {
        white-space: pre-wrap;
        font-family: inherit;
        margin-bottom: 0;
        max-height: 420px;
        overflow: auto;
        color: inherit;
    }

    html.app-skin-dark .extension-hub__stat,
    html.app-skin-dark .extension-hub__surface {
        background: rgba(18, 26, 44, 0.82);
        border-color: rgba(71, 85, 105, 0.55);
        box-shadow: 0 24px 54px rgba(2, 6, 23, 0.34);
    }

    html.app-skin-dark .extension-hub__section-title,
    html.app-skin-dark .extension-hub__card-title,
    html.app-skin-dark .extension-hub__stat-value {
        color: #f8fafc;
    }

    html.app-skin-dark .extension-hub__section-subtitle,
    html.app-skin-dark .extension-hub__slug,
    html.app-skin-dark .extension-hub__card-description,
    html.app-skin-dark .extension-hub__token,
    html.app-skin-dark .extension-hub__stat-label {
        color: #cbd5e1;
    }

    html.app-skin-dark .extension-hub__list-card,
    html.app-skin-dark .extension-hub__theme-card {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.94), rgba(var(--extension-hub-accent-rgb), 0.1));
        border-color: rgba(71, 85, 105, 0.55);
        box-shadow: 0 20px 40px rgba(2, 6, 23, 0.28);
    }

    html.app-skin-dark .extension-hub__token {
        background: rgba(15, 23, 42, 0.68);
        border-color: rgba(71, 85, 105, 0.55);
    }

    html.app-skin-dark .extension-hub__thumbnail {
        box-shadow: none;
    }

    html.app-skin-dark .extension-hub__count-pill {
        background: rgba(var(--extension-hub-accent-rgb), 0.14);
        border-color: rgba(var(--extension-hub-accent-rgb), 0.22);
    }

    html.app-skin-dark .extension-hub__tab {
        background: rgba(15, 23, 42, 0.72);
        border-color: rgba(71, 85, 105, 0.55);
        color: #cbd5e1;
    }

    html.app-skin-dark .extension-hub__tab.active {
        color: #fff;
    }

    html.app-skin-dark .extension-hub__theme-preview {
        border-color: rgba(71, 85, 105, 0.45);
        background: rgba(15, 23, 42, 0.82);
    }

    html.app-skin-dark .extension-hub__market-visual {
        border-color: rgba(71, 85, 105, 0.45);
        background: rgba(15, 23, 42, 0.82);
    }

    html.app-skin-dark .extension-hub__market-alert {
        background: rgba(69, 39, 10, 0.86);
        border-color: rgba(245, 158, 11, 0.28);
        color: #fcd34d;
    }

    html[dir="rtl"] .extension-hub__hero-icon {
        right: auto;
        left: 1.5rem;
    }

    @media (max-width: 1199.98px) {
        .extension-hub__hero {
            padding: 2rem;
        }

        .extension-hub__stats {
            margin-top: -1.8rem;
        }
    }

    @media (max-width: 767.98px) {
        .extension-hub__hero,
        .extension-hub__surface,
        .extension-hub__list-card,
        .extension-hub__theme-card {
            border-radius: 20px;
        }

        .extension-hub__hero {
            padding: 1.75rem;
        }

        .extension-hub__hero-panel {
            width: 100%;
        }

        .extension-hub__stats {
            margin-top: 1rem;
        }

        .extension-hub__surface {
            padding: 1.25rem !important;
        }

        .extension-hub__card-head {
            flex-direction: column;
        }

        .extension-hub__actions {
            width: 100%;
        }

        .extension-hub__actions form,
        .extension-hub__actions .btn-extension-glass,
        .extension-hub__actions form .btn-extension-glass {
            width: 100%;
        }

        .extension-hub__actions .btn-extension-glass {
            justify-content: center;
        }

        .extension-hub__tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 0.25rem;
        }
    }
</style>
