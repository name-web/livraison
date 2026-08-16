@extends('backend.partials.master')
@section('title')
    {{ __('parcel.parcel_bank') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">
                <i class="fas fa-university text-[18px] mr-2 text-wc-primary"></i>
                {{ __('parcel.parcel_bank') }}
            </h1>
            <p class="wc-page-subtitle">
                {{ __('Showing') }} {{ $parcels->firstItem() ?? 0 }} {{ __('to') }} {{ $parcels->lastItem() ?? 0 }} {{ __('of') }} {{ $parcels->total() }} {{ __('results') }} · {{ settings()->currency }}
            </p>
        </div>
    </div>

    <div class="wc-card">
        @if(count($parcels) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-university"></i></div>
                <p class="wc-empty-title">{{ __('parcel.parcel_bank') }}</p>
                <p class="wc-empty-description">Aucun colis bancaire pour le moment.</p>
            </div>
        @else
            <div class="wc-table-wrap">
                <table class="wc-table">
                    <thead>
                        <tr>
                            <th>{{ __('###') }}</th>
                            <th>{{ __('parcel.tracking_id') }}</th>
                            <th>{{ __('parcel.recipient_info') }}</th>
                            <th class="text-right">{{ __('parcel.amount') }}</th>
                            <th>{{ __('parcel.status') }}</th>
                            <th>{{ __('parcel.payment') }}</th>
                            <th class="text-right">{{ __('levels.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=0; @endphp
                        @foreach($parcels as $parcel)
                            <tr>
                                <td class="text-wc-muted-2 wc-tabular">{{ ++$i }}</td>
                                <td class="text-wc-ink font-bold text-[13px]">{{ $parcel->tracking_id }}</td>
                                <td>
                                    <div class="space-y-1 min-w-[220px]">
                                        <div class="flex items-center gap-2 text-[12.5px]">
                                            <i class="fa fa-user text-wc-muted-2 w-[14px]"></i>
                                            <span class="text-wc-ink font-semibold">{{$parcel->customer_name}}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-[12.5px]">
                                            <i class="fas fa-phone text-wc-muted-2 w-[14px]"></i>
                                            <span class="text-wc-ink-2">{{$parcel->customer_phone}}</span>
                                        </div>
                                        <div class="flex items-start gap-2 text-[12.5px]">
                                            <i class="fas fa-map-marker-alt text-wc-muted-2 w-[14px] mt-0.5"></i>
                                            <span class="text-wc-muted-2">{{$parcel->customer_address}}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="space-y-0.5 text-[12.5px] whitespace-nowrap text-right">
                                        <div>{{__('levels.cod')}}: <span class="text-wc-ink font-semibold">{{ formatPrice($parcel->cash_collection) }}</span></div>
                                        <div>{{__('levels.total_delivery_amount')}}: <span class="text-wc-ink font-semibold">{{ formatPrice($parcel->total_delivery_amount) }}</span></div>
                                        <div>{{__('levels.vat_amount')}}: <span class="text-wc-ink font-semibold">{{ formatPrice($parcel->vat_amount) }}</span></div>
                                        <div>{{__('levels.current_payable')}}: <b class="text-wc-primary">{{ formatPrice($parcel->current_payable) }}</b></div>
                                    </div>
                                </td>
                                <td>
                                    <div>{!! $parcel->parcel_status !!}</div>
                                    <span class="text-[11px] text-wc-muted-2">{{__('parcel.updated_on')}}: {{ \Carbon\Carbon::parse($parcel->updated_at)->format('Y-m-d h:i:s A') }}</span>
                                </td>
                                <td>
                                    @php
                                        if($parcel->parcel_invoice !==null && $parcel->parcel_invoice->status == App\Enums\InvoiceStatus::PAID):
                                            $status  = $parcel->parcel_invoice->status;
                                        elseif($parcel->parcel_invoice !==null && $parcel->parcel_invoice->status == App\Enums\InvoiceStatus::UNPAID):
                                            $status  = App\Enums\InvoiceStatus::UNPAID;
                                        elseif($parcel->parcel_invoice !==null):
                                            if($parcel->status == App\Enums\ParcelStatus::DELIVERED || $parcel->status == App\Enums\ParcelStatus::PARTIAL_DELIVERED):
                                                $status  = App\Enums\InvoiceStatus::PROCESSING;
                                            else:
                                                $status  = App\Enums\InvoiceStatus::UNPAID;
                                            endif;
                                        else:
                                            $status  = App\Enums\InvoiceStatus::UNPAID;
                                        endif;
                                    @endphp
                                    <p class="mb-0.5">{{ __('invoice.'.$status) }}</p>
                                    <span class="text-[12px] text-wc-muted-2">
                                        {{ @$parcel->parcel_invoice->invoice_id }}<br/>
                                        @if ($parcel->parcel_invoice !==null && $parcel->parcel_invoice->status == App\Enums\InvoiceStatus::PAID)
                                            Paid At: {{ @dateFormat($parcel->parcel_invoice->updated_at) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <a href="{{ route('merchant-parcel.clone',$parcel->id) }}" class="wc-btn wc-btn-outline wc-btn-sm" title="{{__('levels.clone')}}"><i class="fas fa-clone" aria-hidden="true"></i> {{__('levels.clone')}}</a>
                                        @if ($parcel->status == App\Enums\ParcelStatus::PENDING)
                                            <a href="{{route('merchant-panel.parcel.edit',$parcel->id)}}" class="wc-btn wc-btn-outline wc-btn-sm" title="{{__('levels.edit')}}"><i class="fas fa-edit" aria-hidden="true"></i> {{__('levels.edit')}}</a>
                                            <form id="delete" value="Test" action="{{route('merchant-panel.parcel.delete',$parcel->id)}}" method="POST" data-title="{{ __('delete.parcel') }}">
                                                @method('DELETE')
                                                @csrf
                                                <input type="hidden" name="" value="Parcel" id="deleteTitle">
                                                <button type="submit" class="wc-btn wc-btn-danger-soft wc-btn-sm" title="{{ __('levels.delete') }}"><i class="fa fa-trash" aria-hidden="true"></i> {{ __('levels.delete') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $parcels->firstItem() }}</span>
                    {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $parcels->lastItem() }}</span>
                    {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $parcels->total() }}</span> {!! __('results') !!}
                </p>
                <span class="flex items-center gap-1">{{ $parcels->links() }}</span>
            </div>
        @endif
    </div>
</div>
@endsection()
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
    <script>
        var dateParcel = '{{ $request->parcel_date }}';
    </script>
    <script src="{{ static_asset('backend/js/merchant_panel/parcel/filter.js') }}"></script>
@endpush