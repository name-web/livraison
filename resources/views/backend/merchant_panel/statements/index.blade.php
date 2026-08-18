@extends('backend.partials.master')
@section('title')
    {{ __('menus.statements') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Header --}}
    <div class="wc-page-header animate-wcFadeUp">
        <div>
            <h1 class="wc-page-title">{{ __('menus.statements') }}</h1>
            <p class="wc-page-subtitle">Journal comptable — revenus et dépenses côte à côte.</p>
        </div>
    </div>

    {{-- Résumé global --}}
    <div class="wc-jr-summary animate-wcFadeUp" style="animation-delay:.03s">
        <div class="wc-jr-sum-block wc-jr-sum-in">
            <span class="wc-jr-sum-label">Total revenus</span>
            <span class="wc-jr-sum-val">+ {{ formatPrice($stats['income']) }}</span>
        </div>
        <div class="wc-jr-sum-divider"></div>
        <div class="wc-jr-sum-block wc-jr-sum-out">
            <span class="wc-jr-sum-label">Total dépenses</span>
            <span class="wc-jr-sum-val">- {{ formatPrice($stats['expense']) }}</span>
        </div>
        <div class="wc-jr-sum-divider"></div>
        <div class="wc-jr-sum-block wc-jr-sum-net">
            <span class="wc-jr-sum-label">Solde net</span>
            <span class="wc-jr-sum-val">{{ formatPrice($stats['net']) }}</span>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="wc-jr-filter animate-wcFadeUp" style="animation-delay:.06s">
        <form action="{{ route('merchant.accounts.statements.filter') }}" method="POST" class="flex items-center gap-2 flex-wrap flex-1 m-0">
            @csrf
            <div class="relative flex-1 min-w-[180px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[12px] pointer-events-none"></i>
                <input type="text" id="wcJrLive" class="wc-input !pl-9 !py-2 !text-[12.5px]" placeholder="Recherche...">
            </div>
            <input type="text" autocomplete="off" name="date" class="wc-input date_range_picker !py-2 !text-[12.5px] min-w-[160px]" value="{{ old('date', $request->date ?? '') }}" placeholder="Période">
            <select name="type" class="wc-select !py-2 !text-[12.5px] min-w-[120px]">
                <option value="">Tous types</option>
                <option value="{{ \App\Enums\AccountHeads::INCOME }}" {{ (old('type', $request->type ?? '') == \App\Enums\AccountHeads::INCOME) ? 'selected' : '' }}>{{ __('AccountHeads.1') }}</option>
                <option value="{{ \App\Enums\AccountHeads::EXPENSE }}" {{ (old('type', $request->type ?? '') == \App\Enums\AccountHeads::EXPENSE) ? 'selected' : '' }}>{{ __('AccountHeads.2') }}</option>
            </select>
            <input type="text" name="parcel_tracking_id" class="wc-input !py-2 !text-[12.5px] min-w-[150px]" value="{{ old('parcel_tracking_id', $request->parcel_tracking_id ?? '') }}" placeholder="Tracking ID">
            <button type="submit" class="wc-btn wc-btn-primary wc-btn-sm !py-2"><i class="fas fa-filter text-[10px]"></i></button>
            <a href="{{ route('merchant.accounts.statements.index') }}" class="wc-btn wc-btn-outline wc-btn-sm !py-2"><i class="fas fa-times text-[10px]"></i></a>
        </form>
    </div>

    {{-- Journal --}}
    @if($statements->isEmpty())
        <div class="wc-card">
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-book-open"></i></div>
                <p class="wc-empty-title">Aucun mouvement</p>
            </div>
        </div>
    @else
        @php
            $grouped = $statements->groupBy(function ($s) {
                return \Carbon\Carbon::parse($s->date ?? $s->created_at)->format('Y-m-d');
            });
        @endphp

        <div class="wc-jr-journal animate-wcFadeUp" style="animation-delay:.09s">
            @foreach($grouped as $dateKey => $dayStatements)
                @php
                    $dayDate   = \Carbon\Carbon::parse($dateKey);
                    $dayIncome = $dayStatements->where('type', \App\Enums\AccountHeads::INCOME)->sum('amount');
                    $dayExpense= $dayStatements->where('type', \App\Enums\AccountHeads::EXPENSE)->sum('amount');
                    $dayNet    = $dayIncome - $dayExpense;
                @endphp

                {{-- En-tête jour --}}
                <div class="wc-jr-day-head">
                    <div class="wc-jr-day-date">
                        <i class="fas fa-calendar-day"></i>
                        <span>{{ $dayDate->format('l d M Y') }}</span>
                    </div>
                    <div class="wc-jr-day-totals">
                        <span class="wc-jr-day-in">+ {{ formatPrice($dayIncome) }}</span>
                        <span class="wc-jr-day-out">- {{ formatPrice($dayExpense) }}</span>
                        <span class="wc-jr-day-net {{ $dayNet >= 0 ? 'wc-jr-day-net-pos' : 'wc-jr-day-net-neg' }}">{{ formatPrice($dayNet) }}</span>
                    </div>
                </div>

                {{-- Lignes du jour --}}
                <div class="wc-jr-day-body">
                    @foreach($dayStatements as $s)
                        @php
                            $isIncome = (int) $s->type === \App\Enums\AccountHeads::INCOME;
                            $parcel   = $s->parcel ?? null;
                            $trackId  = $parcel?->tracking_id ?? null;
                            $searchText = e(($s->note ?? '').' '.($trackId ?? '').' '.formatPrice($s->amount).($isIncome ? ' revenu income' : ' dépense expense'));
                        @endphp
                        <div class="wc-jr-row {{ $isIncome ? 'wc-jr-in' : 'wc-jr-out' }}" data-search="{{ $searchText }}">
                            {{-- Colonne gauche: si revenu = contenu, si dépense = vide --}}
                            @if($isIncome)
                                <div class="wc-jr-cell wc-jr-cell-content">
                                    <span class="wc-jr-cell-note">{{ $s->note }}</span>
                                    @if($trackId)
                                        <span class="wc-jr-cell-track"><i class="fas fa-truck"></i> {{ $trackId }}</span>
                                    @endif
                                </div>
                                <div class="wc-jr-cell wc-jr-cell-amt wc-jr-cell-amt-in">
                                    + {{ formatPrice($s->amount) }}
                                </div>
                            @else
                                <div class="wc-jr-cell wc-jr-cell-amt wc-jr-cell-amt-out">
                                    - {{ formatPrice($s->amount) }}
                                </div>
                                <div class="wc-jr-cell wc-jr-cell-content wc-jr-cell-content-right">
                                    <span class="wc-jr-cell-note">{{ $s->note }}</span>
                                    @if($trackId)
                                        <span class="wc-jr-cell-track"><i class="fas fa-truck"></i> {{ $trackId }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex items-center justify-between gap-3 flex-wrap mt-4 px-4 py-3 bg-white rounded-[14px] border border-wc-border animate-wcFadeUp" style="animation-delay:.12s">
            <p class="m-0 text-[12.5px] text-wc-muted">
                {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $statements->firstItem() }}</span>
                {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $statements->lastItem() }}</span>
                {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $statements->total() }}</span> {!! __('results') !!}
            </p>
            <span class="flex items-center gap-1">{{ $statements->links() }}</span>
        </div>
    @endif
</div>
@endsection()

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
/* ─── Résumé ─── */
.wc-jr-summary {
    display: flex;
    align-items: center;
    background: #fff;
    border: 1px solid #e7ebe9;
    border-radius: 14px;
    padding: 16px 24px;
    margin-bottom: 14px;
    gap: 0;
}
.wc-jr-sum-block {
    flex: 1;
    text-align: center;
}
.wc-jr-sum-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #94a3b8;
    margin-bottom: 4px;
}
.wc-jr-sum-val {
    display: block;
    font-size: 17px;
    font-weight: 800;
    font-variant-numeric: tabular-nums;
}
.wc-jr-sum-in .wc-jr-sum-val  { color: #047857; }
.wc-jr-sum-out .wc-jr-sum-val { color: #b91c1c; }
.wc-jr-sum-net .wc-jr-sum-val { color: #1e40af; }
.wc-jr-sum-divider {
    width: 1px;
    height: 36px;
    background: #e2e8f0;
    flex-shrink: 0;
}

/* ─── Filter ─── */
.wc-jr-filter {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid #e7ebe9;
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 18px;
}

/* ─── Journal ─── */
.wc-jr-journal {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* ─── En-tête jour ─── */
.wc-jr-day-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 2px solid #e2e8f0;
    flex-wrap: wrap;
}
.wc-jr-day-date {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 800;
    color: #334155;
}
.wc-jr-day-date i {
    font-size: 13px;
    color: #64748b;
}
.wc-jr-day-totals {
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 12px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}
.wc-jr-day-in   { color: #047857; }
.wc-jr-day-out  { color: #b91c1c; }
.wc-jr-day-net  { color: #64748b; padding-left: 10px; border-left: 1px solid #e2e8f0; }
.wc-jr-day-net-pos { color: #047857; }
.wc-jr-day-net-neg { color: #b91c1c; }

/* ─── Lignes ─── */
.wc-jr-day-body {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.wc-jr-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    align-items: stretch;
    transition: background .1s ease;
}
.wc-jr-row:hover {
    background: #f8fafb;
    border-radius: 8px;
}

.wc-jr-cell {
    padding: 10px 14px;
    border-radius: 8px;
}

/* ─── Colonne Revenu (gauche) ─── */
.wc-jr-in .wc-jr-cell-content {
    background: #f0fdf4;
    border-left: 3px solid #10b981;
}
.wc-jr-in .wc-jr-cell-amt-in {
    text-align: right;
    font-size: 14px;
    font-weight: 800;
    color: #047857;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    font-variant-numeric: tabular-nums;
}

/* ─── Colonne Dépense (droite) ─── */
.wc-jr-out .wc-jr-cell-amt-out {
    text-align: left;
    font-size: 14px;
    font-weight: 800;
    color: #b91c1c;
    display: flex;
    align-items: center;
    font-variant-numeric: tabular-nums;
}
.wc-jr-out .wc-jr-cell-content {
    background: #fef2f2;
    border-right: 3px solid #ef4444;
}
.wc-jr-out .wc-jr-cell-content-right {
    text-align: right;
}

/* ─── Contenu cellule ─── */
.wc-jr-cell-note {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.35;
}
.wc-jr-cell-track {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10.5px;
    font-weight: 600;
    color: #64748b;
    margin-top: 3px;
}
.wc-jr-cell-track i { font-size: 9px; color: #94a3b8; }

.wc-jr-row.is-hidden { display: none; }

@media (max-width: 640px) {
    .wc-jr-summary { flex-direction: column; gap: 10px; padding: 14px; }
    .wc-jr-sum-divider { width: 100%; height: 1px; }
    .wc-jr-filter { flex-direction: column; }
    .wc-jr-row { grid-template-columns: 1fr; gap: 4px; }
    .wc-jr-out .wc-jr-cell-content-right { text-align: left; }
    .wc-jr-out .wc-jr-cell-amt-out { justify-content: flex-start; }
}
</style>
@endpush

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
<script>
"use strict";
(function () {
    var input = document.getElementById('wcJrLive');
    var rows  = Array.prototype.slice.call(document.querySelectorAll('.wc-jr-row'));

    function norm(s) { return (s || '').toLowerCase().replace(/\s+/g, ' ').trim(); }

    if (input) {
        input.addEventListener('input', function () {
            var term = norm(input.value);
            rows.forEach(function (el) {
                el.classList.toggle('is-hidden', term !== '' && norm(el.dataset.search).indexOf(term) === -1);
            });
        });
    }
})();
</script>
@endpush
