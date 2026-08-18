@extends('backend.partials.master')
@section('title')
    {{ __('parcel.my_wallet') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Solde --}}
    <div class="wc-wallet-balance animate-wcFadeUp">
        <div class="flex items-center gap-4">
            <div class="wc-wallet-balance-ic">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="min-w-0">
                <p class="wc-wallet-balance-label m-0">{{ __('parcel.available_balance') }}</p>
                <p class="wc-wallet-balance-value m-0">{{ formatPrice($stats['balance']) }}</p>
                <p class="wc-wallet-balance-meta m-0">
                    + {{ formatPrice($stats['income']) }} {{ __('parcel.total_recharge') }}
                    <span class="sep">·</span>
                    - {{ formatPrice($stats['expense']) }} {{ __('parcel.total_deduction') }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="wc-badge wc-badge-warning">{{ $stats['pending'] }} {{ __('WalletStatus.1') }}</span>
                <span class="wc-badge wc-badge-success">{{ $stats['approved'] }} {{ __('WalletStatus.2') }}</span>
                <span class="wc-badge wc-badge-error">{{ $stats['rejected'] }} {{ __('WalletStatus.3') }}</span>
            </div>
            @if($stats['balance'] <= 10)
                <span class="text-[12px] font-bold text-wc-danger"><i class="fas fa-exclamation-triangle"></i> Solde faible</span>
            @endif
            <a href="#" class="wc-btn wc-btn-primary wc-btn-sm modalBtn"
                data-url="{{ route('merchant-panel.my.wallet.recharge') }}"
                data-title="{{ __('parcel.recharge_wallet') }}" data-bs-toggle="modal"
                data-modalsize="modal-lg" data-bs-target="#dynamic-modal">
                <i class="fas fa-plus"></i> {{ __('parcel.recharge_wallet') }}
            </a>
        </div>
    </div>

    {{-- Historique --}}
    <div class="wc-card animate-wcFadeUp" style="animation-delay:.06s">
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
    <style>
        .wc-wallet-balance {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            background: #fff;
            border: 1px solid #e7ebe9;
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 16px;
        }
        .wc-wallet-balance-ic {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            background: #ecfdf5;
            color: #059669;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .wc-wallet-balance-label {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #94a3b8;
        }
        .wc-wallet-balance-value {
            font-size: 28px;
            font-weight: 800;
            color: #047857;
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
        }
        .wc-wallet-balance-meta {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
        }
        .wc-wallet-balance-meta .sep { margin: 0 7px; opacity: .6; }
    </style>
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