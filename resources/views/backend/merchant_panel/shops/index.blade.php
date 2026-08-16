@extends('backend.partials.master')
@section('title')
    {{ __('merchantshops.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('merchantshops.title') }}</h1>
            <p class="wc-page-subtitle">{{ __('merchantshops.create_shops') }} · points de dépôt de vos colis</p>
        </div>
        <div class="wc-toolbar">
            <a href="{{route('merchant-panel.shops.create')}}" class="wc-btn wc-btn-primary wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i> {{ __('levels.add') }}</a>
        </div>
    </div>

    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-store"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('merchantshops.title') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Vos magasins et points de vente.</p>
                </div>
            </div>
        </div>

        @if(count($merchant_shops) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-store"></i></div>
                <p class="wc-empty-title">Aucun magasin</p>
                <p class="wc-empty-description">Ajoutez un magasin pour organiser vos points de dépôt.</p>
            </div>
        @else
            <div class="wc-table-wrap">
                <table class="wc-table">
                    <thead>
                        <tr>
                            <th>{{ __('levels.id') }}</th>
                            <th>{{ __('merchantshops.name') }}</th>
                            <th>{{ __('merchantshops.contact') }}</th>
                            <th>{{ __('merchantshops.address') }}</th>
                            <th>{{ __('levels.status') }}</th>
                            <th class="text-right">{{ __('levels.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @foreach($merchant_shops as $shop)
                            <tr>
                                <td class="text-wc-muted-2 wc-tabular">{{$i++}}</td>
                                <td>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-wc-ink text-[13px]">{{$shop->name}}</span>
                                        <span class="text-[12px] text-wc-muted-2">(#{{ $shop->id }})</span>
                                    </div>
                                </td>
                                <td class="wc-tabular text-wc-ink-2">{{$shop->contact_no}}</td>
                                <td class="text-[12.5px] text-wc-ink-2 max-w-[260px]">{{$shop->address}}</td>
                                <td>
                                    @if($shop->status == \App\Enums\Status::ACTIVE)
                                        <span class="wc-badge wc-badge-success">{{ __('merchantshops.active') }}</span>
                                    @else
                                        <span class="wc-badge wc-badge-danger">{{ __('merchantshops.inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{route('merchant-panel.shops.edit',$shop->id)}}" class="wc-btn wc-btn-outline wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.edit') }}"><i class="fas fa-edit"></i> {{ __('levels.edit') }}</a>
                                        <form id="delete" value="Test" action="{{route('merchant-panel.shops.delete',$shop->id)}}" method="POST" data-title="{{ __('delete.shop') }}" class="m-0">
                                            @method('DELETE')
                                            @csrf
                                            <input type="hidden" name="" value="Merchant Shop" id="deleteTitle">
                                            <button type="submit" class="wc-btn wc-btn-danger-soft wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.delete') }}"><i class="fa fa-trash"></i> {{ __('levels.delete') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $merchant_shops->firstItem() }}</span>
                    {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $merchant_shops->lastItem() }}</span>
                    {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $merchant_shops->total() }}</span> {!! __('results') !!}
                </p>
                <span class="flex items-center gap-1">{{ $merchant_shops->links() }}</span>
            </div>
        @endif
    </div>
</div>
@endsection()