@extends('backend.merchant_panel.onlinepayment.index')
@section('title')
    {{ __('levels.stripe_payment_details') }}
@endsection
@section('cardcontent')
    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('levels.stripe_payout_details') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Paiement via carte bancaire (Stripe)</p>
                </div>
            </div>
        </div>
        <div class="p-4 sm:p-5">
            <form id="paymentForm">
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
                    <input type="number" id="stripe_amount" class="wc-input flex-1 min-w-[160px]" placeholder="0.00" />
                    <button type="submit" class="wc-btn wc-btn-primary">{{ __('levels.pay_now') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script src = "https://checkout.stripe.com/checkout.js" > </script>
    <script type = "text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        $('#paymentForm').on('submit',function (e) {
                e.preventDefault();
                var amount = $('#stripe_amount').val();
                var accountId = $('#accountId').val();
                if(amount ==''){
                    alert('Amount feild is required.');

                }else{
                    var handler = StripeCheckout.configure({
                        key: '{{ globalSettings("stripe_publishable_key") }}', // your publisher key id
                        locale: 'auto',
                        token: function(token) {
                            console.log('Token Created!!');
                            console.log(token)
                            $('#res_token').html(JSON.stringify(token));
                            $.ajax({
                                url: '{{ route("online.payment.stripe.post") }}',
                                method: 'post',
                                data: {
                                    tokenId: token.id,
                                    amount: amount * {{@settings()->excenseRate->exchange_rate }},
                                    account_id:accountId

                                },
                                success: (response) => {
                                    const Toast = Swal.mixin({
                                                toast: true,
                                                position: 'top-end',
                                                showConfirmButton: false,
                                                timer: 3000,
                                                timerProgressBar: true,
                                                didOpen: (toast) => {
                                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                                                }
                                            })

                                            Toast.fire({
                                                icon: 'success',
                                                title: 'Payment successfully'
                                            })
                                            if(response.success == true){
                                                window.location.reload();
                                            }
                                },
                                error: (error) => {
                                    console.log(error);
                                    alert('Oops! Something went wrong')
                                }
                            })
                        }
                    });
                    handler.open({
                        name: "{{ settings()->name }}",
                        description: 'Merchant Payment',
                        amount: amount *100
                    });
                }

        });

    </script>
@endpush