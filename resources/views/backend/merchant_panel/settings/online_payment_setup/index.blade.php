@extends('backend.partials.master')
@section('title')
    {{ __('menus.online_payment_setup') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('menus.online_payment_setup') }}</h1>
            <p class="wc-page-subtitle">{{ __('menus.settings') }} · clés API de vos passerelles de paiement</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- PayPal --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fab fa-paypal"></i></div>
                    <h3 class="wc-card-title">{{ __('levels.paypal') }}</h3>
                </div>
            </div>
            <div class="p-4 sm:p-5">
                <form action="{{route('merchant.online.payment.setup.update',\App\Enums\PayoutSetup::PAYPAL)}}" method="POST" enctype="multipart/form-data" id="basicform">
                    @method('PUT')
                    @csrf
                    <div class="space-y-3">
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="paypal_client_id">{{ __('levels.paypal_client_id') }} <span class="text-wc-danger">*</span></label>
                            <input id="paypal_client_id" type="text" name="paypal_client_id" data-parsley-trigger="change" placeholder="{{ __('levels.paypal_client_id') }}" autocomplete="off" class="wc-input @error('paypal_client_id') is-invalid @enderror" value="{{ old('paypal_client_id', MerchantSettings('paypal_client_id')) }}" required>
                            @error('paypal_client_id')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="paypal_client_secret">{{ __('levels.paypal_client_secret') }} <span class="text-wc-danger">*</span></label>
                            <input id="paypal_client_secret" type="text" name="paypal_client_secret" data-parsley-trigger="change" placeholder="{{ __('levels.paypal_client_secret') }}" autocomplete="off" class="wc-input @error('paypal_client_secret') is-invalid @enderror" value="{{ old('paypal_client_secret', MerchantSettings('paypal_client_secret')) }}" required>
                            @error('paypal_client_secret')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="paypal_mode">{{ __('levels.test_mode') }} <span class="text-wc-danger">*</span></label>
                            <input id="paypal_mode" type="text" name="paypal_mode" data-parsley-trigger="change" placeholder="{{ __('levels.paypal_mode') }}" autocomplete="off" class="wc-input @error('paypal_mode') is-invalid @enderror" value="{{ old('paypal_mode', MerchantSettings('paypal_mode')) }}" required>
                            @error('paypal_mode')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="wc-label m-0" for="switch-id">{{ __('levels.status') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-id ml-3" name="paypal_status" id="switch-id" type="checkbox" role="switch" @if(old('paypal_status', MerchantSettings('paypal_status')) == \App\Enums\Status::ACTIVE) checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" class="wc-btn wc-btn-primary wc-btn-sm">{{ __('levels.save_change') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Razorpay --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fas fa-credit-card"></i></div>
                    <h3 class="wc-card-title">Razorpay</h3>
                </div>
            </div>
            <div class="p-4 sm:p-5">
                <form action="{{route('merchant.online.payment.setup.update',\App\Enums\PayoutSetup::RAZORPAY)}}" method="POST" enctype="multipart/form-data" id="basicform">
                    @method('PUT')
                    @csrf
                    <div class="space-y-3">
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="razorpay_key">{{ __('levels.razorpay_key') }} <span class="text-wc-danger">*</span></label>
                            <input id="razorpay_key" type="text" name="razorpay_key" data-parsley-trigger="change" placeholder="{{ __('levels.razorpay_key') }}" autocomplete="off" class="wc-input @error('razorpay_key') is-invalid @enderror" value="{{ old('razorpay_key', MerchantSettings('razorpay_key')) }}" required>
                            @error('razorpay_key')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="razorpay_secret">{{ __('levels.razorpay_secret') }} <span class="text-wc-danger">*</span></label>
                            <input id="razorpay_secret" type="text" name="razorpay_secret" data-parsley-trigger="change" placeholder="{{ __('levels.razorpay_secret') }}" autocomplete="off" class="wc-input @error('razorpay_secret') is-invalid @enderror" value="{{ old('razorpay_secret', MerchantSettings('razorpay_secret')) }}" required>
                            @error('razorpay_secret')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="wc-label m-0" for="switch-id">{{ __('levels.status') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-id ml-3" name="razorpay_status" id="switch-id" type="checkbox" role="switch" @if(old('razorpay_status', MerchantSettings('razorpay_status')) == \App\Enums\Status::ACTIVE) checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" class="wc-btn wc-btn-primary wc-btn-sm">{{ __('levels.save_change') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Stripe --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fab fa-stripe-s"></i></div>
                    <h3 class="wc-card-title">{{ __('levels.stripe') }}</h3>
                </div>
            </div>
            <div class="p-4 sm:p-5">
                <form action="{{route('merchant.online.payment.setup.update',\App\Enums\PayoutSetup::STRIPE)}}" method="POST" enctype="multipart/form-data" id="basicform">
                    @method('PUT')
                    @csrf
                    <div class="space-y-3">
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="stripe_publishable_key">{{ __('levels.stripe_publishable_key') }} <span class="text-wc-danger">*</span></label>
                            <input id="stripe_publishable_key" type="text" name="stripe_publishable_key" data-parsley-trigger="change" placeholder="{{ __('levels.stripe_publishable_key') }}" autocomplete="off" class="wc-input @error('stripe_publishable_key') is-invalid @enderror" value="{{ old('stripe_publishable_key', MerchantSettings('stripe_publishable_key')) }}" required>
                            @error('stripe_publishable_key')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="stripe_secret_key">{{ __('levels.stripe_secret_key') }} <span class="text-wc-danger">*</span></label>
                            <input id="stripe_secret_key" type="text" name="stripe_secret_key" data-parsley-trigger="change" placeholder="{{ __('levels.stripe_secret_key') }}" autocomplete="off" class="wc-input @error('stripe_secret_key') is-invalid @enderror" value="{{ old('stripe_secret_key', MerchantSettings('stripe_secret_key')) }}" required>
                            @error('stripe_secret_key')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="wc-label m-0" for="switch-id">{{ __('levels.status') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-id ml-3" name="stripe_status" id="switch-id" type="checkbox" role="switch" @if(old('stripe_status', MerchantSettings('stripe_status')) == \App\Enums\Status::ACTIVE) checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" class="wc-btn wc-btn-primary wc-btn-sm">{{ __('levels.save_change') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Skrill --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fas fa-credit-card"></i></div>
                    <h3 class="wc-card-title">{{ __('levels.skrill') }}</h3>
                </div>
            </div>
            <div class="p-4 sm:p-5">
                <form action="{{route('merchant.online.payment.setup.update',\App\Enums\PayoutSetup::SKRILL)}}" method="POST" enctype="multipart/form-data" id="basicform">
                    @method('PUT')
                    @csrf
                    <div class="space-y-3">
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="skrill_merchant_email">{{ __('levels.skrill_merchant_email') }} <span class="text-wc-danger">*</span></label>
                            <input id="skrill_merchant_email" type="text" name="skrill_merchant_email" data-parsley-trigger="change" placeholder="{{ __('levels.skrill_merchant_email') }}" autocomplete="off" class="wc-input @error('skrill_merchant_email') is-invalid @enderror" value="{{ old('skrill_merchant_email', MerchantSettings('skrill_merchant_email')) }}" required>
                            @error('skrill_merchant_email')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="wc-label m-0" for="switch-id">{{ __('levels.status') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-id ml-3" name="skrill_status" id="switch-id" type="checkbox" role="switch" @if(old('skrill_status', MerchantSettings('skrill_status')) == \App\Enums\Status::ACTIVE) checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" class="wc-btn wc-btn-primary wc-btn-sm">{{ __('levels.save_change') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- SSLCommerz --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fas fa-shield-alt"></i></div>
                    <h3 class="wc-card-title">{{ __('levels.sslcommerz') }}</h3>
                </div>
            </div>
            <div class="p-4 sm:p-5">
                <form action="{{route('merchant.online.payment.setup.update',\App\Enums\PayoutSetup::SSL_COMMERZ)}}" method="POST" enctype="multipart/form-data" id="basicform">
                    @method('PUT')
                    @csrf
                    <div class="space-y-3">
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="sslcommerz_store_id">{{ __('levels.sslcommerz_store_id') }} <span class="text-wc-danger">*</span></label>
                            <input id="sslcommerz_store_id" type="text" name="sslcommerz_store_id" data-parsley-trigger="change" placeholder="{{ __('levels.sslcommerz_store_id') }}" autocomplete="off" class="wc-input @error('sslcommerz_store_id') is-invalid @enderror" value="{{ old('sslcommerz_store_id', MerchantSettings('sslcommerz_store_id')) }}" required>
                            @error('sslcommerz_store_id')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="sslcommerz_store_password">{{ __('levels.sslcommerz_store_password') }} <span class="text-wc-danger">*</span></label>
                            <input id="sslcommerz_store_password" type="text" name="sslcommerz_store_password" data-parsley-trigger="change" placeholder="{{ __('levels.sslcommerz_store_password') }}" autocomplete="off" class="wc-input @error('sslcommerz_store_password') is-invalid @enderror" value="{{ old('sslcommerz_store_password', MerchantSettings('sslcommerz_store_password')) }}" required>
                            @error('sslcommerz_store_password')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="wc-label m-0" for="switch-id">{{ __('levels.test_mode') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-id ml-3" name="sslcommerz_testmode" id="switch-id" type="checkbox" role="switch" @if(old('sslcommerz_testmode', MerchantSettings('sslcommerz_testmode')) == \App\Enums\Status::ACTIVE) checked @endif>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="wc-label m-0" for="switch-id">{{ __('levels.status') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-id ml-3" name="sslcommerz_status" id="switch-id" type="checkbox" role="switch" @if(old('sslcommerz_status', MerchantSettings('sslcommerz_status')) == \App\Enums\Status::ACTIVE) checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" class="wc-btn wc-btn-primary wc-btn-sm">{{ __('levels.save_change') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Aamarpay --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fas fa-credit-card"></i></div>
                    <h3 class="wc-card-title">{{ __('levels.aamarpay') }}</h3>
                </div>
            </div>
            <div class="p-4 sm:p-5">
                <form action="{{route('merchant.online.payment.setup.update',\App\Enums\PayoutSetup::AAMARPAY)}}" method="POST" enctype="multipart/form-data" id="basicform">
                    @method('PUT')
                    @csrf
                    <div class="space-y-3">
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="aamarpay_store_id">{{ __('levels.aamarpay_store_id') }} <span class="text-wc-danger">*</span></label>
                            <input id="aamarpay_store_id" type="text" name="aamarpay_store_id" data-parsley-trigger="change" placeholder="{{ __('levels.aamarpay_store_id') }}" autocomplete="off" class="wc-input @error('aamarpay_store_id') is-invalid @enderror" value="{{ old('aamarpay_store_id', MerchantSettings('aamarpay_store_id')) }}" required>
                            @error('aamarpay_store_id')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="aamarpay_signature_key">{{ __('levels.aamarpay_signature_key') }} <span class="text-wc-danger">*</span></label>
                            <input id="aamarpay_signature_key" type="text" name="aamarpay_signature_key" data-parsley-trigger="change" placeholder="{{ __('levels.aamarpay_signature_key') }}" autocomplete="off" class="wc-input @error('aamarpay_signature_key') is-invalid @enderror" value="{{ old('aamarpay_signature_key', MerchantSettings('aamarpay_signature_key')) }}" required>
                            @error('aamarpay_signature_key')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="wc-label m-0" for="switch-id">{{ __('levels.sendbox_mode') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-id ml-3" name="aamarpay_sendbox_mode" id="switch-id" type="checkbox" role="switch" @if(old('aamarpay_sendbox_mode', MerchantSettings('aamarpay_sendbox_mode')) == \App\Enums\Status::ACTIVE) checked @endif>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="wc-label m-0" for="switch-id">{{ __('levels.status') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-id ml-3" name="aamarpay_status" id="switch-id" type="checkbox" role="switch" @if(old('aamarpay_status', MerchantSettings('aamarpay_status')) == \App\Enums\Status::ACTIVE) checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" class="wc-btn wc-btn-primary wc-btn-sm">{{ __('levels.save_change') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- bKash --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fas fa-mobile-alt"></i></div>
                    <h3 class="wc-card-title">{{ __('levels.bkash') }}</h3>
                </div>
            </div>
            <div class="p-4 sm:p-5">
                <form action="{{route('merchant.online.payment.setup.update',\App\Enums\PayoutSetup::BKASH)}}" method="POST" enctype="multipart/form-data" id="basicform">
                    @method('PUT')
                    @csrf
                    <div class="space-y-3">
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="bkash_app_id">{{ __('levels.bkash_app_id') }} <span class="text-wc-danger">*</span></label>
                            <input id="bkash_app_id" type="text" name="bkash_app_id" data-parsley-trigger="change" placeholder="{{ __('levels.bkash_app_id') }}" autocomplete="off" class="wc-input @error('bkash_app_id') is-invalid @enderror" value="{{ old('bkash_app_id', MerchantSettings('bkash_app_id')) }}" required>
                            @error('bkash_app_id')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="bkash_app_secret">{{ __('levels.bkash_app_secret') }} <span class="text-wc-danger">*</span></label>
                            <input id="bkash_app_secret" type="text" name="bkash_app_secret" data-parsley-trigger="change" placeholder="{{ __('levels.bkash_app_secret') }}" autocomplete="off" class="wc-input @error('bkash_app_secret') is-invalid @enderror" value="{{ old('bkash_app_secret', MerchantSettings('bkash_app_secret')) }}" required>
                            @error('bkash_app_secret')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="bkash_username">{{ __('levels.bkash_username') }} <span class="text-wc-danger">*</span></label>
                            <input id="bkash_username" type="text" name="bkash_username" data-parsley-trigger="change" placeholder="{{ __('levels.bkash_username') }}" autocomplete="off" class="wc-input @error('bkash_username') is-invalid @enderror" value="{{ old('bkash_username', MerchantSettings('bkash_username')) }}" required>
                            @error('bkash_username')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="bkash_password">{{ __('levels.bkash_password') }} <span class="text-wc-danger">*</span></label>
                            <input id="bkash_password" type="password" name="bkash_password" data-parsley-trigger="change" placeholder="{{ __('levels.bkash_password') }}" autocomplete="off" class="wc-input @error('bkash_password') is-invalid @enderror" value="{{ old('bkash_password', MerchantSettings('bkash_password')) }}" required>
                            @error('bkash_password')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="wc-label m-0" for="switch-id">{{ __('levels.bkash_test_mode') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-id ml-3" name="bkash_test_mode" id="switch-id" type="checkbox" role="switch" @if(old('bkash_test_mode', MerchantSettings('bkash_test_mode')) == \App\Enums\Status::ACTIVE) checked @endif>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="wc-label m-0" for="switch-id">{{ __('levels.status') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-id ml-3" name="bkash_status" id="switch-id" type="checkbox" role="switch" @if(old('bkash_status', MerchantSettings('bkash_status')) == \App\Enums\Status::ACTIVE) checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" class="wc-btn wc-btn-primary wc-btn-sm">{{ __('levels.save_change') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Paystack --}}
        <div class="wc-card">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary"><i class="fas fa-credit-card"></i></div>
                    <h3 class="wc-card-title">Paystack</h3>
                </div>
            </div>
            <div class="p-4 sm:p-5">
                <form action="{{route('merchant.online.payment.setup.update',\App\Enums\PayoutSetup::PAYSTACK)}}" method="POST" enctype="multipart/form-data" id="basicform">
                    @method('PUT')
                    @csrf
                    <div class="space-y-3">
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="paystack_secret_key">{{ __('levels.paystack_secret_key') }} <span class="text-wc-danger">*</span></label>
                            <input id="paystack_secret_key" type="text" name="paystack_secret_key" data-parsley-trigger="change" placeholder="{{ __('levels.paystack_secret_key') }}" autocomplete="off" class="wc-input @error('paystack_secret_key') is-invalid @enderror" value="{{ old('paystack_secret_key', MerchantSettings('paystack_secret_key')) }}" required>
                            @error('paystack_secret_key')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="wc-form-group m-0">
                            <label class="wc-label" for="paystack_callback_url">{{ __('levels.paystack_callback_url') }} <span class="text-wc-danger">*</span></label>
                            <input id="paystack_callback_url" type="text" name="paystack_callback_url" data-parsley-trigger="change" placeholder="{{ __('levels.paystack_callback_url') }}" autocomplete="off" class="wc-input @error('paystack_callback_url') is-invalid @enderror" value="{{ old('paystack_callback_url', MerchantSettings('paystack_callback_url')) }}" required>
                            @error('paystack_callback_url')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="wc-label m-0" for="switch-id">{{ __('levels.status') }}</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-id ml-3" name="paystack_status" id="switch-id" type="checkbox" role="switch" @if(old('paystack_status', globalSettings('paystack_status')) == \App\Enums\Status::ACTIVE) checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" class="wc-btn wc-btn-primary wc-btn-sm">{{ __('levels.save_change') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection()