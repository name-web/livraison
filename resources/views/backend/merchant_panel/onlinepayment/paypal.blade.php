@extends('backend.merchant_panel.onlinepayment.index')
@section('title')
    {{ __('levels.paypal_payment_details') }}
@endsection
@section('cardcontent')
    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fab fa-paypal"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('levels.paypal_payout_details') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Paiement via PayPal</p>
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
            <label class="wc-label mt-4">{{ __('levels.amount_usd') }} <span class="text-wc-danger">*</span></label>
            <div class="flex items-center gap-2 flex-wrap mt-1.5">
                <input type="number" id="paypal_amount" class="wc-input flex-1 min-w-[160px]" placeholder="0.00" />
            </div>
            <div id="paypal-button-container" class="mt-4"></div>
        </div>
    </div>
@endsection
@push('scripts')
<script src="https://www.paypal.com/sdk/js?client-id={{ globalSettings('paypal_client_id') }}&currency=USD&intent=capture&enable-funding=venmo" data-sdk-integration-source="integrationbuilder"></script>
    <script type="text/javascript">
        var amount = '10';
        var account_id = null;
        $(document).ready(function(){
            $('#paypal_amount').change(function(){
                amount = $('#paypal_amount').val();
            });
            $('#accountId').change(function(){
                account_id = $(this).val();
            });
        });
         const paypalButtonsComponent = paypal.Buttons({
              style: {
                color: "gold",
                shape: "rect",
                layout: "vertical"
              },
              createOrder: (data, actions) => {

                  const createOrderPayload = {
                      purchase_units: [
                          {
                              amount: {
                                  value: amount
                              }
                          }
                      ]
                  };
                  return actions.order.create(createOrderPayload);
              },
              // finalize the transaction
              onApprove: (data, actions) => {
                  const captureOrderHandler = (details) => {
                      const payerName = details.payer.name.given_name;
                  };
                  $.ajax({
                            url: '{{ route("online.payment.paypal") }}',
                            method: 'post',
                            data: {
                                amount: amount * {{ @settings()->excenseRate->exchange_rate }},
                                account_id:account_id,
                                orderID:data.orderID
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
                                        setTimeout(() => {
                                                if(response.success == true){
                                                    window.location.reload();
                                                }
                                        }, 3000);
                                console.log(response)
                            },
                            error: (error) => {
                                console.log(error);
                                alert('Oops! Something went wrong')
                            }
                        });
                  return actions.order.capture().then(captureOrderHandler);
              },
              onError: (err) => {
                  console.error('An error prevented the buyer from checking out with PayPal');
              }
          });
          paypalButtonsComponent
              .render("#paypal-button-container")
              .catch((err) => {
                  console.error('PayPal Buttons failed to render');
              });
    </script>
@endpush