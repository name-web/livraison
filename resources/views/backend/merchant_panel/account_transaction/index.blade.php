@extends('backend.partials.master')
@section('title')
    {{ __('menus.account_transaction') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Header --}}
    <div class="wc-page-header animate-wcFadeUp">
        <div>
            <h1 class="wc-page-title">{{ __('menus.account_transaction') }}</h1>
            <p class="wc-page-subtitle">Mouvements chronologiques de vos comptes.</p>
        </div>
    </div>

    {{-- Stats inline --}}
    <div class="wc-tx-inline-stats animate-wcFadeUp" style="animation-delay:.03s">
        <div class="wc-tx-is-item">
            <span class="wc-tx-is-dot wc-tx-dot-total"></span>
            <span class="wc-tx-is-label">Total</span>
            <span class="wc-tx-is-val">{{ formatPrice($stats['total']) }}</span>
        </div>
        <div class="wc-tx-is-sep"></div>
        <div class="wc-tx-is-item">
            <span class="wc-tx-is-dot wc-tx-dot-pending"></span>
            <span class="wc-tx-is-label">En attente</span>
            <span class="wc-tx-is-val">{{ formatPrice($stats['pending']) }}</span>
        </div>
        <div class="wc-tx-is-sep"></div>
        <div class="wc-tx-is-item">
            <span class="wc-tx-is-dot wc-tx-dot-approved"></span>
            <span class="wc-tx-is-label">Approuvé</span>
            <span class="wc-tx-is-val">{{ formatPrice($stats['approved']) }}</span>
        </div>
        <div class="wc-tx-is-sep"></div>
        <div class="wc-tx-is-item">
            <span class="wc-tx-is-dot wc-tx-dot-rejected"></span>
            <span class="wc-tx-is-label">Rejeté</span>
            <span class="wc-tx-is-val">{{ formatPrice($stats['rejected']) }}</span>
        </div>
    </div>

    {{-- Filtres compacts --}}
    <div class="wc-tx-filter-bar animate-wcFadeUp" style="animation-delay:.06s">
        <form action="{{ route('merchant.accounts.account-transaction.filter') }}" method="POST" class="flex items-center gap-2 flex-wrap flex-1 m-0">
            @csrf
            <div class="relative flex-1 min-w-[180px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[12px] pointer-events-none"></i>
                <input type="text" id="wcTxLive" class="wc-input !pl-9 !py-2 !text-[12.5px]" placeholder="Recherche instantanée...">
            </div>
            <input type="text" autocomplete="off" name="date" class="wc-input date_range_picker !py-2 !text-[12.5px] min-w-[170px]" value="{{ old('date', $request->date ?? '') }}" placeholder="Période">
            <select name="type" class="wc-select !py-2 !text-[12.5px] min-w-[130px]">
                <option value="">Tous statuts</option>
                @foreach(config('rxcourier.approval_status') as $key => $value)
                    <option value="{{ $value }}" {{ (old('type', $request->type ?? '') == $value) ? 'selected' : '' }}>{{ __('Approvalstatus.'.$value) }}</option>
                @endforeach
            </select>
            <select name="account" class="wc-select !py-2 !text-[12.5px] min-w-[160px]">
                <option value="">Tous comptes</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" {{ (old('account', $request->account ?? '') == $acc->id) ? 'selected' : '' }}>
                        {{ $acc->payment_method == 'bank' ? $acc->branch_name.' ('.$acc->account_no.')' : $acc->mobile_company.' ('.$acc->mobile_no.')' }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="wc-btn wc-btn-primary wc-btn-sm !py-2"><i class="fas fa-filter text-[10px]"></i></button>
            <a href="{{ route('merchant.accounts.account-transaction.index') }}" class="wc-btn wc-btn-outline wc-btn-sm !py-2"><i class="fas fa-times text-[10px]"></i></a>
        </form>
    </div>

    {{-- Liste --}}
    @if($transactions->isEmpty() && empty($request->type) && empty($request->account) && empty($request->date))
        <div class="wc-card">
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-exchange-alt"></i></div>
                <p class="wc-empty-title">Aucune transaction</p>
            </div>
        </div>
    @else
        <div class="wc-tx-list animate-wcFadeUp" style="animation-delay:.09s">
            @foreach($transactions as $t)
                @php
                    $statusClass = match((int) $t->status) {
                        \App\Enums\ApprovalStatus::PENDING  => 'pending',
                        \App\Enums\ApprovalStatus::APPROVED => 'approved',
                        \App\Enums\ApprovalStatus::REJECT   => 'rejected',
                        default => 'pending',
                    };
                    $acc   = $t->merchantAccount ?? null;
                    $isBank   = $acc && $acc->payment_method === 'bank';
                    $isMobile = $acc && $acc->payment_method === 'mobile';
                    $bankName   = $isBank ? optional($acc->bank)->name : '';
                    $mobileName = $isMobile ? optional($acc->mobileBank)->name : '';
                    $searchText = e(($acc->holder_name ?? '').' '.$bankName.' '.$mobileName.' '.($acc->account_no ?? '').' '.($acc->mobile_no ?? '').' '.($t->transaction_id ?? '').' '.formatPrice($t->amount));
                @endphp
                <div class="wc-tx-row wc-tx-{{ $statusClass }}" data-search="{{ $searchText }}">
                    <div class="wc-tx-row-bar"></div>
                    <div class="wc-tx-row-body">
                        <div class="wc-tx-row-main">
                            <div class="wc-tx-row-left">
                                @if($isBank)
                                    <span class="wc-tx-chip wc-tx-chip-bank"><i class="fas fa-university"></i> Banque</span>
                                @else
                                    <span class="wc-tx-chip wc-tx-chip-mobile"><i class="fas fa-mobile-alt"></i> Mobile</span>
                                @endif
                                <span class="wc-tx-row-account">
                                    {{ $isBank ? ($acc->holder_name.' · '.$bankName.' · '.$acc->account_no) : ($mobileName.' · '.$acc->mobile_no) }}
                                </span>
                            </div>
                            <span class="wc-tx-row-amount">{{ formatPrice($t->amount) }}</span>
                        </div>
                        <div class="wc-tx-row-sub">
                            <span class="wc-tx-row-id"><i class="fas fa-hashtag"></i> {{ $t->transaction_id }}</span>
                            <span class="wc-tx-row-date">{{ $t->created_at->format('d M Y · H:i') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex items-center justify-between gap-3 flex-wrap mt-4 px-4 py-3 bg-white rounded-[14px] border border-wc-border animate-wcFadeUp" style="animation-delay:.12s">
            <p class="m-0 text-[12.5px] text-wc-muted">
                {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $transactions->firstItem() }}</span>
                {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $transactions->lastItem() }}</span>
                {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $transactions->total() }}</span> {!! __('results') !!}
            </p>
            <span class="flex items-center gap-1">{{ $transactions->links() }}</span>
        </div>
    @endif
</div>
@endsection()

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
/* ─── Stats inline bar ─── */
.wc-tx-inline-stats {
    display: flex;
    align-items: center;
    gap: 0;
    background: #fff;
    border: 1px solid #e7ebe9;
    border-radius: 12px;
    padding: 12px 20px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.wc-tx-is-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 0;
}
.wc-tx-is-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.wc-tx-dot-total   { background: #0ea5e9; }
.wc-tx-dot-pending { background: #f59e0b; }
.wc-tx-dot-approved{ background: #10b981; }
.wc-tx-dot-rejected{ background: #ef4444; }

.wc-tx-is-label {
    font-size: 11.5px;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.wc-tx-is-val {
    font-size: 13px;
    font-weight: 800;
    color: #1e293b;
    font-variant-numeric: tabular-nums;
}
.wc-tx-is-sep {
    width: 1px;
    height: 20px;
    background: #e2e8f0;
    margin: 0 16px;
}

/* ─── Filter bar ─── */
.wc-tx-filter-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid #e7ebe9;
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 14px;
}

/* ─── Timeline list ─── */
.wc-tx-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.wc-tx-row {
    display: flex;
    background: #fff;
    border: 1px solid #e7ebe9;
    border-radius: 12px;
    overflow: hidden;
    transition: transform .12s ease, box-shadow .12s ease;
}
.wc-tx-row:hover {
    transform: translateX(3px);
    box-shadow: 0 4px 16px rgba(15,23,42,.05);
}

.wc-tx-row-bar {
    width: 4px;
    flex-shrink: 0;
    border-radius: 4px 0 0 4px;
}
.wc-tx-pending  .wc-tx-row-bar { background: #f59e0b; }
.wc-tx-approved .wc-tx-row-bar { background: #10b981; }
.wc-tx-rejected .wc-tx-row-bar { background: #ef4444; }

.wc-tx-row-body {
    flex: 1;
    padding: 12px 16px;
    min-width: 0;
}

.wc-tx-row-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.wc-tx-row-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    flex: 1;
}
.wc-tx-row-account {
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.wc-tx-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 3px 9px;
    border-radius: 6px;
    flex-shrink: 0;
    white-space: nowrap;
}
.wc-tx-chip-bank   { background: #eff6ff; color: #2563eb; }
.wc-tx-chip-mobile { background: #faf5ff; color: #7c3aed; }

.wc-tx-row-amount {
    font-size: 14px;
    font-weight: 800;
    color: #1e293b;
    white-space: nowrap;
    font-variant-numeric: tabular-nums;
    flex-shrink: 0;
}
.wc-tx-pending  .wc-tx-row-amount { color: #b45309; }
.wc-tx-approved .wc-tx-row-amount { color: #047857; }
.wc-tx-rejected .wc-tx-row-amount { color: #b91c1c; }

.wc-tx-row-sub {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 6px;
}
.wc-tx-row-id, .wc-tx-row-date {
    font-size: 11px;
    color: #94a3b8;
    font-weight: 500;
}
.wc-tx-row-id i, .wc-tx-row-date i { font-size: 9px; margin-right: 2px; }

.wc-tx-row.is-hidden { display: none; }

@media (max-width: 640px) {
    .wc-tx-inline-stats { gap: 4px; padding: 10px 14px; }
    .wc-tx-is-sep { margin: 0 8px; }
    .wc-tx-is-label { display: none; }
    .wc-tx-row-main { flex-direction: column; align-items: flex-start; gap: 6px; }
    .wc-tx-filter-bar { flex-direction: column; }
}
</style>
@endpush

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
<script>
"use strict";
(function () {
    var input   = document.getElementById('wcTxLive');
    var rows    = Array.prototype.slice.call(document.querySelectorAll('.wc-tx-row'));

    function norm(s) { return (s || '').toLowerCase().replace(/\s+/g, ' ').trim(); }

    if (input) {
        input.addEventListener('input', function () {
            var term = norm(input.value);
            rows.forEach(function (r) {
                var show = term === '' || norm(r.dataset.search).indexOf(term) !== -1;
                r.classList.toggle('is-hidden', !show);
            });
        });
    }
})();
</script>
@endpush
