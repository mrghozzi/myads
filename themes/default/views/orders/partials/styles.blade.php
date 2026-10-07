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

    .orders-owner-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
    }

    .orders-owner-card .user-avatar {
        position: relative !important;
        top: 0 !important;
        left: 0 !important;
        margin: 0 auto 16px !important;
    }

    body[dir="rtl"] .orders-owner-card .user-avatar {
        left: auto !important;
        right: 0 !important;
        margin: 0 auto 16px !important;
    }

    /* Disclaimer Card */
    .orders-disclaimer-card {
        border-radius: 18px;
        padding: 18px 22px;
        background: linear-gradient(135deg, rgba(97, 93, 250, 0.06), rgba(35, 210, 226, 0.08));
        border: 1px solid rgba(97, 93, 250, 0.2);
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 20px;
    }

    .orders-disclaimer-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #615dfa;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .orders-disclaimer-content {
        flex: 1;
    }

    .orders-disclaimer-title {
        color: #1f2440;
        font-size: 0.95rem;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .orders-disclaimer-text {
        color: #616682;
        font-size: 0.88rem;
        line-height: 1.6;
        margin: 0;
    }

    /* Stepper / Progress Tracker */
    .orders-stepper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        padding: 10px 0;
        margin-bottom: 24px;
    }

    .orders-stepper::before {
        content: "";
        position: absolute;
        top: 24px;
        left: 30px;
        right: 30px;
        height: 4px;
        background: #e7e8f5;
        z-index: 1;
    }

    .orders-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }

    .orders-step-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #d4d6ee;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
        color: #8b90aa;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }

    .orders-step.active .orders-step-circle {
        border-color: #615dfa;
        background: #615dfa;
        color: #fff;
        box-shadow: 0 0 12px rgba(97, 93, 250, 0.4);
    }

    .orders-step.completed .orders-step-circle {
        border-color: #23d2e2;
        background: #23d2e2;
        color: #fff;
    }

    .orders-step-label {
        font-size: 0.76rem;
        font-weight: 700;
        color: #8b90aa;
    }

    .orders-step.active .orders-step-label {
        color: #615dfa;
        font-weight: 800;
    }

    .orders-step.completed .orders-step-label {
        color: #23d2e2;
    }

    /* Attachment & Deliverable cards */
    .orders-attachment-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 18px;
        border-radius: 14px;
        background: #f8f9ff;
        border: 1px dashed rgba(97, 93, 250, 0.3);
        margin-top: 14px;
    }

    .orders-attachment-icon {
        font-size: 1.4rem;
        color: #615dfa;
    }

    .orders-revision-box {
        border-radius: 14px;
        padding: 16px 20px;
        background: rgba(255, 174, 0, 0.08);
        border: 1px dashed #ffae00;
        margin-top: 16px;
    }

    /* Dark Mode Adjustments */
    body[data-theme="css_d"] .orders-disclaimer-card {
        background: linear-gradient(135deg, rgba(97, 93, 250, 0.15), rgba(35, 210, 226, 0.1));
        border-color: rgba(97, 93, 250, 0.3);
    }

    body[data-theme="css_d"] .orders-disclaimer-title {
        color: #fff;
    }

    body[data-theme="css_d"] .orders-disclaimer-text {
        color: #a4b0d1;
    }

    body[data-theme="css_d"] .orders-stepper::before {
        background: #293249;
    }

    body[data-theme="css_d"] .orders-step-circle {
        background: #1d2333;
        border-color: #2f3852;
        color: #7280a5;
    }

    body[data-theme="css_d"] .orders-attachment-card {
        background: #1d2333;
        border-color: rgba(97, 93, 250, 0.4);
    }

    @media (max-width: 768px) {
        .orders-filters,
        .orders-form-grid,
        .orders-summary-grid {
            grid-template-columns: 1fr;
        }

        .orders-stepper {
            flex-wrap: wrap;
            gap: 12px;
        }

        .orders-stepper::before {
            display: none;
        }
    }
</style>
@endonce
