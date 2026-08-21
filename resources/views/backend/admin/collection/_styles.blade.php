<style>
.cl-col-page{--cl-green:#059669;--cl-green-dark:#047857;--cl-green-soft:#ecfdf5;--cl-ink:#1e293b;--cl-ink-2:#475569;--cl-muted:#64748b;--cl-muted-2:#94a3b8;--cl-border:#e7ebe9;--cl-bg-soft:#f8fafc}
.cl-col-page .cl-header{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px}
.cl-col-page .cl-title{font-size:22px;font-weight:800;color:var(--cl-ink);margin:0}
.cl-col-page .cl-subtitle{font-size:13px;color:var(--cl-muted);margin:0}
.cl-col-page .cl-card{background:#fff;border:1px solid var(--cl-border);border-radius:16px;box-shadow:0 1px 2px rgba(16,24,40,.04)}
.cl-col-page .cl-btn{display:inline-flex;align-items:center;gap:7px;border:none;border-radius:10px;font-size:13px;font-weight:600;padding:9px 16px;cursor:pointer;transition:all .2s ease;text-decoration:none;line-height:1.2}
.cl-col-page .cl-btn:focus{outline:none}
.cl-col-page .cl-btn-primary{background:linear-gradient(135deg,var(--cl-green),var(--cl-green-dark));color:#fff;box-shadow:0 4px 12px rgba(5,150,105,.25)}
.cl-col-page .cl-btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(5,150,105,.35);color:#fff}
.cl-col-page .cl-btn-soft{background:var(--cl-bg-soft);color:var(--cl-ink-2);border:1px solid var(--cl-border)}
.cl-col-page .cl-btn-soft:hover{border-color:#a7f3d0;color:var(--cl-green);background:var(--cl-green-soft)}
.cl-col-page .cl-btn-danger{background:#fef2f2;color:#dc2626;border:1px solid #fecaca}
.cl-col-page .cl-btn-danger:hover{background:#fee2e2;color:#b91c1c}
.cl-col-page .cl-btn-sm{padding:6px 11px;font-size:12px;border-radius:8px}
.cl-col-page .cl-btn-icon{min-width:32px;padding:6px 8px;justify-content:center}
.cl-kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:20px}
.cl-kpi{background:#fff;border:1px solid var(--cl-border);border-radius:16px;padding:16px 18px;display:flex;flex-direction:column;gap:6px;animation:clFadeUp .4s ease both}
.cl-kpi-top{display:flex;align-items:center;justify-content:space-between}
.cl-kpi-label{font-size:12px;font-weight:600;color:var(--cl-muted);margin:0}
.cl-kpi-icon{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0}
.cl-kpi-value{font-size:26px;font-weight:800;color:var(--cl-ink);line-height:1.1;font-variant-numeric:tabular-nums}
.cl-kpi-sub{font-size:11px;color:var(--cl-muted-2);margin:0;display:flex;align-items:center;gap:6px}
.cl-dot{width:6px;height:6px;border-radius:50%;display:inline-block}
.cl-dot-green{background:#22c55e}.cl-dot-amber{background:#f59e0b}.cl-dot-blue{background:#3b82f6}.cl-dot-violet{background:#8b5cf6}.cl-dot-red{background:#ef4444}
@keyframes clPulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.45;transform:scale(.8)}}
.cl-live{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;color:var(--cl-green);background:var(--cl-green-soft);border:1px solid #a7f3d0;padding:5px 12px;border-radius:999px}
.cl-live i{font-size:7px;animation:clPulse 1.6s ease infinite}
.cl-filter-bar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:14px 16px;border-bottom:1px solid var(--cl-border);background:var(--cl-bg-soft);border-radius:16px 16px 0 0}
.cl-pill{border:1px solid var(--cl-border);background:#fff;color:var(--cl-muted);font-size:12px;font-weight:600;padding:6px 13px;border-radius:999px;cursor:pointer;transition:all .2s ease;display:inline-flex;align-items:center;gap:6px;text-decoration:none}
.cl-pill:hover{border-color:#a7f3d0;color:var(--cl-green);background:var(--cl-green-soft);text-decoration:none}
.cl-pill.active{background:var(--cl-green);border-color:var(--cl-green);color:#fff;box-shadow:0 2px 8px rgba(5,150,105,.25)}
.cl-pill .cl-count{background:rgba(5,150,105,.12);color:var(--cl-green);font-size:10px;font-weight:800;padding:1px 7px;border-radius:999px}
.cl-pill.active .cl-count{background:rgba(255,255,255,.25);color:#fff}
.cl-input{border:1px solid var(--cl-border);border-radius:10px;font-size:12.5px;padding:7px 12px;color:var(--cl-ink);background:#fff;transition:all .2s ease}
.cl-input:focus{outline:none;border-color:var(--cl-green);box-shadow:0 0 0 3px rgba(5,150,105,.12)}
.cl-select{border:1px solid var(--cl-border);border-radius:10px;font-size:12.5px;padding:7px 12px;color:var(--cl-ink);background:#fff}
.cl-select:focus{outline:none;border-color:var(--cl-green);box-shadow:0 0 0 3px rgba(5,150,105,.12)}
.cl-table-wrap{overflow-x:auto}
.cl-table{width:100%;border-collapse:collapse;font-size:13px}
.cl-table thead th{background:var(--cl-bg-soft);color:var(--cl-muted-2);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;text-align:left;border-bottom:1px solid var(--cl-border);white-space:nowrap}
.cl-table tbody td{padding:12px 14px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.cl-table tbody tr:last-child td{border-bottom:none}
.cl-table tbody tr{transition:background .15s ease;animation:clRowIn .35s ease both}
.cl-table tbody tr:hover{background:#f8fafc}
@keyframes clFadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
@keyframes clRowIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
.cl-badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:4px 11px;border-radius:999px;white-space:nowrap}
.cl-badge-warning{background:#fffbeb;color:#d97706;border:1px solid #fde68a}
.cl-badge-info{background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe}
.cl-badge-transit{background:#ecfdf5;color:#059669;border:1px solid #a7f3d0}
.cl-badge-success{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0}
.cl-badge-delivered{background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe}
.cl-badge-error{background:#fef2f2;color:#dc2626;border:1px solid #fecaca}
.cl-badge-neutral{background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0}
.cl-avatar{width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0}
.cl-code{font-size:11px;font-weight:700;color:var(--cl-green);background:var(--cl-green-soft);padding:2px 8px;border-radius:6px;font-family:ui-monospace,monospace}
.cl-steps{display:flex;align-items:center;min-width:86px}
.cl-step{width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:6px;flex-shrink:0}
.cl-step.done{background:var(--cl-green);color:#fff}
.cl-step.active{background:var(--cl-green);color:#fff;box-shadow:0 0 0 3px rgba(5,150,105,.2)}
.cl-step.todo{background:#e5e7eb;color:#9ca3af}
.cl-step-line{flex:1;height:2px;min-width:10px}
.cl-step-line.done{background:var(--cl-green)}
.cl-step-line.todo{background:#e5e7eb}
.cl-empty{text-align:center;padding:48px 20px}
.cl-empty-icon{width:64px;height:64px;border-radius:20px;background:var(--cl-green-soft);color:var(--cl-green);display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 14px}
.cl-empty-title{font-weight:800;color:var(--cl-ink);margin:0 0 4px;font-size:15px}
.cl-empty-desc{font-size:12.5px;color:var(--cl-muted);margin:0}
.cl-pager{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:13px 16px;border-top:1px solid var(--cl-border);font-size:12.5px;color:var(--cl-muted)}
.cl-pager .pagination{margin:0}
.cl-pager .page-link{border:1px solid var(--cl-border);color:var(--cl-ink-2);font-size:12px;border-radius:8px;margin:0 2px;padding:5px 10px}
.cl-pager .page-item.active .page-link{background:var(--cl-green);border-color:var(--cl-green);color:#fff}
.cl-pager .page-item.disabled .page-link{color:var(--cl-muted-2)}
.cl-muted{color:var(--cl-muted)}.cl-muted-2{color:var(--cl-muted-2)}.cl-ink{color:var(--cl-ink)}.cl-ink-2{color:var(--cl-ink-2)}.cl-green{color:var(--cl-green)}
.cl-fw-8{font-weight:800}.cl-fw-7{font-weight:700}.cl-fw-6{font-weight:600}
.cl-fs-13{font-size:13px}.cl-fs-12{font-size:12px}.cl-fs-11{font-size:11px}.cl-fs-10{font-size:10px}
.cl-tabular{font-variant-numeric:tabular-nums}
.cl-truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.cl-fab{position:fixed;bottom:24px;right:24px;z-index:1050;width:54px;height:54px;border-radius:16px;background:linear-gradient(135deg,var(--cl-green),var(--cl-green-dark));color:#fff;border:none;font-size:20px;cursor:pointer;box-shadow:0 8px 24px rgba(5,150,105,.3);display:none;align-items:center;justify-content:center}
@media(max-width:767px){.cl-fab{display:flex}}
.cl-modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);backdrop-filter:blur(2px);z-index:2000;display:none;align-items:center;justify-content:center;padding:16px}
.cl-modal-overlay.show{display:flex}
.cl-modal{background:#fff;border-radius:18px;width:100%;max-width:520px;box-shadow:0 24px 64px rgba(15,23,42,.2);animation:clPop .25s cubic-bezier(.4,0,.2,1) both}
@keyframes clPop{from{opacity:0;transform:scale(.94) translateY(8px)}to{opacity:1;transform:scale(1) translateY(0)}}
.cl-modal-head{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--cl-border)}
.cl-modal-title{font-size:15px;font-weight:800;color:var(--cl-ink);margin:0}
.cl-modal-body{padding:20px}
.cl-modal-foot{display:flex;justify-content:flex-end;gap:10px;padding:14px 20px;border-top:1px solid var(--cl-border);background:var(--cl-bg-soft);border-radius:0 0 18px 18px}
.cl-label{font-size:12px;font-weight:700;color:var(--cl-ink-2);display:block;margin-bottom:6px}
.cl-spin{width:16px;height:16px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;display:inline-block;animation:clSpin .6s linear infinite}
@keyframes clSpin{to{transform:rotate(360deg)}}
</style>
