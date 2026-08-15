<!-- wrapper  -->

@extends('backend.partials.master')
@section('title')
    {{ __('merchant.merchant_dashboard') }}
@endsection
@section('maincontent')
    <div class="container-fluid dashboard-content">
        <!-- pageheader  -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"
                                        class="breadcrumb-link">{{ __('merchant.dashboard') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ __('merchant.merchant_dashboard') }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex justify-content-between align-items-end flex-wrap">
                        <div>
                            <h2 class="mb-1">{{ __('dashboard.hello', ['name' => $merchant->business_name]) }}</h2>
                            @if ($period)
                                <span class="badge badge-info">
                                    {{ __('dashboard.activity_period', [
                                        'from' => \Carbon\Carbon::parse($period['from'])->format('d/m/Y'),
                                        'to' => \Carbon\Carbon::parse($period['to'])->format('d/m/Y'),
                                    ]) }}
                                </span>
                            @else
                                <span class="badge badge-secondary">{{ __('dashboard.all_periods') }}</span>
                            @endif
                        </div>
                        <form action="{{ route('merchant-panel.dashboard.filter') }}" method="POST"
                            class="d-flex justify-content-end align-items-center">
                            @csrf
                            <input type="text" autocomplete="off" id="date" name="date"
                                class="input py-1 date_range_picker form-control group-input mr-2"
                                value="{{ $dateRange }}"
                                placeholder="{{ __('dashboard.date_range_placeholder') }}">
                            <button type="submit" class="btn btn-sm btn-primary group-btn"><i
                                    class="fa fa-search"></i> {{ __('dashboard.filter') }}</button>
                            @if ($period)
                                <a href="{{ route('dashboard.index') }}"
                                    class="btn btn-sm btn-outline-secondary ml-2">{{ __('dashboard.reset_filter') }}</a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end pageheader  -->

        @if ($data['counts']['total'] == 0 && $data['payments']['total'] == 0)
            {{-- etat vide nouveau marchand --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fa fa-box-open fa-3x text-primary mb-3 d-block"></i>
                            <h3 class="text-primary">{{ __('dashboard.empty_title') }}</h3>
                            <p class="text-muted">{{ __('dashboard.empty_text') }}</p>
                            <a href="{{ route('merchant-panel.parcel.create') }}"
                                class="btn btn-primary">{{ __('dashboard.empty_cta') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- apercu colis --}}
            <div class="row merchant-panel header-summery">
                <div class="col-sm-6 col-lg-4 col-xl mb-3 mb-xl-0">
                    <a href="{{ route('merchant-panel.parcel.index') }}" class="d-block">
                        <div class="card border-3 border-top border-top-primary h-100">
                            <div class="card-body">
                                <div class="d-flex">
                                    <label class="icon p-10px"><i class="fa fa-box-open text-primary"></i></label>
                                    <div class="pl-2 w-100">
                                        <h5 class="m-0 text-primary">{{ __('dashboard.total_parcel') }}</h5>
                                        <h1 class="mb-0 m-0 text-primary">{{ $data['counts']['total'] }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4 col-xl mb-3 mb-xl-0">
                    <a href="{{ route('merchant-panel.parcel.filter', array_merge($filterParams, ['parcel_status' => 'in_transit'])) }}"
                        class="d-block">
                        <div class="card border-3 border-top border-top-primary h-100">
                            <div class="card-body">
                                <div class="d-flex">
                                    <label class="icon p-10px"><i class="fa fa-dolly text-primary"></i></label>
                                    <div class="pl-2 w-100">
                                        <h5 class="m-0 text-primary">{{ __('dashboard.on_going') }}</h5>
                                        <h1 class="mb-0 m-0 text-primary">{{ $data['counts']['on_going'] }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4 col-xl mb-3 mb-xl-0">
                    <a href="{{ route('merchant-panel.parcel.filter', array_merge($filterParams, ['parcel_status' => \App\Enums\ParcelStatus::DELIVERED])) }}"
                        class="d-block">
                        <div class="card border-3 border-top border-top-primary h-100">
                            <div class="card-body">
                                <div class="d-flex">
                                    <label class="icon p-10px"><i class="fa fa-shipping-fast text-primary"></i></label>
                                    <div class="pl-2 w-100">
                                        <h5 class="m-0 text-primary">{{ __('dashboard.deliver') }}</h5>
                                        <h1 class="mb-0 m-0 text-primary">{{ $data['counts']['delivered'] }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4 col-xl mb-3 mb-xl-0">
                    <a href="{{ route('merchant-panel.parcel.filter', array_merge($filterParams, ['parcel_status' => \App\Enums\ParcelStatus::PARTIAL_DELIVERED])) }}"
                        class="d-block">
                        <div class="card border-3 border-top border-top-primary h-100">
                            <div class="card-body">
                                <div class="d-flex">
                                    <label class="icon p-10px"><i class="fa fa-hand-holding-usd text-primary"></i></label>
                                    <div class="pl-2 w-100">
                                        <h5 class="m-0 text-primary">{{ __('dashboard.partial_delivered') }}</h5>
                                        <h1 class="mb-0 m-0 text-primary">{{ $data['counts']['partial'] }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-4 col-xl mb-3 mb-xl-0">
                    <a href="{{ route('merchant-panel.parcel.filter', array_merge($filterParams, ['parcel_status' => \App\Enums\ParcelStatus::RETURN_RECEIVED_BY_MERCHANT])) }}"
                        class="d-block">
                        <div class="card border-3 border-top border-top-primary h-100">
                            <div class="card-body">
                                <div class="d-flex">
                                    <label class="icon p-10px"><i class="fa fa-dna text-primary"></i></label>
                                    <div class="pl-2 w-100">
                                        <h5 class="m-0 text-primary">{{ __('dashboard.return') }}</h5>
                                        <h1 class="mb-0 m-0 text-primary">{{ $data['counts']['returned'] }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- graphiques --}}
            <div class="row mt-3">
                <div class="col-lg-7 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary">{{ __('dashboard.activity_chart') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="apexcharts" id="apexparcels"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary">{{ __('dashboard.parcel_repartition') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="apexcharts" id="apexparcelspiechart"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- solde --}}
            <div class="row">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary">{{ __('dashboard.your_balance') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span class="font-weight-bold">{{ __('dashboard.current_balance') }}</span>
                                        <span class="text-primary font-weight-bold">{{ formatPrice($merchant->current_balance, $currency) }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span class="font-weight-bold">{{ __('dashboard.wallet_balance') }}</span>
                                        <span class="text-primary font-weight-bold">{{ formatPrice($merchant->wallet_balance, $currency) }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span class="font-weight-bold">{{ __('dashboard.opening_balance') }}</span>
                                        <span>{{ formatPrice($merchant->opening_balance, $currency) }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <span class="font-weight-bold">{{ __('dashboard.merchant_vat') }}</span>
                                        <span>{{ formatPrice($merchant->vat, $currency) }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted small mt-3 mb-0"><i class="fa fa-info-circle"></i>
                                {{ __('dashboard.wallet_balance_note') }}</p>
                        </div>
                    </div>
                </div>

                {{-- periode + paiements --}}
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-primary">{{ __('dashboard.period_overview') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span class="font-weight-bold">{{ __('dashboard.total_cash_collection') }}</span>
                                <span>{{ formatPrice($data['amounts']['cash_collection'], $currency) }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span class="font-weight-bold">{{ __('dashboard.total_selling_price') }}</span>
                                <span>{{ formatPrice($data['amounts']['selling_price'], $currency) }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span class="font-weight-bold">{{ __('dashboard.margin') }}</span>
                                <span>{{ formatPrice($data['amounts']['cash_collection'] - $data['amounts']['selling_price'], $currency) }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span class="font-weight-bold">{{ __('dashboard.delivery_fees_paid') }}</span>
                                <span>{{ formatPrice($data['sales']['delivery_fee'], $currency) }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span class="font-weight-bold">{{ __('dashboard.vat_paid') }}</span>
                                <span>{{ formatPrice($data['sales']['vat'], $currency) }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span class="font-weight-bold">{{ __('dashboard.net_balance') }}</span>
                                <span class="text-primary font-weight-bold">{{ formatPrice($data['sales']['net'], $currency) }}</span>
                            </div>
                            <hr>
                            <h6 class="text-primary">{{ __('dashboard.payments') }}</h6>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span class="font-weight-bold">{{ __('dashboard.pending_payments') }}</span>
                                <span>{{ formatPrice($data['payments']['pending'], $currency) }}</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span class="font-weight-bold">{{ __('dashboard.paid_payments') }}</span>
                                <span>{{ formatPrice($data['payments']['paid'], $currency) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span class="font-weight-bold">{{ __('dashboard.payment_requests') }}</span>
                                <span>{{ $data['payments']['total'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- compteurs rapides --}}
            <div class="row mt-3">
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('merchant-panel.shops.index') }}" class="d-block">
                        <div class="card border-3 border-top border-top-primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="text-primary mb-0">{{ __('dashboard.shop_count') }}</h5>
                                    <h3 class="text-primary mb-0">{{ $data['counts']['shops'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('merchant-panel.parcel-bank.index') }}" class="d-block">
                        <div class="card border-3 border-top border-top-primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="text-primary mb-0">{{ __('dashboard.parcel_bank_count') }}</h5>
                                    <h3 class="text-primary mb-0">{{ $data['counts']['parcel_bank'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif
    </div>
    <!-- end wrapper  -->
@endsection

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@push('scripts')
    <script type="text/javascript" src="{{ static_asset('backend/js/charts/apexcharts.js') }}"></script>
    @include('backend.merchant_panel.dashboard-chart')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript">
        var dateParcel = @json($dateRange);
    </script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}">
    </script>
@endpush