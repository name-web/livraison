@extends('backend.partials.master')
@section('title')
    {{ __('paymentrequest.title') }} {{ __('levels.edit') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('levels.edit') }} {{ __('paymentrequest.submit_request') }}</h1>
            <p class="wc-page-subtitle">{{ __('paymentrequest.title') }} · {{ __('levels.edit') }}</p>
        </div>
    </div>

    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('levels.edit') }} {{ __('paymentrequest.submit_request') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Modifiez les informations puis enregistrez.</p>
                </div>
            </div>
        </div>

        <div class="wc-card-body">
            <form action="{{route('merchant-panel.payment-request.update')}}" method="POST" enctype="multipart/form-data" id="basicform">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $singlePayment->id }}" />
                <div class="row">
                    <div class="col-12 col-md-6">
                        <input type="hidden" name="old_amount" value="{{$singlePayment->amount}}">
                        <div class="wc-form-group">
                            <label class="wc-label" for="amount">{{ __('merchantmanage.amount') }} <span class="text-danger">*</span></label>
                            <input id="amount" type="number" name="amount" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.amount') }}" autocomplete="off" class="wc-input" value="{{old('amount',$singlePayment->amount)}}" required>
                            @error('amount')
                                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="wc-form-group">
                            <label class="wc-label" for="merchant">{{ __('paymentrequest.account') }} <span class="text-danger">*</span></label>
                            <select id="merchant_account" class="wc-select" name="merchant_account">
                                <option selected disabled>{{ __('merchantPlaceholder.select_account') }}</option>
                                @foreach ($merchantaccounts as $account)
                                    @if($account->payment_method == 'bank')
                                        <option value='{{ $account->id }}' @if($singlePayment->merchant_account == $account->id) selected @endif>{{ $account->holder_name }},{{optional($account->bank)->name }},{{ $account->account_no }},{{ $account->branch_name }}</option>
                                    @elseif($account->payment_method == 'mobile')
                                        <option value='{{ $account->id }}' @if($singlePayment->merchant_account == $account->id) selected @endif>{{ optional($account->mobileBank)->name }},{{ $account->holder_name }},{{ $account->mobile_no }},{{ $account->account_type }}</option>
                                    @elseif ($account->payment_method == 'cash')
                                        <option value='{{ $account->id }}' @if($singlePayment->merchant_account == $account->id) selected @endif>{{ __('merchant.'.$account->payment_method) }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('merchantaccount')
                                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="wc-form-group">
                            <label class="wc-label" for="description">{{ __('merchantmanage.description') }}</label>
                            <textarea name="description" class="wc-input" style="padding-top:10px;min-height:110px;resize:vertical">{{ old('description',$singlePayment->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-12 col-md-6"></div>
                </div>
                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-wc-border">
                    <button type="submit" class="wc-btn wc-btn-primary">{{ __('levels.save_change') }}</button>
                    <a href="{{ route('merchant-panel.payment-request.index') }}" class="wc-btn wc-btn-outline">{{ __('levels.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection()
@push('scripts')
     <script src="{{ static_asset('backend/js/merchantmanaage/create.js') }}"></script>
@endpush