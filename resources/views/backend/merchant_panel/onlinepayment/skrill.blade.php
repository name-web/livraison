@extends('backend.merchant_panel.onlinepayment.index')
@section('title')
    {{ __('levels.skrill_payment_details') }}
@endsection
@section('cardcontent')
    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('levels.skrill_payout_details') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Paiement via Skrill</p>
                </div>
            </div>
        </div>
        <div class="p-4 sm:p-5">
            <form action="{{route('skrill.make.payment')}}" method="get">
                <div class="wc-form-group m-0">
                    <label class="wc-label" for="accountId">{{ __('levels.to_account') }}</label>
                    <select id="accountId" name="account_id" class="wc-select @error('merchant_id') is-invalid @enderror">
                        @foreach ($accounts as $account)
                            @if ($account->gateway == 1)
                                <option value="{{ $account->id }}">{{ $account->user->name }} | {{ __('merchant.cash') }} : {{ $account->balance }}</option>
                            @elseif($account->gateway == 3 || $account->gateway == 4 || $account->gateway == 5)
                                <option value="{{ $account->id }}">{{$account->account_holder_name}} |No : {{ $account->mobile }}| @if($account->type == 1) {{ __('merchant.title') }} @else {{ __('placeholder.persional') }} @endif | {{ __('merchantmanage.current_balance') }}: {{ $account->balance }} </option>
                            @else
                                <option value="{{ $account->id }}">{{$account->account_holder_name}} | A.No : {{ $account->account_no }} | {{ __('merchantmanage.current_balance') }}: {{ $account->balance }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <label class="wc-label mt-4">{{ __('levels.amount_usd') }} <span class="text-wc-danger">*</span></label>
                <div class="flex items-center gap-2 flex-wrap mt-1.5">
                    <input type="number" name="amount" id="skrill_amount" class="wc-input flex-1 min-w-[160px]" placeholder="0.00" />
                    <button type="submit" class="wc-btn wc-btn-primary">{{ __('levels.pay_now') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection