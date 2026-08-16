@extends('backend.partials.master')
@section('title')
    {{ __('reports.title') }} {{ __('reports.parcel_total_summery') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('reports.parcel_total_summery') }}</h1>
            <p class="wc-page-subtitle">{{ __('reports.title') }} · synthèse financière en FCFA</p>
        </div>
    </div>

    {{-- Filtre --}}
    <div class="wc-filter">
        <form action="{{route('merchant.parcel.filter.total.summery')}}" method="GET" class="m-0">
            @csrf
            <div class="flex items-end gap-2 flex-wrap">
                <div class="wc-form-group m-0 flex-1 min-w-[240px] max-w-[380px]">
                    <label class="wc-label" for="date">{{ __('parcel.date') }}</label>
                    <input type="text" autocomplete="off" id="date" name="parcel_date" placeholder="{{ __('merchantPlaceholder.date') }}" class="wc-input date_range_picker" value="{{ old('parcel_date',$request->parcel_date) }}">
                    @error('parcel_date')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                </div>
                <button type="submit" class="wc-btn wc-btn-primary"><i class="fa fa-filter text-[12px]"></i> {{ __('levels.filter') }}</button>
                <a href="{{ route('parcel.total.summery.index') }}" class="wc-btn wc-btn-outline"><i class="fa fa-eraser text-[12px]"></i> {{ __('levels.clear') }}</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4">
        @if(!blank($parcelsStatus))
        <div class="wc-card">
            <div class="wc-card-header !min-h-[54px] !py-3">
                <div class="flex items-center gap-2.5">
                    <div class="wc-card-icon !w-9 !h-9 bg-wc-primary-soft text-wc-primary"><i class="fas fa-box-open"></i></div>
                    <h3 class="wc-card-title">{{__('parcel.title')}} {{ __('levels.status') }}</h3>
                </div>
            </div>
            <div class="p-4 space-y-2">
                @foreach($parcelsStatus as $key=>$parcelCount)
                    <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                        <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ trans("parcelStatus." . $key) }}</span>
                        <span class="wc-badge wc-badge-neutral wc-tabular">{{ $parcelCount->count() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!blank($parcels))
        <div class="wc-card">
            <div class="wc-card-header !min-h-[54px] !py-3">
                <div class="flex items-center gap-2.5">
                    <div class="wc-card-icon !w-9 !h-9 bg-wc-primary-soft text-wc-primary"><i class="fas fa-chart-line"></i></div>
                    <h3 class="wc-card-title">{{__('reports.profit_info')}}</h3>
                </div>
            </div>
            <div class="p-4 space-y-2">
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.Total_Delivery_Charge') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($parcelProfit['totalDeliveryCharge']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.COD_Charge') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($parcelProfit['totalCOD']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.Total_Vat') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($parcelProfit['totalVat']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.F./L.Charge') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($parcelProfit['totalLiquidFragileAmount']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.P.Charge') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($parcelProfit['packagingAmount']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-2">
                    <span class="text-[12.5px] font-extrabold text-wc-ink">{{ __('reports.Total_Profit') }}</span>
                    <span class="text-[14px] font-extrabold text-wc-success wc-tabular">{{ formatPrice($parcelsTotal['totalCashCollection'] - $parcelsTotal['totalSellingPrice']) }}</span>
                </div>
            </div>
        </div>

        <div class="wc-card">
            <div class="wc-card-header !min-h-[54px] !py-3">
                <div class="flex items-center gap-2.5">
                    <div class="wc-card-icon !w-9 !h-9 bg-wc-primary-soft text-wc-primary"><i class="fas fa-hand-holding-usd"></i></div>
                    <h3 class="wc-card-title">{{__('reports.Cash_Collection_Info')}}</h3>
                </div>
            </div>
            <div class="p-4 space-y-2">
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('dashboard.total_cash_collection') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($parcelsTotal['totalCashCollection']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('dashboard.total_selling_price') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($parcelsTotal['totalSellingPrice']) }}</span>
                </div>
            </div>
        </div>

        <div class="wc-card">
            <div class="wc-card-header !min-h-[54px] !py-3">
                <div class="flex items-center gap-2.5">
                    <div class="wc-card-icon !w-9 !h-9 bg-wc-primary-soft text-wc-primary"><i class="fas fa-money-bill-wave"></i></div>
                    <h3 class="wc-card-title">{{__('reports.Payable_to_Merchant')}}</h3>
                </div>
            </div>
            <div class="p-4 space-y-2">
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.Total_payable_merchant(COD)') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($parcelsTotal['totalPaybleAmount']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.Total_paid_to_merchant(with Pending)') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice(0) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.Total_paid_by_Merchant') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($merchantTotalPayment['paidAmount']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.Total_Delivery_Charge(Including VAT)') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($parcelProfit['totalDeliveryChargeVat']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-2">
                    <span class="text-[12.5px] font-extrabold text-wc-ink">{{ __('reports.Pending_Payments') }}</span>
                    <span class="text-[14px] font-extrabold text-wc-warning wc-tabular">{{ formatPrice($merchantTotalPayment['pendingAmount']) }}</span>
                </div>
            </div>
        </div>

        <div class="wc-card lg:col-span-2 xl:col-span-4">
            <div class="wc-card-header !min-h-[54px] !py-3">
                <div class="flex items-center gap-2.5">
                    <div class="wc-card-icon !w-9 !h-9 bg-wc-primary-soft text-wc-primary"><i class="fas fa-landmark"></i></div>
                    <h3 class="wc-card-title">{{__('reports.Bank_Cash_Info')}}</h3>
                </div>
            </div>
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-2">
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.total_paid_to_merchant') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($merchantTotalPayment['paidAmount']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.Pending_Payments') }}</span>
                    <span class="text-[13px] font-bold text-wc-warning wc-tabular">{{ formatPrice($merchantTotalPayment['pendingAmount']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-semibold text-wc-ink-2">{{ __('reports.Total_bank_opening_balance') }}</span>
                    <span class="text-[13px] font-bold text-wc-ink wc-tabular">{{ formatPrice($parcelsTotal['totalBankOpeningBalance']) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 py-1.5 border-b border-wc-border last:border-0">
                    <span class="text-[12.5px] font-extrabold text-wc-ink">{{ __('reports.Current_Cash_Balance') }}</span>
                    <span class="text-[14px] font-extrabold text-wc-success wc-tabular">{{ formatPrice($parcelsTotal['totalBankBalance']) }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection()

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
    <script>
        var merchantUrl = '{{ route('parcel.merchant.get') }}';
        var merchantID = '{{ $request->parcel_merchant_id }}';
        var deliveryManID = '{{ $request->parcel_deliveryman_id }}';
        var pickupManID = '{{ $request->parcel_pickupman_id }}';
        var dateParcel = '{{ $request->parcel_date }}';
    </script>
    <script src="{{ static_asset('backend/js/parcel/filter.js') }}"></script>
    <script src="{{ static_asset('backend/js/reports/print.js') }}"></script>
    <script src="{{ static_asset('backend/js/reports/jquery.table2excel.min.js') }}"></script>
    <script src="{{ static_asset('backend/js/reports/reports.js') }}"></script>
@endpush