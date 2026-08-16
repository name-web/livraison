@extends('backend.partials.master')
@section('title')
    {{ __('menus.payout') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('menus.payout') }}</h1>
            <p class="wc-page-subtitle">Rechargez votre compte via une passerelle de paiement en ligne</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">
        {{-- Passerelles --}}
        <div class="xl:col-span-2 space-y-3">
            <div class="wc-card">
                <div class="wc-card-header">
                    <div class="flex items-center gap-3">
                        <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <h3 class="wc-card-title">{{ __('menus.payout') }}</h3>
                            <p class="text-[12px] text-wc-muted m-0">Sélectionnez une passerelle.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                @if(globalSettings('paypal_status') == \App\Enums\Status::ACTIVE)
                <a href="{{ route('online.payment.paypal.index') }}" class="wc-card !p-0 overflow-hidden group">
                    <div class="p-4 text-center transition-colors group-hover:bg-wc-surface-soft">
                        <img src="{{ static_asset('backend/images/default/payout/paypal.png') }}" alt="paypal" class="h-9 object-contain mx-auto" style="margin:-5px"/>
                    </div>
                </a>
                @endif
                @if(globalSettings('stripe_status') == \App\Enums\Status::ACTIVE)
                <a href="{{ route('online.payment.stripe') }}" class="wc-card !p-0 overflow-hidden group">
                    <div class="p-4 text-center transition-colors group-hover:bg-wc-surface-soft">
                        <img src="{{ static_asset('backend/images/default/payout/stripe.png') }}" alt="stripe" class="h-9 object-contain mx-auto"/>
                    </div>
                </a>
                @endif
                @if(globalSettings('skrill_status') == \App\Enums\Status::ACTIVE)
                <a href="{{ route('skrill.index') }}" class="wc-card !p-0 overflow-hidden group">
                    <div class="p-4 text-center transition-colors group-hover:bg-wc-surface-soft">
                        <img src="{{ static_asset('backend/images/default/payout/skrill.png') }}" alt="skrill" class="h-9 object-contain mx-auto" style="margin:10px"/>
                    </div>
                </a>
                @endif
                @if(globalSettings('sslcommerz_status') == \App\Enums\Status::ACTIVE)
                <a href="{{ route('online.payment.sslcommerz.index') }}" class="wc-card !p-0 overflow-hidden group">
                    <div class="p-4 text-center transition-colors group-hover:bg-wc-surface-soft">
                        <img src="{{ static_asset('backend/images/default/payout/sslecommerce.png') }}" alt="sslcommerz" class="h-9 object-contain mx-auto" style="margin:20px"/>
                    </div>
                </a>
                @endif
                @if(globalSettings('aamarpay_status') == \App\Enums\Status::ACTIVE)
                <a href="{{ route('online.payment.aamarpay.index') }}" class="wc-card !p-0 overflow-hidden group">
                    <div class="p-4 text-center transition-colors group-hover:bg-wc-surface-soft">
                        <img src="{{ static_asset('backend/images/default/payout/aamarpay.png') }}" alt="aamarpay" class="h-9 object-contain mx-auto" style="margin:25px"/>
                    </div>
                </a>
                @endif
                @if(globalSettings('bkash_status') == \App\Enums\Status::ACTIVE)
                <a href="{{ route('online.payment.bkash.index') }}" class="wc-card !p-0 overflow-hidden group">
                    <div class="p-4 text-center transition-colors group-hover:bg-wc-surface-soft">
                        <img src="{{ static_asset('backend/images/default/payout/bkash.png') }}" alt="bkash" class="h-9 object-contain mx-auto" style="margin:10px"/>
                    </div>
                </a>
                @endif
                @if(globalSettings('paystack_status') == \App\Enums\Status::ACTIVE)
                <a href="{{ route('online.payment.paystack.index') }}" class="wc-card !p-0 overflow-hidden group">
                    <div class="p-4 text-center transition-colors group-hover:bg-wc-surface-soft">
                        <img src="{{ static_asset('backend/images/default/payout/paystack.png') }}" alt="paystack" class="h-9 object-contain mx-auto" style="margin:10px"/>
                    </div>
                </a>
                @endif
            </div>
        </div>

        {{-- Formulaire de la passerelle --}}
        <div class="xl:col-span-3">
            @yield('cardcontent')
        </div>
    </div>
</div>
@endsection()