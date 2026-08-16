@extends('backend.partials.master')
@section('title')
    {{ __('news_offer.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('news_offer.title') }}</h1>
            <p class="wc-page-subtitle">Actualités et offres de la plateforme</p>
        </div>
    </div>

    @if(count($news_offers) === 0)
        <div class="wc-card">
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-newspaper"></i></div>
                <p class="wc-empty-title">Aucune actualité</p>
            </div>
        </div>
    @else
        <div class="space-y-4 !max-w-[860px] mx-auto">
            @foreach($news_offers as $news_offer)
                <div class="wc-card overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-wc-border bg-wc-surface-soft/60">
                        <h3 class="text-[15px] font-extrabold text-wc-ink m-0">{{ $news_offer->title }}</h3>
                        <p class="text-[12px] text-wc-muted mt-1 mb-0">
                            {{ __('levels.author') }}: <span class="font-semibold text-wc-ink">{{$news_offer->user->name}}</span>
                            <span class="mx-1.5 text-wc-border-strong">|</span>
                            {{ __('levels.date') }}: {{dateFormat($news_offer->date) }}
                        </p>
                    </div>
                    <div class="p-4 sm:p-5">
                        @if ($news_offer->upload->original != "")
                            <img src="{{$news_offer->image}}" class="rounded-lg border border-wc-border object-cover w-full mb-4" alt="Image" height="350">
                        @endif
                        <div class="text-[13.5px] text-wc-ink-2 leading-relaxed">{!! $news_offer->description !!}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection