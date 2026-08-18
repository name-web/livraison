@extends('backend.partials.master')
@section('title')
    {{ __('menus.invoice') }} {{ __('levels.details') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    @php
        $statusKey = (int) @$invoice->status;
        $badgeClass = match($statusKey) {
            \App\Enums\InvoiceStatus::PAID => 'wc-badge-success',
            \App\Enums\InvoiceStatus::UNPAID => 'wc-badge-error',
            default => 'wc-badge-warning',
        };
        $statusText = trans('invoice.'.$statusKey);
    @endphp

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('menus.invoice') }} · {{ @$invoice->invoice_id }}</h1>
            <p class="wc-page-subtitle">
                <span class="wc-badge {{ $badgeClass }} mr-2"><i class="fas fa-circle text-[6px] mr-1.5"></i>{{ $statusText }}</span>
                {{ __('levels.date') }} : {{ @$invoice->invoice_date }}
            </p>
        </div>
        <div class="wc-toolbar">
            <a href="{{ route('merchant.panel.invoice.index') }}" class="wc-btn wc-btn-outline wc-btn-sm"><i class="fas fa-arrow-left"></i> {{ __('levels.back') }}</a>
            <a href="{{ route('merchant.panel.invoice.csv',[$invoice->merchant_id,$invoice->invoice_id]) }}" class="wc-btn wc-btn-primary wc-btn-sm"><i class="fa fa-download"></i> CSV</a>
        </div>
    </div>

    {{-- KPI : résumé de la facture --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.02s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('levels.total') }}</p>
                <div class="wc-card-icon bg-[#eef1f5] text-[#334155]"><i class="fas fa-box"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ @$summary->total_parcels ?? 0 }}</p>
            <p class="wc-kpi-sub neutral m-0">colis facturés</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.06s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('parcel.cash_collection') }}</p>
                <div class="wc-card-icon bg-[#ecfdf5] text-[#059669]"><i class="fas fa-hand-holding-usd"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ formatPrice(@$summary->collection) }}</p>
            <p class="wc-kpi-sub neutral m-0">encaissé</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.1s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('parcel.Total_Charge') }}</p>
                <div class="wc-card-icon bg-[#fffbeb] text-[#d97706]"><i class="fas fa-receipt"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ formatPrice(@$summary->charges) }}</p>
            <p class="wc-kpi-sub neutral m-0">livraison + TVA + retours</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.14s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('invoice.paid_out') }}</p>
                <div class="wc-card-icon bg-[#f5f3ff] text-[#7c3aed]"><i class="fas fa-coins"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ formatPrice(@$summary->payable) }}</p>
            <p class="wc-kpi-sub neutral m-0">{{ __('parcel.current_payable') }}</p>
        </div>
    </div>

    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-box-open"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('levels.details') }} · {{ @$invoice->invoice_id }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Détail des colis facturés.</p>
                </div>
            </div>
        </div>

        {{-- Barre d'outils interactive : recherche --}}
        <div class="flex items-center gap-3 flex-wrap px-4 py-3 border-b border-wc-border bg-[#f8fafc]">
            <div class="relative flex-1 min-w-[220px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[13px] pointer-events-none"></i>
                <input type="text" id="wcInvParcelSearch" class="wc-input !pl-9" placeholder="Rechercher un colis (tracking, client, téléphone)...">
            </div>
            <span class="text-[12.5px] text-wc-muted wc-tabular whitespace-nowrap" id="wcInvParcelCounter"></span>
        </div>

        @if(count($invoiceParcels) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-box-open"></i></div>
                <p class="wc-empty-title">Aucun colis sur cette facture</p>
            </div>
        @else
            <div class="wc-table-wrap">
                <table class="wc-table">
                    <thead>
                        <tr>
                            <th>{{ __('levels.id') }}</th>
                            <th>{{ __('menus.date') }}</th>
                            <th>{{ __('invoice.invoice') }}</th>
                            <th>{{ __('levels.track_id') }}</th>
                            <th>{{ __('levels.status') }}</th>
                            <th>{{ __('parcel.customer_info') }}</th>
                            <th class="text-right">{{ __('parcel.cash_collection') }}</th>
                            <th class="text-right">{{ __('parcel.delivery_charge') }}</th>
                            <th class="text-right">{{ __('Return Charge') }}</th>
                            <th class="text-right">{{ __('parcel.cod_charges') }}</th>
                            <th class="text-right">{{ __('parcel.vat') }}</th>
                            <th class="text-right">{{ __('parcel.Total_Charge') }}</th>
                            <th class="text-right">{{ __('invoice.paid_out') }}</th>
                        </tr>
                    </thead>
                    <tbody id="wcInvParcelBody">
                        @php $i=0; @endphp
                        @foreach ($invoiceParcels as $invoiceParcel)
                            @php
                                $parcel = $invoiceParcel->parcel;
                                $initials = strtoupper(mb_substr(trim(@$parcel->customer_name), 0, 2));
                            @endphp
                            <tr class="animate-wcRowIn wc-inv-parcel-row" style="animation-delay: {{ $loop->iteration * 0.02 }}s"
                                data-search="{{ mb_strtolower(@$parcel->tracking_id.' '.@$parcel->invoice_no.' '.@$parcel->customer_name.' '.@$parcel->customer_phone) }}">
                                <td class="text-wc-muted-2 wc-tabular">{{++$i}}</td>
                                <td class="text-wc-muted-2 whitespace-nowrap">{{\Carbon\Carbon::parse($invoiceParcel->updated_at)->format('d-m-Y')}}</td>
                                <td class="text-wc-ink font-bold text-[13px]">{{@$parcel->invoice_no}}</td>
                                <td class="wc-tabular">{{@$parcel->tracking_id}}</td>
                                <td>
                                    @if($invoiceParcel->parcel_status == \App\Enums\ParcelStatus::RETURN_TO_COURIER)
                                        <span class="wc-badge wc-badge-info mb-1">{{ trans("parcelStatus.24") }}</span>
                                    @endif
                                    @if(@$parcel->partial_delivered == \App\Enums\BooleanStatus::YES)
                                        <span class="wc-badge wc-badge-success mt-1">{{ trans("parcelStatus.".\App\Enums\ParcelStatus::PARTIAL_DELIVERED) }}</span>
                                    @else
                                        @if(@$parcel->status != \App\Enums\ParcelStatus::RETURN_TO_COURIER)
                                            {!! @$parcel->parcel_status !!}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="wc-avatar !bg-[#eef1f5] !text-[#64748b]">{{ $initials ?: '—' }}</div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-wc-ink text-[13px]">{{@$parcel->customer_name}}</div>
                                            <div class="text-[12px] text-wc-muted-2 wc-tabular">{{ @$parcel->customer_phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right wc-tabular">{{ formatPrice(@$invoiceParcel->collected_amount) }}</td>
                                <td class="text-right wc-tabular">{{ formatPrice(@$invoiceParcel->total_delivery_amount) }}</td>
                                <td class="text-right wc-tabular">{{ formatPrice(@$invoiceParcel->return_charge) }}</td>
                                <td class="text-right wc-tabular">{{ formatPrice(@$invoiceParcel->cod_amount) }}</td>
                                <td class="text-right wc-tabular">{{ formatPrice(@$invoiceParcel->vat_amount) }}</td>
                                <td class="text-right wc-tabular">{{ formatPrice(@$invoiceParcel->total_charge_amount) }}</td>
                                <td class="text-right wc-tabular font-bold text-wc-ink">{{ formatPrice(@$invoiceParcel->current_payable) }}</td>
                            </tr>
                        @endforeach
                        <tr id="wcInvParcelNoResult" class="d-none">
                            <td colspan="13">
                                <div class="wc-empty !py-10">
                                    <div class="wc-empty-icon"><i class="fas fa-filter"></i></div>
                                    <p class="wc-empty-title">Aucun résultat</p>
                                    <p class="wc-empty-description">Aucun colis ne correspond à votre recherche.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $invoiceParcels->firstItem() }}</span>
                    {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $invoiceParcels->lastItem() }}</span>
                    {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $invoiceParcels->total() }}</span> {!! __('results') !!}
                </p>
                <span class="flex items-center gap-1">{{ $invoiceParcels->links() }}</span>
            </div>
        @endif
    </div>
</div>
@endsection()

@push('scripts')
<script type="text/javascript">
"use strict";
(function () {
    const rows = Array.prototype.slice.call(document.querySelectorAll('.wc-inv-parcel-row'));
    const searchInput = document.getElementById('wcInvParcelSearch');
    const counter = document.getElementById('wcInvParcelCounter');
    const noResult = document.getElementById('wcInvParcelNoResult');

    function normalize(s) {
        return (s || '').toString().toLowerCase().replace(/\s+/g, ' ').trim();
    }

    function applyFilters() {
        const searchTerm = searchInput ? normalize(searchInput.value) : '';
        let visible = 0;

        rows.forEach(function (row) {
            const matchSearch = searchTerm === '' || normalize(row.dataset.search).indexOf(searchTerm) !== -1;
            row.classList.toggle('d-none', !matchSearch);
            if (matchSearch) visible++;
        });

        if (noResult) noResult.classList.toggle('d-none', visible !== 0);
        if (counter) {
            counter.textContent = visible + ' / ' + rows.length + ' colis';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    applyFilters();
})();
</script>
@endpush