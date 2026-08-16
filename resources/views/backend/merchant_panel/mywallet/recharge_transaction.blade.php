<div class="wc-table-wrap">
    <table class="wc-table">
        <thead>
            <tr>
                <th>{{ __('levels.id') }}</th>
                <th>{{ __('parcel.source') }}</th>
                <th>{{ __('levels.date') }}</th>
                <th>{{ __('parcel.transaction_id') }}</th>
                <th>{{ __('parcel.payment_method') }}</th>
                <th class="text-right">{{ __('parcel.amount') }}</th>
                <th>{{ __('parcel.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 0; @endphp
            @foreach ($recharge_transactions as $recharge_wallet)
                <tr>
                    <td class="text-wc-muted-2 wc-tabular">{{ ++$i }}</td>
                    <td class="text-wc-ink font-bold text-[13px]">{{ $recharge_wallet->source }}</td>
                    <td class="text-wc-muted-2 whitespace-nowrap">{{ dateFormat($recharge_wallet->created_at) }}</td>
                    <td class="wc-tabular">{{ @$recharge_wallet->transaction_id }}</td>
                    <td class="text-wc-muted">{{ __('WalletPaymentMethod.' . $recharge_wallet->payment_method) }}</td>
                    <td class="text-right">
                        @if ($recharge_wallet->type == App\Enums\Wallet\WalletType::INCOME)
                            <span class="text-wc-success font-bold wc-tabular">+ {{ formatPrice(@$recharge_wallet->amount) }}</span>
                        @elseif($recharge_wallet->type == App\Enums\Wallet\WalletType::EXPENSE)
                            <span class="text-wc-danger font-bold wc-tabular">{{ formatPrice(@$recharge_wallet->amount) }}</span>
                        @endif
                    </td>
                    <td>
                        @if ($recharge_wallet->type == App\Enums\Wallet\WalletType::INCOME)
                            {!! @$recharge_wallet->my_status !!}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if(count($recharge_transactions) === 0)
    <div class="wc-empty">
        <div class="wc-empty-icon"><i class="fas fa-arrow-down"></i></div>
        <p class="wc-empty-title">Aucune recharge</p>
        <p class="wc-empty-description">Vos recharges apparaîtront ici.</p>
    </div>
@endif
<div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
    <p class="m-0 text-[12.5px] text-wc-muted">
        {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $recharge_transactions->firstItem() }}</span>
        {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $recharge_transactions->lastItem() }}</span>
        {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $recharge_transactions->total() }}</span> {!! __('results') !!}
    </p>
    <span class="flex items-center gap-1">{{ $recharge_transactions->links() }}</span>
</div>