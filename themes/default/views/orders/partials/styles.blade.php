@once
<style>
    .orders-shell {
        display: grid;
        gap: 18px;
    }

    .orders-hero {
        position: relative;
        overflow: hidden;
    }

    .orders-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top right, rgba(35, 210, 226, 0.28), transparent 34%),
            linear-gradient(135deg, rgba(97, 93, 250, 0.12), rgba(35, 210, 226, 0.08));
        pointer-events: none;
    }

    .orders-toolbar,
    .orders-empty,
    .orders-panel,
    .orders-form-preview,
    .orders-offer-form,
    .orders-admin-card {
        border-radius: 20px;
        border: 1px solid rgba(97, 93, 250, 0.12);
        background: #fff;
        box-shadow: 0 20px 40px rgba(94, 92, 154, 0.08);
    }

    .orders-toolbar,
    .orders-panel,
    .orders-form-preview,
    .orders-offer-form,
    .orders-admin-card {
        padding: 22px;
    }

    .orders-toolbar {
        display: grid;
        gap: 16px;
    }

    .orders-toolbar-head,
    .orders-card-head,
    .orders-detail-head,
    .orders-offer-head,
    .orders-admin-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
    }

    .orders-toolbar-title,
    .orders-card-title,
    .orders-detail-title,
    .orders-offer-title {
        color: #1f2440;
        font-size: 1.1rem;
        font-weight: 800;
        margin: 0;
    }

    .orders-toolbar-copy,
    .orders-card-copy,
    .orders-muted,
    .orders-admin-copy {
        color: #7c809b;
        font-size: 0.92rem;
        line-height: 1.6;
    }

    .orders-filters {
        display: grid;
        grid-template-columns: minmax(0, 2fr) repeat(3, minmax(0, 1fr)) auto;
        gap: 12px;
    }

    .orders-filter-field {
        display: grid;
        gap: 8px;
    }

    .orders-filter-label,
    .orders-kicker,
    .orders-summary-label {
        color: #8b90aa;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .orders-filter-input,
    .orders-filter-select,
    .orders-textarea {
        width: 100%;
        min-height: 48px;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid rgba(97, 93, 250, 0.14);
        background: #f8f9ff;
        color: #2a2f47;
        font-weight: 600;
    }

    .orders-textarea {
        min-height: 150px;
        resize: vertical;
    }

    .orders-grid {
        display: grid;
        gap: 18px;
    }

    .orders-card,
    .orders-offer-card {
        border-radius: 22px;
        border: 1px solid rgba(97, 93, 250, 0.1);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(247, 248, 255, 0.98));
        box-shadow: 0 18px 36px rgba(94, 92, 154, 0.08);
        padding: 22px;
    }

    .orders-card-meta,
    .orders-offer-meta,
    .orders-detail-meta,
    .orders-summary-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .orders-meta-pill,
    .orders-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 800;
    }

    .orders-meta-pill {
        background: #eef2ff;
        color: #3b4270;
    }

    .orders-status-pill {
        background: #eef7ff;
        color: #2b72bd;
    }

    .orders-status-pill.status-open { background: #edf8f2; color: #178f52; }
    .orders-status-pill.status-under_review { background: #fff6e8; color: #d18a18; }
    .orders-status-pill.status-awarded { background: #eef2ff; color: #4b5ad5; }
    .orders-status-pill.status-in_progress { background: #e8f7ff; color: #147fb4; }
    .orders-status-pill.status-delivered { background: #edf5ff; color: #3660cc; }
    .orders-status-pill.status-completed { background: #edf8f2; color: #16814b; }
    .orders-status-pill.status-cancelled,
    .orders-status-pill.status-closed { background: #fff0f1; color: #d54a5a; }

    .orders-card-description,
    .orders-offer-message,
    .orders-detail-description {
        color: #4e556e;
        line-height: 1.8;
    }

    .orders-card-footer,
    .orders-detail-actions,
    .orders-offer-actions,
    .orders-inline-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .orders-empty {
        padding: 40px 28px;
        text-align: center;
    }

    .orders-layout-main {
        display: grid;
        gap: 18px;
    }

    .orders-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .orders-summary-item {
        padding: 16px;
        border-radius: 18px;
        background: #f7f8ff;
        border: 1px solid rgba(97, 93, 250, 0.08);
    }

    .orders-summary-value {
        color: #20263f;
        font-size: 1rem;
        font-weight: 800;
        margin-top: 6px;
    }

    .orders-divider {
        height: 1px;
        background: rgba(97, 93, 250, 0.1);
        margin: 18px 0;
    }

    .orders-offer-stack {
        display: grid;
        gap: 16px;
    }

    .orders-offer-card.is-awarded {
        border-color: rgba(97, 93, 250, 0.24);
        box-shadow: 0 22px 40px rgba(97, 93, 250, 0.13);
    }

    .orders-rating {
        display: inline-flex;
        gap: 6px;
        color: #ffbf47;
        font-size: 0.9rem;
    }

    .orders-form-layout {
        display: grid;
        gap: 18px;
    }

    .orders-form-section {
        display: grid;
        gap: 14px;
        padding: 20px;
        border-radius: 20px;
        background: #f8f9ff;
        border: 1px solid rgba(97, 93, 250, 0.08);
    }

    .orders-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .orders-admin-table {
        width: 100%;
    }

    .orders-admin-table th {
        white-space: nowrap;
    }

    body[data-theme="css_d"] .orders-toolbar,
    body[data-theme="css_d"] .orders-empty,
    body[data-theme="css_d"] .orders-panel,
    body[data-theme="css_d"] .orders-form-preview,
    body[data-theme="css_d"] .orders-offer-form,
    body[data-theme="css_d"] .orders-admin-card,
    body[data-theme="css_d"] .orders-card,
    body[data-theme="css_d"] .orders-offer-card {
        background: #1f2637;
        border-color: #2d3650;
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.18);
    }

    body[data-theme="css_d"] .orders-filter-input,
    body[data-theme="css_d"] .orders-filter-select,
    body[data-theme="css_d"] .orders-textarea,
    body[data-theme="css_d"] .orders-form-section,
    body[data-theme="css_d"] .orders-summary-item {
        background: #242c3f;
        border-color: #34405b;
        color: #f1f4ff;
    }

    body[data-theme="css_d"] .orders-toolbar-title,
    body[data-theme="css_d"] .orders-card-title,
    body[data-theme="css_d"] .orders-detail-title,
    body[data-theme="css_d"] .orders-offer-title,
    body[data-theme="css_d"] .orders-summary-value {
        color: #fff;
    }

    body[data-theme="css_d"] .orders-toolbar-copy,
    body[data-theme="css_d"] .orders-card-copy,
    body[data-theme="css_d"] .orders-muted,
    body[data-theme="css_d"] .orders-card-description,
    body[data-theme="css_d"] .orders-offer-message,
    body[data-theme="css_d"] .orders-detail-description,
    body[data-theme="css_d"] .orders-admin-copy {
        color: #9ba6c4;
    }

    body[data-theme="css_d"] .orders-meta-pill {
        background: #2b344d;
        color: #d5dcf6;
    }

    body[data-theme="css_d"] .orders-divider {
        background: rgba(255, 255, 255, 0.08);
    }

    @media (max-width: 768px) {
        .orders-filters,
        .orders-form-grid,
        .orders-summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endonce
