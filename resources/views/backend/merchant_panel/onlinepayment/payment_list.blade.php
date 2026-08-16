@extends('backend.merchant_panel.onlinepayment.index')
@section('cardcontent')
    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('menus.payout') }} {{ __('levels.list') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Historique de vos paiements en ligne.</p>
                </div>
            </div>
        </div>

        @if(count($oPayments) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-list-alt"></i></div>
                <p class="wc-empty-title">Aucun paiement en ligne</p>
            </div>
        @else
            <div class="wc-table-wrap">
                <table class="wc-table">
                    <thead>
                        <tr>
                            <th>{{ __('levels.id') }}</th>
                            <th>{{ __('levels.card_type') }}</th>
                            <th>{{ __('levels.to_account') }}</th>
                            <th>{{ __('levels.transaction_id') }}</th>
                            <th class="text-right">{{ __('levels.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @foreach($oPayments as $payment)
                            <tr>
                                <td class="text-wc-muted-2 wc-tabular">{{$i++}}</td>
                                <td><span class="wc-badge wc-badge-info-soft">{{ @__('PaymentType.'.$payment->payment_type) }}</span></td>
                                <td>
                                    <div class="text-[12.5px] leading-relaxed text-wc-ink-2">
                                        @if(@$payment->account->gateway == 1)
                                            {{ __('merchant.cash') }}
                                        @elseif (@$payment->account->gateway == 2)
                                            <span class="font-bold text-wc-ink">{{ @$payment->account->account_holder_name }}</span><br/>
                                            {{ @$payment->account->account_no }}<br/>
                                            {{ @$payment->account->branch_name }}
                                        @else
                                            @if (@$payment->account->gateway == 3)
                                                Bkash
                                            @elseif (@$payment->account->gateway == 4)
                                                Rocket
                                            @elseif (@$payment->account->gateway == 5)
                                                Nagad
                                            @endif
                                            {{ @$payment->account->mobile }}<br/>
                                            {{ @$payment->account->account_type }}
                                        @endif
                                    </div>
                                </td>
                                <td class="wc-tabular font-bold text-wc-ink text-[13px]">{{ @$payment->transaction_id }}</td>
                                <td class="text-right font-bold text-wc-ink wc-tabular">{{ formatPrice(@$payment->amount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $oPayments->firstItem() }}</span>
                    {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $oPayments->lastItem() }}</span>
                    {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $oPayments->total() }}</span> {!! __('results') !!}
                </p>
                <span class="flex items-center gap-1">{{ $oPayments->links() }}</span>
            </div>
        @endif
    </div>
@endsection