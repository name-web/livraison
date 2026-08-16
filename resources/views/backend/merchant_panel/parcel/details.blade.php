@extends('backend.partials.master')
@section('title')
    {{ __('parcel.title') }} {{ __('levels.view') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('invoice.invoice') }} · #{{ @$parcel->invoice_no }}</h1>
            <p class="wc-page-subtitle">{{ __('levels.details') }} du colis {{ @$parcel->tracking_id }}</p>
        </div>
        <div class="wc-toolbar">
            <a href="{{route('merchant-panel.parcel.index')}}" class="wc-btn wc-btn-outline wc-btn-sm"><i class="fas fa-arrow-left text-[12px]"></i> {{ __('levels.back') }}</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Cash on delivery --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fas fa-hand-holding-usd"></i></div>
                    <h3 class="wc-card-title">{{ __('levels.cash_on_delivery') }}</h3>
                </div>
            </div>
            <div class="p-4 space-y-2.5">
                <div class="flex items-center justify-between gap-3 text-[13px] border-b border-wc-border pb-2.5">
                    <span class="text-wc-muted">{{__('levels.delivery_fee')}}</span>
                    <span class="font-bold text-wc-ink wc-tabular">{{ formatPrice(($parcel->total_delivery_amount - $parcel->cod_amount)) }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 text-[13px] border-b border-wc-border pb-2.5">
                    <span class="text-wc-muted">{{__('levels.cod')}}</span>
                    <span class="font-bold text-wc-ink wc-tabular">{{ formatPrice(@$parcel->cod_amount) }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 text-[13.5px]">
                    <strong class="text-wc-ink">{{__('levels.total_cost')}}</strong>
                    <strong class="text-wc-primary wc-tabular">{{ formatPrice(@$parcel->total_delivery_amount) }}</strong>
                </div>
            </div>
        </div>

        {{-- Delivery info --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fas fa-truck"></i></div>
                    <h3 class="wc-card-title">{{ __('levels.delivery_info') }}</h3>
                </div>
            </div>
            <div class="p-4 space-y-2.5">
                <div class="flex items-center justify-between gap-3 text-[13px] border-b border-wc-border pb-2.5">
                    <span class="text-wc-muted">{{__('levels.delivery_type')}}</span>
                    <span class="font-bold text-wc-ink">{{ @$parcel->delivery_type_name }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 text-[13px] border-b border-wc-border pb-2.5">
                    <span class="text-wc-muted">{{__('levels.weight')}}</span>
                    <span class="font-bold text-wc-ink">{{@$parcel->weight}} {{@$parcel->deliveryCategory->title}}</span>
                </div>
                <div class="flex items-center justify-between gap-3 text-[13.5px]">
                    <strong class="text-wc-ink">{{__('levels.amount_to_collect')}}</strong>
                    <strong class="text-wc-primary wc-tabular">{{ formatPrice(@$parcel->cash_collection) }}</strong>
                </div>
            </div>
        </div>

        {{-- Sender info --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fas fa-user-tie"></i></div>
                    <h3 class="wc-card-title">{{ __('levels.sender_info') }}</h3>
                </div>
            </div>
            <div class="p-4 space-y-2.5">
                <div class="flex items-center justify-between gap-3 text-[13px] border-b border-wc-border pb-2.5">
                    <span class="text-wc-muted">{{__('levels.business_name')}}</span>
                    <span class="font-bold text-wc-ink text-right">{{ @$parcel->merchant->business_name }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 text-[13px] border-b border-wc-border pb-2.5">
                    <span class="text-wc-muted">{{__('levels.mobile')}}</span>
                    <span class="font-bold text-wc-ink wc-tabular">{{ @$parcel->merchant->user->mobile }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 text-[13px]">
                    <span class="text-wc-muted">{{__('levels.email')}}</span>
                    <span class="font-bold text-wc-ink text-right break-all">{{ @$parcel->merchant->user->email }}</span>
                </div>
            </div>
        </div>

        {{-- Recipient info --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fas fa-user"></i></div>
                    <h3 class="wc-card-title">{{ __('levels.recipient_info') }}</h3>
                </div>
            </div>
            <div class="p-4 space-y-2.5">
                <div class="flex items-center justify-between gap-3 text-[13px] border-b border-wc-border pb-2.5">
                    <span class="text-wc-muted">{{__('levels.name')}}</span>
                    <span class="font-bold text-wc-ink text-right">{{ @$parcel->customer_name }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 text-[13px] border-b border-wc-border pb-2.5">
                    <span class="text-wc-muted">{{__('levels.phone')}}</span>
                    <span class="font-bold text-wc-ink wc-tabular">{{ @$parcel->customer_phone }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 text-[13px]">
                    <span class="text-wc-muted">{{__('levels.address')}}</span>
                    <span class="font-bold text-wc-ink text-right">{{ @$parcel->customer_address }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection()