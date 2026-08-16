@extends('backend.merchant_panel.onlinepayment.index')
@section('title')
    {{ __('levels.sslcommerz_payment_details') }}
@endsection
@section('cardcontent')
    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('levels.sslcommerz_payout_details') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Paiement via SSLCommerz</p>
                </div>
            </div>
        </div>
        <div class="p-4 sm:p-5">
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
            <label class="wc-label mt-4">{{ __('levels.amount') }} <span class="text-wc-danger">*</span></label>
            <div class="flex items-center gap-2 flex-wrap mt-1.5">
                <input type="number" id="total_amount" class="wc-input flex-1 min-w-[160px]" placeholder="0.00" />
                <button class="wc-btn wc-btn-primary" id="sslczPayBtn"
                    token="if you have any token validation"
                    postdata=""
                    order="If you already have the transaction generated for current order"
                    endpoint="{{ url('/pay-via-ajax') }}">{{ __('levels.pay_now') }}
                </button>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>

<!-- If you want to use the popup integration, -->
<script type="text/javascript">
$(document).ready(function(){
    var obj = {};
    $("#total_amount").on('change',function(){
        obj.amount = $(this).val();
        $('#sslczPayBtn').prop('postdata', obj);
    });

    obj.account_id = $("#accountId").val();
    $('#sslczPayBtn').prop('postdata', obj);

    $("#accountId").on('change',function(){
        obj.account_id = $(this).val();
        $('#sslczPayBtn').prop('postdata', obj);
    });

    $('#sslczPayBtn').click(function(){
        if($('#total_amount').val() == ''){
            alert('Amount fieds is required');
        }else{

        }
    });
});

(function (window, document) {
        var loader = function () {
            var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
            script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7); // USE THIS FOR SANDBOX
            tag.parentNode.insertBefore(script, tag);
        };
    window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
})(window, document);

</script>
@endpush