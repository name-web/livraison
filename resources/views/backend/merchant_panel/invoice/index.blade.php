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
            <p class="wc-page-subtitle">{{ $invoices->total() }} {{ __('levels.list') }} · FCFA</p>
        </div>
    </div>

    {{-- KPI : statistiques des factures --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.02s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('levels.total') }}</p>
                <div class="wc-card-icon bg-[#eef1f5] text-[#334155]"><i class="fas fa-file-invoice"></i></div>
            </div>
            <p class="wc-kpi-value m-0" id="wcInvStatTotal">{{ $stats['total'] }}</p>
            <p class="wc-kpi-sub positive m-0"><i class="fas fa-circle text-[6px]"></i> {{ $stats['paid'] }} payées</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.06s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('parcel.cash_collection') }}</p>
                <div class="wc-card-icon bg-[#ecfdf5] text-[#059669]"><i class="fas fa-hand-holding-usd"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ formatPrice($stats['collection']) }}</p>
            <p class="wc-kpi-sub neutral m-0">encaissé sur toutes les factures</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.1s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('parcel.Total_Charge') }}</p>
                <div class="wc-card-icon bg-[#fffbeb] text-[#d97706]"><i class="fas fa-receipt"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ formatPrice($stats['charges']) }}</p>
            <p class="wc-kpi-sub neutral m-0">livraison + TVA + retours</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.14s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('parcel.current_payable') }}</p>
                <div class="wc-card-icon bg-[#f5f3ff] text-[#7c3aed]"><i class="fas fa-coins"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ formatPrice($stats['payable']) }}</p>
            <p class="wc-kpi-sub neutral m-0">
                @if($stats['unpaid'] > 0)
                    <span class="text-wc-danger"><i class="fas fa-circle text-[6px]"></i> {{ $stats['unpaid'] }} non payée(s)</span>
                @else
                    <span class="text-wc-success"><i class="fas fa-circle text-[6px]"></i> tout est réglé</span>
                @endif
            </p>
        </div>
    </div>

    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('menus.invoice') }} {{ __('levels.list') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">
                        {{ __('Showing') }} {{ $invoices->firstItem() ?? 0 }} {{ __('to') }} {{ $invoices->lastItem() ?? 0 }} {{ __('of') }} {{ $invoices->total() }} {{ __('results') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Barre d'outils interactive : recherche + filtres --}}
        <div class="flex items-center gap-3 flex-wrap px-4 py-3 border-b border-wc-border bg-[#f8fafc]">
            <div class="relative flex-1 min-w-[220px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[13px] pointer-events-none"></i>
                <input type="text" id="wcInvSearch" class="wc-input !pl-9" placeholder="Rechercher une facture (n°, date, montant)...">
            </div>
            <div class="flex items-center gap-1.5" id="wcInvFilters" role="group" aria-label="Filtrer par statut">
                <button type="button" class="wc-btn wc-btn-primary wc-btn-sm wc-inv-filter" data-filter="all">{{ __('levels.total') }}</button>
                <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-inv-filter" data-filter="paid">{{ __('invoice.3') }}</button>
                <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-inv-filter" data-filter="unpaid">{{ __('invoice.0') }}</button>
                <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-inv-filter" data-filter="processing">{{ __('invoice.2') }}</button>
            </div>
            <span class="text-[12.5px] text-wc-muted wc-tabular whitespace-nowrap" id="wcInvCounter"></span>
        </div>

        @if(count($invoices) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-file-invoice"></i></div>
                <p class="wc-empty-title">Aucune facture</p>
                <p class="wc-empty-description">Vos factures générées apparaîtront ici.</p>
            </div>
        @else
            <div class="wc-table-wrap">
                <table class="wc-table">
                    <thead>
                        <tr>
                            <th>{{ __('levels.id') }}</th>
                            <th>{{ __('invoice.id') }}</th>
                            <th>{{ __('levels.date') }}</th>
                            <th class="text-right">{{ __('parcel.cash_collection') }}</th>
                            <th class="text-right">{{ __('parcel.Total_Charge') }}</th>
                            <th class="text-right">{{ __('parcel.current_payable') }}</th>
                            <th>{{ __('parcel.status') }}</th>
                            <th class="text-right">{{ __('levels.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="wcInvBody">
                        @php $i=0; @endphp
                        @foreach ($invoices as $invoice)
                            @php
                                $statusKey = (int) $invoice->status;
                                $badgeClass = match($statusKey) {
                                    \App\Enums\InvoiceStatus::PAID => 'wc-badge-success',
                                    \App\Enums\InvoiceStatus::UNPAID => 'wc-badge-error',
                                    default => 'wc-badge-warning',
                                };
                                $statusText = trans('invoice.'.$statusKey);
                            @endphp
                            <tr class="animate-wcRowIn wc-inv-row" style="animation-delay: {{ $loop->iteration * 0.03 }}s"
                                data-search="{{ mb_strtolower(@$invoice->invoice_id.' '.$invoice->invoice_date.' '.number_format((float) $invoice->current_payable, 0, ',', '')) }}"
                                data-status="{{ match($statusKey) {
                                    \App\Enums\InvoiceStatus::PAID => 'paid',
                                    \App\Enums\InvoiceStatus::UNPAID => 'unpaid',
                                    default => 'processing',
                                } }}">
                                <td class="text-wc-muted-2 wc-tabular">{{++$i}}</td>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="wc-avatar !bg-[#ecfdf5] !text-[#059669]"><i class="fas fa-file-invoice text-[13px]"></i></div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-wc-ink text-[13px]">{{@$invoice->invoice_id}}</div>
                                            <div class="text-[11.5px] text-wc-muted-2 wc-tabular">#{{ $invoice->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-wc-muted-2 whitespace-nowrap">
                                    <span class="text-[12.5px]">{{@$invoice->invoice_date}}</span>
                                </td>
                                <td class="text-right wc-tabular">{{ formatPrice(@$invoice->cash_collection) }}</td>
                                <td class="text-right wc-tabular">{{ formatPrice(@$invoice->total_charge) }}</td>
                                <td class="text-right wc-tabular font-bold text-wc-ink">{{ formatPrice(@$invoice->current_payable) }}</td>
                                <td>
                                    <span class="wc-badge {{ $badgeClass }}"><i class="fas fa-circle text-[6px] mr-1.5"></i>{{ $statusText }}</span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('merchant.panel.invoice.details',$invoice->invoice_id) }}" class="wc-btn wc-btn-primary wc-btn-sm"><i class="fa fa-eye"></i> {{ __('levels.details') }}</a>
                                        <a href="{{ route('merchant.panel.invoice.csv',[$invoice->merchant_id,$invoice->invoice_id]) }}" class="wc-btn wc-btn-outline wc-btn-sm"><i class="fa fa-download"></i> CSV</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="wcInvNoResult" class="d-none">
                            <td colspan="8">
                                <div class="wc-empty !py-10">
                                    <div class="wc-empty-icon"><i class="fas fa-filter"></i></div>
                                    <p class="wc-empty-title">Aucun résultat</p>
                                    <p class="wc-empty-description">Aucune facture ne correspond à votre recherche ou filtre.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $invoices->firstItem() }}</span>
                    {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $invoices->lastItem() }}</span>
                    {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $invoices->total() }}</span> {!! __('results') !!}
                </p>
                <span class="flex items-center gap-1">{{ $invoices->links() }}</span>
            </div>
        @endif
    </div>
</div>
@endsection()

@push('scripts')
<script type="text/javascript">
"use strict";
(function () {
    const rows = Array.prototype.slice.call(document.querySelectorAll('.wc-inv-row'));
    const body = document.getElementById('wcInvBody');
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