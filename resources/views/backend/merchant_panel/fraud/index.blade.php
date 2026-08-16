@extends('backend.partials.master')
@section('title')
    {{ __('fraud.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('fraud.title') }}</h1>
            <p class="wc-page-subtitle">{{ __('fraud.create_fraud') }} · liste des numéros signalés</p>
        </div>
        <div class="wc-toolbar">
            <form action="{{route('merchant-panel.fraud.check')}}" method="POST" class="flex items-center gap-1 m-0">
                @csrf
                <input type="text" name="phone" inputmode="numeric" placeholder="{{ __('merchantPlaceholder.fraud_check') }}" class="wc-input wc-btn-sm !w-[190px]">
                <button type="submit" class="wc-btn wc-btn-primary wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.search') }}"><i class="fas fa-search"></i> {{ __('levels.search') }}</button>
            </form>
            <a href="{{route('merchant-panel.fraud.filter')}}" class="wc-btn wc-btn-outline wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.my_list') }}"><i class="fas fa-clipboard-list"></i> {{ __('levels.my_list') }}</a>
            <a href="{{route('merchant-panel.fraud.create')}}" class="wc-btn wc-btn-primary wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i> {{ __('levels.add') }}</a>
        </div>
    </div>

    @if(count($frauds) === 0)
        <div class="wc-card">
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-user-shield"></i></div>
                <p class="wc-empty-title">Aucune fraude signalée</p>
            </div>
        </div>
    @else
        <div class="space-y-3">
            @foreach($frauds as $fraud)
            <div class="wc-card">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[14px] font-extrabold text-wc-ink">{{ $fraud->name }}</span>
                                <span class="wc-badge wc-badge-danger-soft"><i class="fas fa-flag text-[10px]"></i> {{__('fraud.title')}}</span>
                            </div>
                            <div class="flex items-center gap-4 mt-1.5 flex-wrap">
                                <span class="text-[12.5px] text-wc-muted"><i class="fas fa-phone text-[11px] mr-1"></i><strong class="text-wc-ink">{{ $fraud->phone }}</strong></span>
                                <span class="text-[12.5px] text-wc-muted"><i class="far fa-calendar-alt text-[11px] mr-1"></i>{{ dateFormat($fraud->created_at) }}</span>
                            </div>
                        </div>
                        @if ($fraud->created_by == Auth::user()->id)
                        <div class="flex items-center gap-2">
                            <a href="{{route('merchant-panel.fraud.edit',$fraud->id)}}" class="wc-btn wc-btn-outline wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.edit') }}"><i class="fas fa-edit"></i> {{ __('levels.edit') }}</a>
                            <form id="delete" action="{{route('merchant-panel.fraud.delete',$fraud->id)}}" method="POST" data-title="{{ __('delete.fraud') }}" class="m-0">
                                @method('DELETE')
                                @csrf
                                <input type="hidden" name="" value="{{ __('fraud.title') }}" id="deleteTitle">
                                <button class="wc-btn wc-btn-danger-soft wc-btn-sm" type="submit" data-toggle="tooltip" data-placement="top" title="{{ __('levels.delete') }}"><i class="fa fa-trash"></i> {{ __('levels.delete') }}</button>
                            </form>
                        </div>
                        @endif
                    </div>
                    <div class="border-t border-wc-border mt-3 pt-3">
                        <p class="text-[13px] text-wc-ink-2 leading-relaxed m-0">{{ $fraud->details }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between gap-3 flex-wrap px-1 py-3">
            <p class="m-0 text-[12.5px] text-wc-muted">
                {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $frauds->firstItem() }}</span>
                {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $frauds->lastItem() }}</span>
                {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $frauds->total() }}</span> {!! __('results') !!}
            </p>
            <span class="flex items-center gap-1">{{ $frauds->links() }}</span>
        </div>
    @endif
</div>
@endsection()