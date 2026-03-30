<style>
    .extension-hub--reports {
        --extension-hub-accent: #ea4d4d;
        --extension-hub-accent-rgb: 234, 77, 77;
        --extension-hub-accent-strong: #f59e0b;
    }

    .reports-hub__hero-panel {
        min-width: min(100%, 340px);
    }

    .reports-hub__list {
        display: grid;
        gap: 1rem;
    }

    .reports-hub__card {
        position: relative;
    }

    .reports-hub__card--pending {
        border-color: rgba(245, 158, 11, 0.28);
        box-shadow: 0 18px 44px rgba(245, 158, 11, 0.12);
    }

    .reports-hub__reference {
        display: inline-flex;
        align-items: center;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.05);
        color: #0f172a;
        font-weight: 800;
        letter-spacing: 0.04em;
    }

    .reports-hub__type-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        color: var(--extension-hub-accent);
        background: rgba(var(--extension-hub-accent-rgb), 0.08);
        border: 1px solid rgba(var(--extension-hub-accent-rgb), 0.12);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .reports-hub__meta-card,
    .reports-hub__reason-card {
        height: 100%;
        padding: 1rem 1.1rem;
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        background: rgba(248, 250, 252, 0.82);
    }

    .reports-hub__label {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        margin-bottom: 0.75rem;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .reports-hub__person {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .reports-hub__person-avatar {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(var(--extension-hub-accent-rgb), 0.08);
        border: 1px solid rgba(var(--extension-hub-accent-rgb), 0.12);
        color: var(--extension-hub-accent);
        font-size: 1rem;
        font-weight: 900;
        text-transform: uppercase;
        flex-shrink: 0;
    }

    .reports-hub__person-name {
        margin: 0;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
        word-break: break-word;
    }

    .reports-hub__reason {
        margin: 0;
        color: #334155;
        line-height: 1.85;
        word-break: break-word;
    }

    .reports-hub__removed {
        padding: 0.9rem 1rem;
        border-radius: 16px;
        color: #b91c1c;
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.14);
        font-weight: 700;
    }

    .reports-hub__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.7rem;
    }

    .reports-hub__action-col {
        width: min(100%, 220px);
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
    }

    .reports-hub__action-col .btn-extension-glass,
    .reports-hub__action-col form,
    .reports-hub__action-col form .btn-extension-glass {
        width: 100%;
    }

    .reports-hub__empty-copy {
        max-width: 520px;
        margin: 0.5rem auto 0;
    }

    html.app-skin-dark .reports-hub__reference {
        background: rgba(148, 163, 184, 0.16);
        color: #f8fafc;
    }

    html.app-skin-dark .reports-hub__meta-card,
    html.app-skin-dark .reports-hub__reason-card {
        background: rgba(15, 23, 42, 0.72);
        border-color: rgba(71, 85, 105, 0.55);
    }

    html.app-skin-dark .reports-hub__person-name,
    html.app-skin-dark .reports-hub__reason {
        color: #e2e8f0;
    }

    html.app-skin-dark .reports-hub__label {
        color: #cbd5e1;
    }

    html.app-skin-dark .reports-hub__removed {
        background: rgba(127, 29, 29, 0.24);
        border-color: rgba(248, 113, 113, 0.24);
        color: #fecaca;
    }

    @media (max-width: 991.98px) {
        .reports-hub__action-col {
            width: 100%;
        }
    }
</style>
