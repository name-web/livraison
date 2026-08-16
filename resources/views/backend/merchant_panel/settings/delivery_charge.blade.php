@extends('backend.partials.master')
@section('title')
    {{ __('delivery_charge.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('delivery_charge.title') }}</h1>
            <p class="wc-page-subtitle">{{ __('menus.settings') }} · tarifs de livraison par catégorie et zone</p>
        </div>
    </div>

    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('delivery_charge.title') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Frais en {{ settings()->currency }}.</p>
                </div>
            </div>
        </div>

        @if(count($delivery_charges) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-truck"></i></div>
                <p class="wc-empty-title">Aucun tarif de livraison</p>
            </div>
        @else
            <div class="wc-table-wrap">
                <table class="wc-table">
                    <thead>
                        <tr>
                            <th>{{ __('levels.id') }}</th>
                            <th>{{ __('levels.category') }}</th>
                            <th class="text-right">{{ __('levels.weight') }}</th>
                            <th class="text-right">{{ __('levels.same_day') }}</th>
                            <th class="text-right">{{ __('levels.next_day') }}</th>
                            <th class="text-right">{{ __('levels.sub_city') }}</th>
                            <th class="text-right">{{ __('levels.outside_city') }}</th>
                            <th>{{ __('levels.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @foreach($delivery_charges as $delivery_charge)
                        <tr>
                            <td class="text-wc-muted-2 wc-tabular">{{$i++}}</td>
                            <td class="font-bold text-wc-ink text-[13px]">{{$delivery_charge->deliveryCharge->category->title}}</td>
                            <td class="text-right wc-tabular text-wc-muted-2">{{$delivery_charge->deliveryCharge->weight ?? 0}} kg</td>
                            <td class="text-right wc-tabular font-semibold text-wc-ink">{{ formatPrice($delivery_charge->same_day) }}</td>
                            <td class="text-right wc-tabular font-semibold text-wc-ink">{{ formatPrice($delivery_charge->next_day) }}</td>
                            <td class="text-right wc-tabular font-semibold text-wc-ink">{{ formatPrice($delivery_charge->sub_city) }}</td>
                            <td class="text-right wc-tabular font-semibold text-wc-ink">{{ formatPrice($delivery_charge->outside_city) }}</td>
                            <td>{!! $delivery_charge->my_status !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $delivery_charges->firstItem() }}</span>
                    {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $delivery_charges->lastItem() }}</span>
                    {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $delivery_charges->total() }}</span> {!! __('results') !!}
                </p>
                <span class="flex items-center gap-1">{{ $delivery_charges->links() }}</span>
            </div>
        @endif
    </div>
</div>
@endsection()