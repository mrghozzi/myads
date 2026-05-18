@once
<style>
    .custom-ads-shell { display: grid; gap: 22px; }
    .custom-ads-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin: 22px 0; }
    .custom-ads-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .custom-ads-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
    .custom-ads-card { padding: 20px; border: 1px solid #edf0f7; border-radius: 8px; background: #fff; }
    .custom-ads-card h4 { margin: 0 0 8px; color: #3e3f5e; font-size: 1rem; font-weight: 800; }
    .custom-ads-muted { color: #8f91ac; font-size: .86rem; line-height: 1.6; }
    .custom-ads-stat { font-size: 1.55rem; color: #0f766e; font-weight: 900; }
    .custom-ads-pills { display: flex; flex-wrap: wrap; gap: 8px; }
    .custom-ads-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 6px; background: #ecfeff; color: #0f766e; font-size: .76rem; font-weight: 800; }
    .custom-ads-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .custom-ads-table th { padding: 12px 14px; color: #8f91ac; text-transform: uppercase; font-size: .75rem; border-bottom: 1px solid #edf0f7; }
    .custom-ads-table td { padding: 14px; border-bottom: 1px solid #f3f4f8; vertical-align: top; }
    .custom-ads-status { display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 6px; font-size: .74rem; font-weight: 800; text-transform: uppercase; }
    .custom-ads-status.active { background: #dcfce7; color: #166534; }
    .custom-ads-status.pending, .custom-ads-status.invited { background: #fef3c7; color: #92400e; }
    .custom-ads-status.paused { background: #e0f2fe; color: #075985; }
    .custom-ads-status.cancelled, .custom-ads-status.rejected, .custom-ads-status.disabled { background: #fee2e2; color: #991b1b; }
    .custom-ads-status.completed { background: #ede9fe; color: #5b21b6; }
    .custom-ads-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; }
    .custom-ads-preview { border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px; background: #fbfdff; }
    .custom-ads-code { width: 100%; min-height: 90px; border: 1px solid #d7dae8; border-radius: 8px; padding: 12px; color: #111827; background: #f8fafc; font-size: .85rem; direction: ltr; }
    @media (max-width: 680px) {
        .custom-ads-table thead { display: none; }
        .custom-ads-table tr { display: block; padding: 10px 0; border-bottom: 1px solid #edf0f7; }
        .custom-ads-table td { display: block; border-bottom: 0; padding: 8px 12px; }
    }
</style>
@endonce
