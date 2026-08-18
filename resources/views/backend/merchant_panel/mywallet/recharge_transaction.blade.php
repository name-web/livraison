<div class="flex items-center gap-3 flex-wrap px-4 py-3 border-b border-wc-border bg-[#f8fafc]">
    <div class="relative flex-1 min-w-[220px]">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[13px] pointer-events-none"></i>
        <input type="text" id="wcWalletRechargeSearch" class="wc-input !pl-9" placeholder="Rechercher une recharge (source, transaction, montant)...">
    </div>
    <span class="text-[12.5px] text-wc-muted wc-tabular whitespace-nowrap" id="wcWalletRechargeCounter"></span>
</div>
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
        <tbody id="wcWalletRechargeBody">
            @php $i = 0; @endphp
            @foreach ($recharge_transactions as $recharge_wallet)
                @php
                    $isIncome = $recharge_wallet->type == App\Enums\Wallet\WalletType::INCOME;
                    $statusKey = (int) $recharge_wallet->status;
                    $badgeClass = match($statusKey) {
                        App\Enums\Wallet\WalletStatus::APPROVED => 'wc-badge-success',
                        App\Enums\Wallet\WalletStatus::REJECTED => 'wc-badge-error',
                        default => 'wc-badge-warning',
                    };
                @endphp
                <tr class="animate-wcRowIn wc-wallet-recharge-row" style="animation-delay: {{ $loop->iteration * 0.02 }}s"
                    data-search="{{ mb_strtolower(($recharge_wallet->source ?? '').' '.($recharge_wallet->transaction_id ?? '').' '.number_format((float) $recharge_wallet->amount, 0, ',', '')) }}">
                    <td class="text-wc-muted-2 wc-tabular">{{ ++$i }}</td>
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="wc-avatar !bg-[#ecfdf5] !text-[#059669]"><i class="fas fa-arrow-down text-[12px]"></i></div>
                            <div class="min-w-0">
                                <div class="font-bold text-wc-ink text-[13px]">{{ $recharge_wallet->source }}</div>
                                <div class="text-[11.5px] text-wc-muted-2">{{ dateFormat($recharge_wallet->created_at) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-wc-muted-2 whitespace-nowrap text-[12.5px]">{{ dateFormat($recharge_wallet->created_at) }}</td>
                    <td class="wc-tabular text-[12.5px]">{{ @$recharge_wallet->transaction_id }}</td>
                    <td>
                        <span class="text-[12.5px] text-wc-muted">{{ __('WalletPaymentMethod.'.$recharge_wallet->payment_method) }}</span>
                    </td>
                    <td class="text-right">
                        @if ($isIncome)
                            <span class="text-wc-success font-bold wc-tabular">+ {{ formatPrice(@$recharge_wallet->amount) }}</span>
                        @else
                            <span class="text-wc-danger font-bold wc-tabular">- {{ formatPrice(@$recharge_wallet->amount) }}</span>
                        @endif
                    </td>
                    <td>
                        @if ($isIncome)
                            <span class="wc-badge {{ $badgeClass }}"><i class="fas fa-circle text-[6px] mr-1.5"></i>{{ __('WalletStatus.'.$statusKey) }}</span>
                        @else
                            <span class="wc-badge wc-badge-default">{{ __('parcel.deduction') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr id="wcWalletRechargeNoResult" class="d-none">
                <td colspan="7">
                    <div class="wc-empty !py-10">
                        <div class="wc-empty-icon"><i class="fas fa-filter"></i></div>
                        <p class="wc-empty-title">Aucun résultat</p>
                        <p class="wc-empty-description">Aucune recharge ne correspond à votre recherche.</p>
                    </div>
                </td>
            </tr>
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

@push('scripts')
<script type="text/javascript">
"use strict";
(function () {
    const rows = Array.prototype.slice.call(document.querySelectorAll('.wc-wallet-recharge-row'));
    const searchInput = document.getElementById('wcWalletRechargeSearch');
    const counter = document.getElementById('wcWalletRechargeCounter');
    const noResult = document.getElementById('wcWalletRechargeNoResult');

    function normalize(s) {
        return (s || '').toString().toLowerCase().replace(/\s+/g, ' ').trim();
    }

    function applyFilters() {
        const searchTerm = searchInput ? normalize(searchInput.value) : '';
        let visible = 0;

        rows.forEach(function (row) {
            const matchSearch = searchTerm === '' || normalize(row.dataset.search).indexOf(searchTerm) !== -1;
            row.classList.toggle('d-none', !matchSearch);
            if (matchSearch) visible++;
        });

        if (noResult) noResult.classList.toggle('d-none', visible !== 0);
        if (counter) counter.textContent = visible + ' / ' + rows.length + ' recharges';
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    applyFilters();
})();
</script>
@endpush