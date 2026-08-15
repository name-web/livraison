@extends('backend.partials.master')
@section('title')
    {{ __('merchant.merchant_dashboard') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">
    {{-- Filter form (server-side PRG) --}}
    <form action="{{ route('merchant-panel.dashboard.filter') }}" method="POST" id="wcFilterForm" class="hidden">
        @csrf
        <input type="hidden" name="date" id="wcFilterDate" value="{{ $dateRange }}">
    </form>

    {{-- Mount React dashboard --}}
    <div id="merchant-app"></div>

    @php
        $periodData = null;
        if ($period) {
            $periodData = [
                'from' => \Carbon\Carbon::parse($period['from'])->format('d/m/Y'),
                'to'   => \Carbon\Carbon::parse($period['to'])->format('d/m/Y'),
            ];
        }
        $merchantData = [
            'name'           => $merchant->business_name,
            'currency'       => $currency,
            'walletBalance'  => (float) $merchant->wallet_balance,
            'currentBalance' => (float) $merchant->current_balance,
            'openingBalance' => (float) $merchant->opening_balance,
            'vat'            => (float) $merchant->vat,
        ];
    @endphp

    <script type="application/json" id="merchant-props">
        {!! json_encode([
            'merchant' => $merchantData,
            'counts'   => $data['counts'],
            'amounts'  => $data['amounts'],
            'sales'    => $data['sales'],
            'payments' => $data['payments'],
            'series'   => $series,
            'recent'   => $recentParcels,
            'period'   => $periodData,
            'urls'     => [
                'filter'      => route('merchant-panel.dashboard.filter'),
                'create'      => route('merchant-panel.parcel.create'),
                'export'      => route('merchant-panel.parcel.file-export'),
                'shops'       => route('merchant-panel.shops.index'),
                'parcelBank'  => route('merchant-panel.parcel-bank.index'),
                'parcelIndex' => route('merchant-panel.parcel.index'),
                'wallet'      => route('merchant-panel.my.wallet.index'),
                'transaction' => route('merchant.accounts.account-transaction.index'),
                'statements'  => route('merchant.accounts.statements.index'),
            ],
        ]) !!}
    </script>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush