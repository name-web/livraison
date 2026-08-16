@extends('backend.partials.master')
@section('title')
{{ __('menus.invoice') }} {{ __('levels.details') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('menus.invoice') }} · {{ @$invoice->invoice_id }}</h1>
            <p class="wc-page-subtitle">
                {{ __('Showing') }} {{ $invoiceParcels->firstItem() ?? 0 }} {{ __('to') }} {{ $invoiceParcels->lastItem() ?? 0 }} {{ __('of') }} {{ $invoiceParcels->total() }} {{ __('results') }}
            </p>
        </div>
        <div class="wc-toolbar">
            <a href="{{ route('merchant.panel.invoice.csv',[$invoice->merchant_id,$invoice->invoice_id]) }}" class="wc-btn wc-btn-outline wc-btn-sm"><i class="fa fa-download"></i> CSV</a>
        </div>
    </div>

    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('levels.details') }} · {{ @$invoice->invoice_id }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Détail des colis facturés.</p>
                </div>
            </div>
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
                    <tbody>
                        @php $i=0; @endphp
                        @foreach ($invoiceParcels as $invoiceParcel)
                            <tr>
                                <td class="text-wc-muted-2 wc-tabular">{{++$i}}</td>
                                <td class="text-wc-muted-2 whitespace-nowrap">{{\Carbon\Carbon::parse($invoiceParcel->updated_at)->format('d-m-Y')}}</td>
                                <td class="text-wc-ink font-bold text-[13px]">{{@$invoiceParcel->parcel->invoice_no}}</td>
                                <td class="wc-tabular">{{@$invoiceParcel->parcel->tracking_id}}</td>
                                <td>
                                    @if($invoiceParcel->parcel_status == \App\Enums\ParcelStatus::RETURN_TO_COURIER)
                                        <span class="wc-badge wc-badge-info mb-1">{{ trans("parcelStatus.24") }}</span>
                                    @endif
                                    @if($invoiceParcel->parcel->partial_delivered == \App\Enums\BooleanStatus::YES)
                                        <span class="wc-badge wc-badge-success mt-1">{{ trans("parcelStatus.".\App\Enums\ParcelStatus::PARTIAL_DELIVERED) }}</span>
                                    @else
                                        @if($invoiceParcel->parcel->status != \App\Enums\ParcelStatus::RETURN_TO_COURIER)
                                            {!! @$invoiceParcel->parcel->parcel_status !!}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="font-bold text-wc-ink text-[13px]">{{@$invoiceParcel->parcel->customer_name}}</div>
                                    <div class="text-[12px] text-wc-muted">{{ @$invoiceParcel->parcel->customer_phone }}</div>
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