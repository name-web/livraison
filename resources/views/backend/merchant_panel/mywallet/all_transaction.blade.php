<div class="flex items-center gap-3 flex-wrap px-4 py-3 border-b border-wc-border bg-[#f8fafc]">
    <div class="relative flex-1 min-w-[220px]">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[13px] pointer-events-none"></i>
        <input type="text" id="wcWalletSearch" class="wc-input !pl-9" placeholder="Rechercher une transaction (source, transaction, montant)...">
    </div>
    <div class="flex items-center gap-1.5" id="wcWalletFilters" role="group" aria-label="Filtrer par statut">
        <button type="button" class="wc-btn wc-btn-primary wc-btn-sm wc-wallet-filter" data-filter="all">{{ __('levels.total') }}</button>
        <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-wallet-filter" data-filter="pending">{{ __('WalletStatus.1') }}</button>
        <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-wallet-filter" data-filter="approved">{{ __('WalletStatus.2') }}</button>
        <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-wallet-filter" data-filter="rejected">{{ __('WalletStatus.3') }}</button>
    </div>
    <span class="text-[12.5px] text-wc-muted wc-tabular whitespace-nowrap" id="wcWalletCounter"></span>
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
        <tbody id="wcWalletBody">
            @php $i = 0; @endphp
            @foreach ($wallets as $wallet)
                @php
                    $isIncome = $wallet->type == App\Enums\Wallet\WalletType::INCOME;
                    $statusKey = (int) $wallet->status;
                    $badgeClass = match($statusKey) {
                        App\Enums\Wallet\WalletStatus::APPROVED => 'wc-badge-success',
                        App\Enums\Wallet\WalletStatus::REJECTED => 'wc-badge-error',
                        default => 'wc-badge-warning',
                    };
                @endphp
                <tr class="animate-wcRowIn wc-wallet-row" style="animation-delay: {{ $loop->iteration * 0.02 }}s"
                    data-search="{{ mb_strtolower(($wallet->source ?? '').' '.($wallet->transaction_id ?? '').' '.number_format((float) $wallet->amount, 0, ',', '')) }}"
                    data-status="{{ $isIncome ? match($statusKey) {
                        App\Enums\Wallet\WalletStatus::PENDING => 'pending',
                        App\Enums\Wallet\WalletStatus::APPROVED => 'approved',
                        default => 'rejected',
                    } : 'expense' }}">
                    <td class="text-wc-muted-2 wc-tabular">{{ ++$i }}</td>
                    <td class="text-wc-ink font-bold text-[13px]">{{ $wallet->source }}</td>
                    <td class="text-wc-muted-2 whitespace-nowrap text-[12.5px]">{{ dateFormat($wallet->created_at) }}</td>
                    <td class="wc-tabular text-[12.5px]">{{ @$wallet->transaction_id }}</td>
                    <td><span class="text-[12.5px] text-wc-muted">{{ __('WalletPaymentMethod.'.$wallet->payment_method) }}</span></td>
                    <td class="text-right">
                        @if ($isIncome)
                            <span class="text-wc-success font-bold wc-tabular">+ {{ formatPrice(@$wallet->amount) }}</span>
                        @else
                            <span class="text-wc-danger font-bold wc-tabular">- {{ formatPrice(@$wallet->amount) }}</span>
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
            <tr id="wcWalletNoResult" class="d-none">
                <td colspan="7">
                    <div class="wc-empty !py-10">
                        <div class="wc-empty-icon"><i class="fas fa-filter"></i></div>
                        <p class="wc-empty-title">Aucun résultat</p>
                        <p class="wc-empty-description">Aucune transaction ne correspond à votre recherche.</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@if(count($wallets) === 0)
    <div class="wc-empty">
        <div class="wc-empty-icon"><i class="fas fa-receipt"></i></div>
        <p class="wc-empty-title">Aucune transaction</p>
        <p class="wc-empty-description">Vos transactions apparaîtront ici.</p>
    </div>
@endif
<div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
    <p class="m-0 text-[12.5px] text-wc-muted">
        {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $wallets->firstItem() }}</span>
        {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $wallets->lastItem() }}</span>
        {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $wallets->total() }}</span> {!! __('results') !!}
    </p>
    <span class="flex items-center gap-1">{{ $wallets->links() }}</span>
</div>

@push('scripts')
<script type="text/javascript">
"use strict";
(function () {
    const rows = Array.prototype.slice.call(document.querySelectorAll('.wc-wallet-row'));
    const searchInput = document.getElementById('wcWalletSearch');
    const counter = document.getElementById('wcWalletCounter');
    const noResult = document.getElementById('wcWalletNoResult');
    const filterBtns = Array.prototype.slice.call(document.querySelectorAll('.wc-wallet-filter'));

    let activeFilter = 'all';
    let searchTerm = '';

    function normalize(s) {
        return (s || '').toString().toLowerCase().replace(/\s+/g, ' ').trim();
    }

    function applyFilters() {
        let visible = 0;
        rows.forEach(function (row) {
            const matchStatus = activeFilter === 'all' || row.dataset.status === activeFilter;
            const matchSearch = searchTerm === '' || normalize(row.dataset.search).indexOf(searchTerm) !== -1;
            const show = matchStatus && matchSearch;
            row.classList.toggle('d-none', !show);
            if (show) visible++;
        });

        if (noResult) noResult.classList.toggle('d-none', visible !== 0);
        if (counter) counter.textContent = visible + ' / ' + rows.length + ' transactions';
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            searchTerm = normalize(searchInput.value);
            applyFilters();
        });
    }

    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterBtns.forEach(function (b) {
                b.classList.remove('wc-btn-primary');
                b.classList.add('wc-btn-soft');
            });
            btn.classList.remove('wc-btn-soft');
            btn.classList.add('wc-btn-primary');
            activeFilter = btn.dataset.filter;
            applyFilters();
        });
    });

    applyFilters();
})();
</script>
@endpush