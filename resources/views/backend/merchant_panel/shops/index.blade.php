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

    {{-- KPI : statistiques des points de ramassage --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.02s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('levels.total') }}</p>
                <div class="wc-card-icon bg-[#eef1f5] text-[#334155]"><i class="fas fa-store"></i></div>
            </div>
            <p class="wc-kpi-value m-0" id="wcShopStatTotal">{{ $stats['total'] }}</p>
            <p class="wc-kpi-sub neutral m-0">{{ __('merchantshops.title') }}</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.06s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('merchantshops.active') }}</p>
                <div class="wc-card-icon bg-[#ecfdf5] text-[#059669]"><i class="fas fa-check-circle"></i></div>
            </div>
            <p class="wc-kpi-value m-0" id="wcShopStatActive">{{ $stats['active'] }}</p>
            <p class="wc-kpi-sub positive m-0"><i class="fas fa-circle text-[6px]"></i> {{ __('levels.active') }}</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.1s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('merchantshops.inactive') }}</p>
                <div class="wc-card-icon bg-[#fef2f2] text-[#dc2626]"><i class="fas fa-pause-circle"></i></div>
            </div>
            <p class="wc-kpi-value m-0" id="wcShopStatInactive">{{ $stats['inactive'] }}</p>
            <p class="wc-kpi-sub neutral m-0"><i class="fas fa-circle text-[6px]"></i> {{ __('levels.inactive') }}</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.14s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">{{ __('merchantshops.default') }}</p>
                <div class="wc-card-icon bg-[#fffbeb] text-[#d97706]"><i class="fas fa-star"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ $stats['default'] }}</p>
            <p class="wc-kpi-sub neutral m-0">{{ __('merchantshops.add_default') }}</p>
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

        {{-- Barre d'outils interactive : recherche + filtres --}}
        <div class="flex items-center gap-3 flex-wrap px-4 py-3 border-b border-wc-border bg-[#f8fafc]">
            <div class="relative flex-1 min-w-[220px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[13px] pointer-events-none"></i>
                <input type="text" id="wcShopSearch" class="wc-input !pl-9" placeholder="Rechercher un point de ramassage (nom, contact, adresse)...">
            </div>
            <div class="flex items-center gap-1.5" id="wcShopFilters" role="group" aria-label="Filtrer par statut">
                <button type="button" class="wc-btn wc-btn-primary wc-btn-sm wc-shop-filter" data-filter="all">{{ __('levels.total') }}</button>
                <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-shop-filter" data-filter="active">{{ __('merchantshops.active') }}</button>
                <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-shop-filter" data-filter="inactive">{{ __('merchantshops.inactive') }}</button>
            </div>
            <span class="text-[12.5px] text-wc-muted wc-tabular whitespace-nowrap" id="wcShopCounter"></span>
        </div>

        @if(count($merchant_shops) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-store"></i></div>
                <p class="wc-empty-title">Aucun magasin</p>
                <p class="wc-empty-description">Ajoutez un magasin pour organiser vos points de dépôt.</p>
                <a href="{{route('merchant-panel.shops.create')}}" class="wc-btn wc-btn-primary wc-btn-sm mt-2"><i class="fa fa-plus"></i> {{ __('levels.add') }}</a>
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
                    <tbody id="wcShopBody">
                        @php $i=1; @endphp
                        @foreach($merchant_shops as $shop)
                            @php
                                $phoneDigits = preg_replace('/\D/', '', $shop->contact_no);
                                $initials = strtoupper(mb_substr(trim($shop->name), 0, 2));
                                $hasCoords = !empty($shop->merchant_lat) && !empty($shop->merchant_long);
                                $isActive = $shop->status == \App\Enums\Status::ACTIVE;
                            @endphp
                            <tr class="animate-wcRowIn wc-shop-row" style="animation-delay: {{ $loop->iteration * 0.03 }}s"
                                data-search="{{ mb_strtolower($shop->name.' '.$shop->contact_no.' '.$shop->address) }}"
                                data-status="{{ $isActive ? 'active' : 'inactive' }}">
                                <td class="text-wc-muted-2 wc-tabular">{{$i++}}</td>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="wc-avatar {{ $isActive ? '!bg-[#ecfdf5] !text-[#059669]' : '!bg-[#eef1f5] !text-[#64748b]' }}">{{$initials}}</div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-wc-ink text-[13px]">{{$shop->name}}</span>
                                                @if($shop->default_shop == \App\Enums\Status::ACTIVE)
                                                    <span class="wc-badge wc-badge-default"><i class="fas fa-star text-[9px]"></i> {{ __('merchantshops.default') }}</span>
                                                @endif
                                            </div>
                                            <div class="text-[11.5px] text-wc-muted-2 wc-tabular">#{{ $shop->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <a href="tel:{{$shop->contact_no}}" class="text-[13px] font-semibold text-wc-ink-2 wc-tabular hover:text-wc-primary" title="Appeler">
                                            <i class="fas fa-phone text-[11px] text-wc-muted-2 mr-1"></i>{{$shop->contact_no}}
                                        </a>
                                        <button type="button" class="wc-icon-btn wc-copy-btn" data-copy="{{$shop->contact_no}}" title="Copier le contact">
                                            <i class="fas fa-copy text-[11px]"></i>
                                        </button>
                                        @if($phoneDigits)
                                            <a href="https://wa.me/{{$phoneDigits}}" target="_blank" rel="noopener" class="wc-icon-btn !text-[#059669]" title="WhatsApp">
                                                <i class="fab fa-whatsapp text-[11px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[12.5px] text-wc-ink-2 max-w-[240px] truncate">{{$shop->address}}</span>
                                        <button type="button" class="wc-icon-btn wc-copy-btn" data-copy="{{$shop->address}}" title="Copier l'adresse">
                                            <i class="fas fa-copy text-[11px]"></i>
                                        </button>
                                        @if($hasCoords)
                                            <a href="https://www.google.com/maps?q={{$shop->merchant_lat}},{{$shop->merchant_long}}" target="_blank" rel="noopener" class="wc-icon-btn !text-[#2563eb]" title="Voir sur la carte">
                                                <i class="fas fa-map-marker-alt text-[11px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($isActive)
                                        <span class="wc-badge wc-badge-success">{{ __('merchantshops.active') }}</span>
                                    @else
                                        <span class="wc-badge wc-badge-error">{{ __('merchantshops.inactive') }}</span>
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
                        <tr id="wcShopNoResult" class="d-none">
                            <td colspan="6">
                                <div class="wc-empty !py-10">
                                    <div class="wc-empty-icon"><i class="fas fa-filter"></i></div>
                                    <p class="wc-empty-title">Aucun résultat</p>
                                    <p class="wc-empty-description">Aucun point de ramassage ne correspond à votre recherche ou filtre.</p>
                                </div>
                            </td>
                        </tr>
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

@push('scripts')
<style>
    .wc-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 8px;
        border: 1px solid var(--wc-border);
        background: #fff;
        color: var(--wc-muted-2);
        cursor: pointer;
        transition: all .15s ease;
        flex-shrink: 0;
        padding: 0;
    }
    .wc-icon-btn:hover {
        border-color: #cde9dc;
        color: var(--wc-primary);
        transform: translateY(-1px);
        box-shadow: var(--wc-shadow-xs);
    }
    .wc-icon-btn.copied {
        border-color: #a7f3d0;
        background: #ecfdf5;
        color: #059669;
    }
</style>
<script type="text/javascript">
"use strict";
(function () {
    const rows = Array.prototype.slice.call(document.querySelectorAll('.wc-shop-row'));
    const body = document.getElementById('wcShopBody');
    const searchInput = document.getElementById('wcShopSearch');
    const counter = document.getElementById('wcShopCounter');
    const noResult = document.getElementById('wcShopNoResult');
    const filterBtns = Array.prototype.slice.call(document.querySelectorAll('.wc-shop-filter'));

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
        if (counter) {
            const total = rows.length;
            counter.textContent = visible + ' / ' + total + ' affichés';
        }
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

    function copyText(text, btn) {
        function done() {
            const icon = btn.querySelector('i');
            if (icon) {
                const prev = icon.className;
                icon.className = 'fas fa-check';
                btn.classList.add('copied');
                setTimeout(function () {
                    icon.className = prev;
                    btn.classList.remove('copied');
                }, 1200);
            }
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done, done);
        } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(ta);
            done();
        }
    }

    Array.prototype.slice.call(document.querySelectorAll('.wc-copy-btn')).forEach(function (btn) {
        btn.addEventListener('click', function () {
            copyText(btn.dataset.copy || '', btn);
        });
    });

    applyFilters();
})();
</script>
@endpush