@extends('backend.partials.master')
@section('title')
    {{ __('paymentrequest.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('paymentrequest.title') }}</h1>
            <p class="wc-page-subtitle">Suivez vos demandes de retrait, de la soumission au versement.</p>
        </div>
        <div class="wc-toolbar">
            <span class="wc-pay-balance">
                <i class="fas fa-wallet"></i>
                {{ __('parcel.my_wallet') }} : <b>{{ formatPrice(optional($merchant)->current_balance) }}</b>
            </span>
            <a href="{{ route('merchant-panel.payment-request.create') }}" class="wc-btn wc-btn-primary wc-btn-sm">
                <i class="fas fa-plus"></i> {{ __('levels.add') }}
            </a>
        </div>
    </div>

    @if(count($payments) === 0)
        <div class="wc-card">
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-credit-card"></i></div>
                <p class="wc-empty-title">{{ __('paymentrequest.title') }}</p>
                <p class="wc-empty-description">Aucune demande de paiement pour le moment.</p>
                <a href="{{ route('merchant-panel.payment-request.create') }}" class="wc-btn wc-btn-primary wc-btn-sm mt-3">
                    <i class="fas fa-plus"></i> {{ __('levels.add') }}
                </a>
            </div>
        </div>
    @else
        {{-- Recherche globale du board --}}
        <div class="wc-card mb-4">
            <div class="flex items-center gap-3 flex-wrap px-4 py-3">
                <div class="relative flex-1 min-w-[220px]">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[13px] pointer-events-none"></i>
                    <input type="text" id="wcPaySearch" class="wc-input !pl-9" placeholder="Rechercher dans toutes les colonnes (transaction, compte, montant)...">
                </div>
                <span class="text-[12.5px] text-wc-muted wc-tabular whitespace-nowrap" id="wcPayCounter"></span>
            </div>
        </div>

        {{-- Kanban : une colonne par statut --}}
        <div class="wc-pay-board">

            {{-- Colonne : En attente --}}
            <div class="wc-pay-col wc-pay-col-pending animate-wcFadeUp" data-col="pending" style="animation-delay:.02s">
                <div class="wc-pay-col-head">
                    <span><i class="fas fa-clock"></i> {{ __('approvalstatus.3') }}</span>
                    <span class="wc-pay-col-count" id="wcPayCountPending">0</span>
                </div>
                <div class="wc-pay-col-body">
                    @foreach($payments as $payment)
                        @if(in_array((int) $payment->status, [\App\Enums\ApprovalStatus::PENDING, \App\Enums\ApprovalStatus::APPROVED]))
                            @include('backend.merchant_panel.payment_request.partials.payment-card', ['payment' => $payment, 'col' => 'pending'])
                        @endif
                    @endforeach
                    <div class="wc-pay-col-empty">Aucune demande en attente</div>
                </div>
            </div>

            {{-- Colonne : Traitées --}}
            <div class="wc-pay-col wc-pay-col-processed animate-wcFadeUp" data-col="processed" style="animation-delay:.08s">
                <div class="wc-pay-col-head">
                    <span><i class="fas fa-check-circle"></i> {{ __('approvalstatus.4') }}</span>
                    <span class="wc-pay-col-count" id="wcPayCountProcessed">0</span>
                </div>
                <div class="wc-pay-col-body">
                    @foreach($payments as $payment)
                        @if((int) $payment->status == \App\Enums\ApprovalStatus::PROCESSED)
                            @include('backend.merchant_panel.payment_request.partials.payment-card', ['payment' => $payment, 'col' => 'processed'])
                        @endif
                    @endforeach
                    <div class="wc-pay-col-empty">Aucune demande traitée</div>
                </div>
            </div>

            {{-- Colonne : Rejetées --}}
            <div class="wc-pay-col wc-pay-col-rejected animate-wcFadeUp" data-col="rejected" style="animation-delay:.14s">
                <div class="wc-pay-col-head">
                    <span><i class="fas fa-times-circle"></i> {{ __('approvalstatus.1') }}</span>
                    <span class="wc-pay-col-count" id="wcPayCountRejected">0</span>
                </div>
                <div class="wc-pay-col-body">
                    @foreach($payments as $payment)
                        @if((int) $payment->status == \App\Enums\ApprovalStatus::REJECT)
                            @include('backend.merchant_panel.payment_request.partials.payment-card', ['payment' => $payment, 'col' => 'rejected'])
                        @endif
                    @endforeach
                    <div class="wc-pay-col-empty">Aucune demande rejetée</div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 flex-wrap mt-4 px-4 py-3 bg-white rounded-[14px] border border-wc-border">
            <p class="m-0 text-[12.5px] text-wc-muted">
                {!! __('Showing') !!}
                <span class="font-bold text-wc-ink">{{ $payments->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-bold text-wc-ink">{{ $payments->lastItem() }}</span>
                {!! __('of') !!}
                <span class="font-bold text-wc-ink">{{ $payments->total() }}</span>
                {!! __('results') !!}
            </p>
            <span class="flex items-center gap-1">{{ $payments->links() }}</span>
        </div>
    @endif
</div>
@endsection()

@push('styles')
<style>
    .wc-pay-balance {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 14px;
        border-radius: 999px;
        background: #f0fdf4;
        border: 1px solid #a7f3d0;
        color: #047857;
        font-size: 12.5px;
        font-weight: 600;
    }
    .wc-pay-balance i { color: #059669; font-size: 12px; }
    .wc-pay-balance b { font-weight: 800; }

    .wc-pay-board {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        align-items: start;
    }

    .wc-pay-col {
        background: #f3f4f6;
        border: 1px solid #e7ebe9;
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .wc-pay-col-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        font-size: 12.5px;
        font-weight: 800;
        letter-spacing: .02em;
        border-bottom: 1px solid transparent;
    }
    .wc-pay-col-pending .wc-pay-col-head {
        background: #fffbeb;
        color: #b45309;
        border-bottom-color: #fde68a;
    }
    .wc-pay-col-processed .wc-pay-col-head {
        background: #ecfdf5;
        color: #047857;
        border-bottom-color: #a7f3d0;
    }
    .wc-pay-col-rejected .wc-pay-col-head {
        background: #fef2f2;
        color: #b91c1c;
        border-bottom-color: #fecaca;
    }

    .wc-pay-col-count {
        min-width: 26px;
        height: 26px;
        padding: 0 8px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
        background: rgba(255, 255, 255, .8);
        border: 1px solid rgba(0, 0, 0, .06);
    }

    .wc-pay-col-body {
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .wc-pay-card {
        background: #fff;
        border: 1px solid #e7ebe9;
        border-radius: 12px;
        padding: 13px 14px;
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        animation-duration: .35s;
    }
    .wc-pay-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, .07);
        border-color: #cde9dc;
    }

    .wc-pay-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }
    .wc-pay-card-title {
        font-size: 13px;
        font-weight: 800;
        color: #111827;
        margin: 0;
        line-height: 1.35;
    }
    .wc-pay-card-txn {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 600;
        margin: 1px 0 0;
        word-break: break-all;
    }
    .wc-pay-card-amount {
        font-size: 15px;
        font-weight: 800;
        color: #111827;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    .wc-pay-col-pending .wc-pay-card-amount { color: #b45309; }
    .wc-pay-col-processed .wc-pay-card-amount { color: #047857; }
    .wc-pay-col-rejected .wc-pay-card-amount { color: #b91c1c; }

    .wc-pay-card-desc {
        font-size: 12px;
        color: #64748b;
        margin: 8px 0 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.45;
    }
    .wc-pay-card-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        color: #94a3b8;
        margin-top: 9px;
        flex-wrap: wrap;
    }
    .wc-pay-card-meta .sep { opacity: .5; }

    .wc-pay-card-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #e7ebe9;
    }

    .wc-pay-col-empty {
        display: none;
        padding: 18px 12px;
        text-align: center;
        font-size: 12px;
        color: #94a3b8;
        border: 1px dashed #d3d9d6;
        border-radius: 10px;
    }
    .wc-pay-col.is-empty .wc-pay-col-empty { display: block; }

    @media (max-width: 991.98px) {
        .wc-pay-board { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script type="text/javascript">
"use strict";
(function () {
    const cards = Array.prototype.slice.call(document.querySelectorAll('.wc-pay-card'));
    const searchInput = document.getElementById('wcPaySearch');
    const counter = document.getElementById('wcPayCounter');
    const cols = Array.prototype.slice.call(document.querySelectorAll('.wc-pay-col'));

    function normalize(s) {
        return (s || '').toString().toLowerCase().replace(/\s+/g, ' ').trim();
    }

    function applyFilters() {
        const searchTerm = searchInput ? normalize(searchInput.value) : '';
        let visible = 0;
        const colCounts = { pending: 0, processed: 0, rejected: 0 };

        cards.forEach(function (card) {
            const matchSearch = searchTerm === '' || normalize(card.dataset.search).indexOf(searchTerm) !== -1;
            card.classList.toggle('d-none', !matchSearch);
            if (matchSearch) {
                visible++;
                colCounts[card.dataset.col]++;
            }
        });

        Object.keys(colCounts).forEach(function (key) {
            const el = document.getElementById('wcPayCount' + key.charAt(0).toUpperCase() + key.slice(1));
            if (el) el.textContent = colCounts[key];
        });

        cols.forEach(function (col) {
            const total = cards.filter(function (c) {
                return c.dataset.col === col.dataset.col;
            }).length;
            col.classList.toggle('is-empty', colCounts[col.dataset.col] === 0 && total > 0);
        });

        if (counter) counter.textContent = visible + ' / ' + cards.length + ' demandes';
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    applyFilters();
})();
</script>
@endpush