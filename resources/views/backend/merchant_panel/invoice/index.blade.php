@extends('backend.partials.master')
@section('title')
    {{ __('menus.invoice') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('menus.invoice') }}</h1>
            <p class="wc-page-subtitle">Vos relevés de facturation, période par période.</p>
        </div>
    </div>

    @if(count($invoices) === 0)
        <div class="wc-card">
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-file-invoice"></i></div>
                <p class="wc-empty-title">Aucune facture</p>
                <p class="wc-empty-description">Vos factures générées apparaîtront ici.</p>
            </div>
        </div>
    @else
        {{-- Bandeau de synthèse comptable --}}
        <div class="wc-inv-summary animate-wcFadeUp" style="animation-delay:.02s">
            <div class="wc-inv-summary-item">
                <span class="wc-inv-summary-ic" style="background:#eef1f5;color:#334155"><i class="fas fa-file-invoice"></i></span>
                <div>
                    <p class="wc-inv-summary-label">{{ __('levels.total') }}</p>
                    <p class="wc-inv-summary-value">{{ $stats['total'] }} <small class="wc-inv-summary-sub">factures</small></p>
                </div>
            </div>
            <div class="wc-inv-summary-item">
                <span class="wc-inv-summary-ic" style="background:#ecfdf5;color:#059669"><i class="fas fa-hand-holding-usd"></i></span>
                <div>
                    <p class="wc-inv-summary-label">{{ __('parcel.cash_collection') }}</p>
                    <p class="wc-inv-summary-value">{{ formatPrice($stats['collection']) }}</p>
                </div>
            </div>
            <div class="wc-inv-summary-item">
                <span class="wc-inv-summary-ic" style="background:#fffbeb;color:#d97706"><i class="fas fa-receipt"></i></span>
                <div>
                    <p class="wc-inv-summary-label">{{ __('parcel.Total_Charge') }}</p>
                    <p class="wc-inv-summary-value">{{ formatPrice($stats['charges']) }}</p>
                </div>
            </div>
            <div class="wc-inv-summary-item wc-inv-summary-item-net">
                <span class="wc-inv-summary-ic" style="background:#f5f3ff;color:#7c3aed"><i class="fas fa-coins"></i></span>
                <div>
                    <p class="wc-inv-summary-label">{{ __('parcel.current_payable') }}</p>
                    <p class="wc-inv-summary-value">{{ formatPrice($stats['payable']) }}</p>
                </div>
            </div>
            <div class="wc-inv-summary-item wc-inv-summary-item-legend">
                <div class="wc-inv-legend-row"><span class="wc-inv-dot" style="background:#059669"></span> {{ $stats['paid'] }} payée(s)</div>
                <div class="wc-inv-legend-row"><span class="wc-inv-dot" style="background:#d97706"></span> {{ $stats['processing'] }} en traitement</div>
                <div class="wc-inv-legend-row"><span class="wc-inv-dot" style="background:#dc2626"></span> {{ $stats['unpaid'] }} non payée(s)</div>
            </div>
        </div>

        {{-- Recherche + filtres --}}
        <div class="wc-card mb-4 animate-wcFadeUp" style="animation-delay:.06s">
            <div class="flex items-center gap-3 flex-wrap px-4 py-3">
                <div class="relative flex-1 min-w-[220px]">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[13px] pointer-events-none"></i>
                    <input type="text" id="wcInvSearch" class="wc-input !pl-9" placeholder="Rechercher un relevé (n°, date, montant)...">
                </div>
                <div class="flex items-center gap-1.5" id="wcInvFilters" role="group" aria-label="Filtrer par statut">
                    <button type="button" class="wc-btn wc-btn-primary wc-btn-sm wc-inv-filter" data-filter="all">{{ __('levels.total') }}</button>
                    <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-inv-filter" data-filter="paid">{{ __('invoice.3') }}</button>
                    <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-inv-filter" data-filter="processing">{{ __('invoice.2') }}</button>
                    <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-inv-filter" data-filter="unpaid">{{ __('invoice.0') }}</button>
                </div>
                <span class="text-[12.5px] text-wc-muted wc-tabular whitespace-nowrap" id="wcInvCounter"></span>
            </div>
        </div>

        {{-- Relevés (documents empilés) --}}
        <div class="wc-inv-docs">
            @foreach ($invoices as $invoice)
                @php
                    $statusKey = (int) $invoice->status;
                    $rail = match($statusKey) {
                        \App\Enums\InvoiceStatus::PAID => 'paid',
                        \App\Enums\InvoiceStatus::UNPAID => 'unpaid',
                        default => 'processing',
                    };
                    $statusText = trans('invoice.'.$statusKey);
                @endphp
                <div class="wc-inv-doc animate-wcRowIn wc-inv-doc-{{ $rail }}" style="animation-delay: {{ $loop->iteration * 0.04 }}s"
                    data-search="{{ mb_strtolower(@$invoice->invoice_id.' '.$invoice->invoice_date.' '.number_format((float) $invoice->current_payable, 0, ',', '')) }}"
                    data-status="{{ $rail }}">
                    <div class="wc-inv-rail"><i class="fas fa-file-invoice"></i></div>
                    <div class="wc-inv-doc-body">
                        <div class="wc-inv-doc-head">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="wc-inv-doc-no m-0">{{ @$invoice->invoice_id }}</p>
                                    <span class="wc-badge {{ $rail == 'paid' ? 'wc-badge-success' : ($rail == 'unpaid' ? 'wc-badge-error' : 'wc-badge-warning') }}"><i class="fas fa-circle text-[6px] mr-1.5"></i>{{ $statusText }}</span>
                                </div>
                                <p class="wc-inv-doc-meta m-0">
                                    <span class="wc-tabular">#{{ $invoice->id }}</span>
                                    <span class="sep">·</span>
                                    <span>{{ @$invoice->invoice_date }}</span>
                                </p>
                            </div>
                            <div class="wc-inv-doc-actions">
                                <a href="{{ route('merchant.panel.invoice.details',$invoice->invoice_id) }}" class="wc-btn wc-btn-primary wc-btn-sm"><i class="fa fa-eye"></i> {{ __('levels.details') }}</a>
                                <a href="{{ route('merchant.panel.invoice.csv',[$invoice->merchant_id,$invoice->invoice_id]) }}" class="wc-btn wc-btn-outline wc-btn-sm"><i class="fa fa-download"></i> CSV</a>
                            </div>
                        </div>
                        <div class="wc-inv-doc-amounts">
                            <div class="wc-inv-doc-amount">
                                <span class="wc-inv-doc-amount-label">{{ __('parcel.cash_collection') }}</span>
                                <span class="wc-inv-doc-amount-value">{{ formatPrice(@$invoice->cash_collection) }}</span>
                            </div>
                            <div class="wc-inv-doc-amount">
                                <span class="wc-inv-doc-amount-label">{{ __('parcel.Total_Charge') }}</span>
                                <span class="wc-inv-doc-amount-value">{{ formatPrice(@$invoice->total_charge) }}</span>
                            </div>
                            <div class="wc-inv-doc-amount wc-inv-doc-amount-net">
                                <span class="wc-inv-doc-amount-label">{{ __('parcel.current_payable') }}</span>
                                <span class="wc-inv-doc-amount-value">{{ formatPrice(@$invoice->current_payable) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div id="wcInvNoResult" class="d-none">
                <div class="wc-card">
                    <div class="wc-empty !py-10">
                        <div class="wc-empty-icon"><i class="fas fa-filter"></i></div>
                        <p class="wc-empty-title">Aucun résultat</p>
                        <p class="wc-empty-description">Aucun relevé ne correspond à votre recherche ou filtre.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 flex-wrap mt-4 px-4 py-3 bg-white rounded-[14px] border border-wc-border">
            <p class="m-0 text-[12.5px] text-wc-muted">
                {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $invoices->firstItem() }}</span>
                {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $invoices->lastItem() }}</span>
                {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $invoices->total() }}</span> {!! __('results') !!}
            </p>
            <span class="flex items-center gap-1">{{ $invoices->links() }}</span>
        </div>
    @endif
</div>
@endsection()

@push('styles')
<style>
    /* Bandeau de synthèse comptable */
    .wc-inv-summary {
        display: flex;
        align-items: stretch;
        gap: 12px;
        flex-wrap: wrap;
        background: #fff;
        border: 1px solid #e7ebe9;
        border-radius: 16px;
        padding: 16px 18px;
        margin-bottom: 16px;
    }
    .wc-inv-summary-item {
        display: flex;
        align-items: center;
        gap: 11px;
        padding-right: 18px;
        border-right: 1px dashed #e7ebe9;
        min-width: 0;
    }
    .wc-inv-summary-item:last-child { border-right: 0; }
    .wc-inv-summary-ic {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }
    .wc-inv-summary-label {
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #94a3b8;
        margin: 0;
    }
    .wc-inv-summary-value {
        font-size: 17px;
        font-weight: 800;
        color: #111827;
        margin: 2px 0 0;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .wc-inv-summary-sub { font-size: 11px; font-weight: 700; color: #94a3b8; }
    .wc-inv-summary-item-net .wc-inv-summary-value { color: #7c3aed; }
    .wc-inv-summary-item-legend { flex-direction: column; align-items: flex-start; justify-content: center; gap: 4px; }
    .wc-inv-legend-row { font-size: 12px; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 7px; }
    .wc-inv-dot { width: 8px; height: 8px; border-radius: 999px; display: inline-block; }

    /* Relevés documents */
    .wc-inv-docs {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .wc-inv-doc {
        display: flex;
        background: #fff;
        border: 1px solid #e7ebe9;
        border-radius: 16px;
        overflow: hidden;
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }
    .wc-inv-doc:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, .07);
        border-color: #cde9dc;
    }
    .wc-inv-rail {
        width: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 17px;
        flex-shrink: 0;
    }
    .wc-inv-doc-paid .wc-inv-rail { background: linear-gradient(180deg, #34d399, #059669); }
    .wc-inv-doc-unpaid .wc-inv-rail { background: linear-gradient(180deg, #f87171, #dc2626); }
    .wc-inv-doc-processing .wc-inv-rail { background: linear-gradient(180deg, #fbbf24, #d97706); }

    .wc-inv-doc-body { padding: 15px 18px; flex: 1; min-width: 0; }
    .wc-inv-doc-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }
    .wc-inv-doc-no {
        font-size: 15.5px;
        font-weight: 800;
        color: #111827;
        letter-spacing: .01em;
    }
    .wc-inv-doc-meta {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 3px;
    }
    .wc-inv-doc-meta .sep { margin: 0 6px; opacity: .6; }
    .wc-inv-doc-actions { display: flex; gap: 8px; flex-shrink: 0; }

    .wc-inv-doc-amounts {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-top: 13px;
        background: #fafbfb;
        border: 1px solid #eef0f3;
        border-radius: 12px;
        padding: 11px 14px;
    }
    .wc-inv-doc-amount {
        display: flex;
        flex-direction: column;
        gap: 2px;
        border-right: 1px dashed #e2e8f0;
        padding-right: 12px;
    }
    .wc-inv-doc-amount:last-child { border-right: 0; padding-right: 0; }
    .wc-inv-doc-amount-label {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #94a3b8;
    }
    .wc-inv-doc-amount-value {
        font-size: 14.5px;
        font-weight: 800;
        color: #334155;
        font-variant-numeric: tabular-nums;
    }
    .wc-inv-doc-amount-net .wc-inv-doc-amount-value { color: #7c3aed; font-size: 16px; }

    @media (max-width: 575.98px) {
        .wc-inv-summary-item { border-right: 0; padding-right: 0; width: 100%; }
        .wc-inv-doc-amounts { grid-template-columns: 1fr; }
        .wc-inv-doc-amount { border-right: 0; padding-right: 0; padding-bottom: 8px; border-bottom: 1px dashed #e2e8f0; }
        .wc-inv-doc-amount:last-child { border-bottom: 0; padding-bottom: 0; }
    }
</style>
@endpush

@push('scripts')
<script type="text/javascript">
"use strict";
(function () {
    const rows = Array.prototype.slice.call(document.querySelectorAll('.wc-inv-doc'));
    const searchInput = document.getElementById('wcInvSearch');
    const counter = document.getElementById('wcInvCounter');
    const noResult = document.getElementById('wcInvNoResult');
    const filterBtns = Array.prototype.slice.call(document.querySelectorAll('.wc-inv-filter'));

    let activeFilter = 'all';
    let searchTerm = '';

    function normalize(s) {
        return (s || '').toString().toLowerCase().replace(/\s+/g, ' ').trim();
    }

    function applyFilters() {
        let visible = 0;
        rows.forEach(function (row) {
            const matchStatus = activeFilter === 'all' || row.dataset.status === activeFilter;
            const matchSearch = searchTerm === '' || normalize(row.dataset.search).indexOf(searchTerm) !== -1;
            const show = matchStatus && matchSearch;
            row.classList.toggle('d-none', !show);
            if (show) visible++;
        });

        if (noResult) noResult.classList.toggle('d-none', visible !== 0);
        if (counter) {
            counter.textContent = visible + ' / ' + rows.length + ' affichés';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            searchTerm = normalize(searchInput.value);
            applyFilters();
        });
    }

    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterBtns.forEach(function (b) {
                b.classList.remove('wc-btn-primary');
                b.classList.add('wc-btn-soft');
            });
            btn.classList.remove('wc-btn-soft');
            btn.classList.add('wc-btn-primary');
            activeFilter = btn.dataset.filter;
            applyFilters();
        });
    });

    applyFilters();
})();
</script>
@endpush