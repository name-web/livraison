@extends('backend.partials.master')
@section('title')
    {{ __('parcel.my_wallet') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('parcel.my_wallet') }}</h1>
            <p class="wc-page-subtitle">{{ __('parcel.wallet_history') }} · FCFA</p>
        </div>
        <div class="wc-toolbar">
            <a href="#" class="wc-btn wc-btn-primary modalBtn"
                data-url="{{ route('merchant-panel.my.wallet.recharge') }}"
                data-title="{{ __('parcel.recharge_wallet') }}" data-bs-toggle="modal"
                data-modalsize="modal-lg" data-bs-target="#dynamic-modal">
                <i class="fas fa-plus"></i> {{ __('parcel.recharge_wallet') }}
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="wc-filter">
        <form action="{{ route('merchant-panel.my.wallet.index') }}" method="GET" id="filter-form" class="m-0">
            <div class="wc-filter-grid">
                <div class="wc-form-group m-0">
                    <label class="wc-label" for="date">{{ __('parcel.date') }}</label>
                    <input type="text" autocomplete="off" id="date" name="date" placeholder="{{ __('merchantPlaceholder.date') }}" class="wc-input date_range_picker" value="{{ old('date', $request->date) }}">
                    @error('date')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                </div>
                <div class="wc-form-group m-0">
                    <label class="wc-label" for="parcelStatus">{{ __('parcel.status') }}</label>
                    <select id="parcelStatus" name="status" class="wc-select @error('status') is-invalid @enderror">
                        <option value="" selected>{{ __('menus.select') }} {{ __('levels.status') }}</option>
                        @foreach (trans('WalletStatus') as $key => $status)
                            <option value="{{ $key }}" {{ old('status', $request->status) == $key ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                </div>
                <div class="wc-form-group m-0">
                    <label class="wc-label" for="search">{{ __('parcel.search') }}</label>
                    <input id="search" type="text" name="search" placeholder="{{ __('parcel.search') }}" autocomplete="off" class="wc-input" value="{{ old('search', $request->search) }}">
                    @error('search')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="wc-btn wc-btn-primary"><i class="fas fa-filter text-[12px]"></i> {{ __('levels.filter') }}</button>
                    <a href="{{ route('merchant-panel.my.wallet.index') }}" class="wc-btn wc-btn-outline"><i class="fas fa-eraser text-[12px]"></i> {{ __('levels.clear') }}</a>
                </div>
            </div>
        </form>
    </div>

    {{-- KPIs wallet --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
        <div class="wc-kpi-card">
            <p class="wc-kpi-label">Solde du portefeuille</p>
            <h3 class="wc-kpi-value">{{ formatPrice(Auth::user()->merchant->wallet_balance) }}</h3>
            @if (Auth::user()->merchant->wallet_balance <= 10)
                <p class="text-[12px] font-bold text-wc-danger mt-2 mb-0">Solde faible, pensez à recharger.</p>
            @endif
        </div>
        <div class="wc-kpi-card">
            <p class="wc-kpi-label">Total recharges</p>
            <h3 class="wc-kpi-value text-wc-success">{{ formatPrice(\App\Models\Backend\Wallet::where(['user_id'=>Auth::user()->id,'type'=>App\Enums\Wallet\WalletType::INCOME])->sum('amount')) }}</h3>
        </div>
        <div class="wc-kpi-card">
            <p class="wc-kpi-label">Total déductions</p>
            <h3 class="wc-kpi-value text-wc-danger">{{ formatPrice(\App\Models\Backend\Wallet::where(['user_id'=>Auth::user()->id,'type'=>App\Enums\Wallet\WalletType::EXPENSE])->sum('amount')) }}</h3>
        </div>
        <div class="wc-kpi-card flex items-center justify-between">
            <div>
                <p class="wc-kpi-label">Demandes</p>
                <div class="flex items-center gap-3 mt-1">
                    <span class="wc-badge wc-badge-warning">{{ \App\Models\Backend\Wallet::where('user_id', Auth::user()->id)->where('status', \App\Enums\Wallet\WalletStatus::PENDING)->count() }} en attente</span>
                    <span class="wc-badge wc-badge-success">{{ \App\Models\Backend\Wallet::where('user_id', Auth::user()->id)->where('status', \App\Enums\Wallet\WalletStatus::APPROVED)->count() }} validées</span>
                    <span class="wc-badge wc-badge-error">{{ \App\Models\Backend\Wallet::where('user_id', Auth::user()->id)->where('status', \App\Enums\Wallet\WalletStatus::REJECTED)->count() }} rejetées</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Historique --}}
    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('parcel.wallet_history') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Toutes vos transactions, recharges et déductions.</p>
                </div>
            </div>
        </div>
        <div class="p-4">
            <nav>
                <div class="nav nav-tabs border-0 gap-2" id="nav-tab" role="tablist">
                    <button class="wc-btn wc-btn-soft wc-btn-sm @if(!$request->recharge_page) active @endif" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                        <i class="fas fa-list-ul text-[11px]"></i> {{ __('parcel.all_transaction') }}
                    </button>
                    <button class="wc-btn wc-btn-outline wc-btn-sm @if($request->recharge_page) active @endif" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                        <i class="fas fa-arrow-down text-[11px]"></i> {{ __('parcel.recharge') }}
                    </button>
                </div>
            </nav>
            <div class="tab-content mt-3" id="nav-tabContent">
                <div class="tab-pane fade @if(!$request->recharge_page) show active @endif" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                    @include('backend.merchant_panel.mywallet.all_transaction')
                </div>
                <div class="tab-pane fade @if($request->recharge_page) show active @endif" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                    @include('backend.merchant_panel.mywallet.recharge_transaction')
                </div>
            </div>
        </div>
    </div>
</div>
<div id="paytm-checkoutjs"></div>
@endsection

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script type="text/javascript">
        $(document).on('click', '.quick-amount', function() {
            $('#recharge_amount').val(parseFloat($(this).data('amount')));
        });
    </script>
    <script type="text/javascript">
        var dateParcel = '{{ $request->date }}';
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}">
    </script>
@endpush