<div class="row">
    <div class="col-lg-6">
        <form action="{{ route('merchant-panel.my.wallet.recharge.add') }}" method="post" id="recharge-form">
            @csrf
            <div class="wc-form-group">
                <label class="wc-label" for="recharge_amount">{{ __('levels.amount') }} <span class="text-danger">*</span></label>
                <div class="flex items-center gap-2">
                    <input id="recharge_amount" type="number" name="amount" data-parsley-trigger="change"
                        placeholder="{{ __('levels.amount') }}" autocomplete="off" class="wc-input"
                        value="{{ old('amount') }}">
                    <button class="wc-btn wc-btn-primary flex-shrink-0" type="submit">{{ __('parcel.paynow') }}</button>
                </div>
                <small class="text-wc-muted-2 text-[11.5px] d-block mt-1">Montant en FCFA · paiement via Mobile Money ou carte</small>
                @error('width')
                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                @enderror
            </div>
        </form>
        <h5 class="text-[13px] font-extrabold text-wc-ink uppercase mt-3 mb-1">{{ __('parcel.quick_add') }}</h5>
        <p class="text-[12.5px] text-wc-muted">{{ __('parcel.quickly_add_money_from_given_options_and_recharge_your_wallet') }}</p>
        <div class="grid grid-cols-4 gap-2">
            @foreach ([5000, 10000, 20000, 50000, 100000, 200000, 500000, 1000000] as $quickAmount)
                <button type="button" class="quick-amount wc-btn wc-btn-outline wc-btn-sm !px-2" data-amount="{{ $quickAmount }}" style="cursor:pointer">
                    {{ number_format($quickAmount, 0, ',', ' ') }}
                </button>
            @endforeach
        </div>
    </div>
    <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center">
        <img src="{{ static_asset('backend/images/default/payout/wallet.png') }}" class="w-100" style="max-width:320px" />
    </div>
</div>