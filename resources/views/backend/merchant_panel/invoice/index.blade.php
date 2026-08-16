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
                    <tbody>
                        @php $i=0; @endphp
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td class="text-wc-muted-2 wc-tabular">{{++$i}}</td>
                                <td class="text-wc-ink font-bold text-[13px]">{{@$invoice->invoice_id}}</td>
                                <td class="text-wc-muted-2 whitespace-nowrap">{{@$invoice->invoice_date}}</td>
                                <td class="text-right wc-tabular">{{ formatPrice(@$invoice->cash_collection) }}</td>
                                <td class="text-right wc-tabular">{{ formatPrice(@$invoice->total_charge) }}</td>
                                <td class="text-right wc-tabular font-bold text-wc-ink">{{ formatPrice(@$invoice->current_payable) }}</td>
                                <td>{!! $invoice->my_status !!}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('merchant.panel.invoice.details',$invoice->invoice_id) }}" class="wc-btn wc-btn-primary wc-btn-sm"><i class="fa fa-eye"></i> {{ __('levels.details') }}</a>
                                        <a href="{{ route('merchant.panel.invoice.csv',[$invoice->merchant_id,$invoice->invoice_id]) }}" class="wc-btn wc-btn-outline wc-btn-sm"><i class="fa fa-download"></i> CSV</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
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