@extends('backend.partials.master')
@section('title')
    {{ __('delivery_charge.cod_charges') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('delivery_charge.cod_charges') }}</h1>
            <p class="wc-page-subtitle">{{ __('menus.settings') }} · frais de contre-remboursement par zone</p>
        </div>
    </div>

    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-percent"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('delivery_charge.cod_charges') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Frais appliqués sur les paiements à la livraison.</p>
                </div>
            </div>
        </div>
        <div class="wc-table-wrap">
            <table class="wc-table">
                <thead>
                    <tr>
                        <th>{{ __('levels.id') }}</th>
                        <th>{{ __('settings.location') }}</th>
                        <th class="text-right">{{ __('settings.charges') }} (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                    @foreach($cod_charges->cod_charges as $key=>$charge)
                    <tr>
                        <td class="text-wc-muted-2 wc-tabular">{{$i++}}</td>
                        <td class="font-bold text-wc-ink text-[13px]">{{ __('merchant.'.$key) }}</td>
                        <td class="text-right wc-tabular"><span class="wc-badge wc-badge-info-soft">{{$charge}}%</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection()