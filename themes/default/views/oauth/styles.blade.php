@once
<style>
    .oauth-consent-card {
        overflow: hidden;
    }

    .oauth-consent-card .widget-box-content {
        margin-top: 0;
    }

    .oauth-consent-banner {
        position: relative;
        overflow: hidden;
        padding: 30px 32px 92px;
        background: var(--dev-surface-accent);
        border-bottom: 1px solid var(--dev-border);
    }

    .oauth-consent-banner__glow {
        position: absolute;
        border-radius: 999px;
        pointer-events: none;
        filter: blur(0);
        opacity: 0.9;
    }

    .oauth-consent-banner__glow--one {
        top: -54px;
        inset-inline-end: -22px;
        width: 160px;
        height: 160px;
        background: rgba(97, 93, 250, 0.16);
    }

    .oauth-consent-banner__glow--two {
        bottom: -88px;
        inset-inline-start: -28px;
        width: 210px;
        height: 210px;
        background: rgba(35, 210, 226, 0.16);
    }

    .oauth-consent-banner__inner {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 18px;
    }

    .oauth-consent-banner__icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        font-size: 1.25rem;
        box-shadow: 0 14px 28px rgba(97, 93, 250, 0.18);
    }

    .oauth-consent-kicker {
        margin: 0;
        color: rgba(255, 255, 255, 0.82);
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .oauth-consent-title {
        margin: 0;
        color: #fff;
        font-family: "Rajdhani", sans-serif;
        font-size: clamp(2rem, 4vw, 2.55rem);
        font-weight: 700;
        line-height: 1;
    }

    .oauth-consent-summary {
        max-width: 520px;
        margin: 0;
        color: rgba(255, 255, 255, 0.92);
        font-size: 0.98rem;
        font-weight: 600;
        line-height: 1.7;
    }

    .oauth-consent-content {
        padding: 0 32px 32px;
    }

    .oauth-consent-app {
        position: relative;
        margin-top: -62px;
        display: flex;
        align-items: flex-end;
        gap: 20px;
        z-index: 2;
    }

    .oauth-consent-app__logo,
    .oauth-consent-app__fallback {
        width: 92px;
        height: 92px;
        border-radius: 28px;
        border: 6px solid var(--dev-surface);
        box-shadow: 0 20px 36px rgba(94, 92, 154, 0.16);
        background: var(--dev-surface);
        flex-shrink: 0;
    }

    .oauth-consent-app__logo {
        object-fit: cover;
    }

    .oauth-consent-app__fallback {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
        color: #fff;
        font-family: "Rajdhani", sans-serif;
        font-size: 2.35rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .oauth-consent-app__meta {
        min-width: 0;
        display: grid;
        gap: 10px;
        padding-bottom: 4px;
    }

    .oauth-consent-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .oauth-consent-chip,
    .oauth-consent-count,
    .oauth-consent-mini-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid var(--dev-border);
        background: var(--dev-chip-bg);
        color: var(--dev-title);
        font-size: 0.78rem;
        font-weight: 800;
        line-height: 1;
    }

    .oauth-consent-chip--accent {
        background: var(--dev-surface-accent);
        color: var(--dev-accent);
    }

    .oauth-consent-count {
        min-width: 38px;
        justify-content: center;
    }

    .oauth-consent-mini-chip {
        padding: 6px 10px;
        font-size: 0.72rem;
    }

    .oauth-consent-mini-chip--warning {
        background: rgba(249, 180, 77, 0.12);
        border-color: rgba(249, 180, 77, 0.22);
        color: var(--dev-warning);
    }

    .oauth-consent-app__name {
        margin: 0;
        color: var(--dev-title);
        font-family: "Rajdhani", sans-serif;
        font-size: clamp(2rem, 4vw, 2.7rem);
        font-weight: 700;
        line-height: 0.95;
        word-break: break-word;
    }

    .oauth-consent-app__domain {
        margin: 0;
        color: var(--dev-muted);
        font-size: 0.92rem;
        font-weight: 700;
        line-height: 1.7;
        word-break: break-word;
    }

    .oauth-consent-section {
        margin-top: 30px;
        display: grid;
        gap: 16px;
    }

    .oauth-consent-section__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .oauth-consent-scopes {
        display: grid;
        gap: 14px;
    }

    .oauth-consent-scope {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 18px;
        border-radius: 22px;
        border: 1px solid var(--dev-border);
        background: var(--dev-surface-soft);
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .oauth-consent-scope:hover {
        transform: translateY(-1px);
        border-color: rgba(97, 93, 250, 0.24);
        box-shadow: 0 18px 30px rgba(94, 92, 154, 0.08);
    }

    .oauth-consent-scope.is-sensitive {
        border-color: rgba(249, 180, 77, 0.28);
        background: linear-gradient(180deg, rgba(249, 180, 77, 0.08), var(--dev-surface-soft));
    }

    .oauth-consent-scope__icon {
        width: 48px;
        height: 48px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--dev-surface);
        color: var(--dev-accent);
        font-size: 1.05rem;
        flex-shrink: 0;
    }

    .oauth-consent-scope.is-sensitive .oauth-consent-scope__icon {
        color: var(--dev-warning);
    }

    .oauth-consent-scope__copy {
        min-width: 0;
        flex: 1;
        display: grid;
        gap: 8px;
    }

    .oauth-consent-scope__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .oauth-consent-scope__head h3 {
        margin: 0;
        color: var(--dev-title);
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.35;
    }

    .oauth-consent-scope__meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .oauth-consent-scope code {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: var(--dev-surface);
        border: 1px solid var(--dev-border);
        color: var(--dev-accent);
        font-size: 0.74rem;
        font-weight: 700;
        line-height: 1;
    }

    .oauth-consent-scope__description {
        margin: 0;
        color: var(--dev-text);
        font-size: 0.92rem;
        line-height: 1.7;
    }

    .oauth-consent-empty {
        padding: 24px 22px;
        border-radius: 22px;
        border: 1px dashed var(--dev-border);
        background: var(--dev-surface-soft);
        text-align: center;
        color: var(--dev-muted);
        font-weight: 700;
    }

    .oauth-consent-note {
        margin-top: 24px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 18px 20px;
        border-radius: 20px;
        border: 1px solid rgba(97, 93, 250, 0.16);
        background: rgba(97, 93, 250, 0.08);
    }

    .oauth-consent-note__icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(97, 93, 250, 0.14);
        color: var(--dev-accent);
        font-size: 1rem;
        flex-shrink: 0;
    }

    .oauth-consent-note__copy {
        min-width: 0;
        display: grid;
        gap: 8px;
    }

    .oauth-consent-note__title {
        margin: 0;
        color: var(--dev-title);
        font-size: 0.92rem;
        font-weight: 800;
        line-height: 1.3;
    }

    .oauth-consent-note__text {
        margin: 0;
        color: var(--dev-text);
        font-size: 0.88rem;
        line-height: 1.7;
    }

    .oauth-consent-note__links {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .oauth-consent-note__link {
        color: var(--dev-accent);
        font-size: 0.82rem;
        font-weight: 800;
        text-decoration: none;
    }

    .oauth-consent-note__link:hover {
        text-decoration: none;
    }

    .oauth-consent-form {
        margin-top: 28px;
        display: grid;
        gap: 14px;
    }

    .oauth-consent-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .oauth-consent-actions .button {
        justify-content: center;
    }

    .oauth-consent-actions .button i {
        font-size: 0.95rem;
    }

    .oauth-consent-disclaimer {
        margin: 0;
        color: var(--dev-muted);
        font-size: 0.8rem;
        font-weight: 600;
        line-height: 1.7;
        text-align: center;
    }

    @media screen and (max-width: 680px) {
        .oauth-consent-banner {
            padding: 24px 22px 84px;
        }

        .oauth-consent-content {
            padding: 0 22px 24px;
        }

        .oauth-consent-app {
            margin-top: -52px;
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .oauth-consent-app__logo,
        .oauth-consent-app__fallback {
            width: 82px;
            height: 82px;
            border-radius: 24px;
        }

        .oauth-consent-app__fallback {
            font-size: 2rem;
        }

        .oauth-consent-actions {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>
@endonce
