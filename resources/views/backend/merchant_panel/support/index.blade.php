@extends('backend.partials.master')
@section('title')
    {{ __('support.supprot') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Header --}}
    <div class="wc-page-header animate-wcFadeUp">
        <div>
            <h1 class="wc-page-title">{{ __('support.supprot') }}</h1>
            <p class="wc-page-subtitle">Vos demandes d'assistance et conversations.</p>
        </div>
        <div class="wc-toolbar">
            <a href="{{ route('merchant-panel.support.add') }}" class="wc-btn wc-btn-primary wc-btn-sm">
                <i class="fas fa-plus"></i> {{ __('support.supprot_add') }}
            </a>
        </div>
    </div>

    {{-- Filtre statut + recherche --}}
    <div class="wc-sp-bar animate-wcFadeUp" style="animation-delay:.03s">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[12px] pointer-events-none"></i>
            <input type="text" id="wcSpSearch" class="wc-input !pl-9 !py-2 !text-[12.5px]" placeholder="Rechercher un ticket...">
        </div>
        <div class="flex items-center gap-1.5" id="wcSpFilters">
            <button type="button" class="wc-btn wc-btn-primary wc-btn-sm wc-sp-filter active" data-filter="all">Tous</button>
            <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-sp-filter" data-filter="pending">
                <span class="wc-sp-dot wc-sp-dot-pending"></span> En attente
            </button>
            <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-sp-filter" data-filter="processing">
                <span class="wc-sp-dot wc-sp-dot-processing"></span> En cours
            </button>
            <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-sp-filter" data-filter="resolved">
                <span class="wc-sp-dot wc-sp-dot-resolved"></span> Résolu
            </button>
            <button type="button" class="wc-btn wc-btn-soft wc-btn-sm wc-sp-filter" data-filter="closed">
                <span class="wc-sp-dot wc-sp-dot-closed"></span> Fermé
            </button>
        </div>
        <span class="text-[12.5px] text-wc-muted wc-tabular whitespace-nowrap" id="wcSpCounter"></span>
    </div>

    {{-- Liste tickets --}}
    @if($supports->isEmpty())
        <div class="wc-card">
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-headset"></i></div>
                <p class="wc-empty-title">Aucun ticket</p>
                <p class="wc-empty-description">Créez un ticket pour contacter l'assistance.</p>
                <a href="{{ route('merchant-panel.support.add') }}" class="wc-btn wc-btn-primary wc-btn-sm mt-3">
                    <i class="fas fa-plus"></i> {{ __('support.supprot_add') }}
                </a>
            </div>
        </div>
    @else
        <div class="wc-sp-list animate-wcFadeUp" style="animation-delay:.06s">
            @foreach($supports as $s)
                @php
                    $statusKey = match((int) $s->status) {
                        \App\Enums\SupportStatus::PENDING    => 'pending',
                        \App\Enums\SupportStatus::PROCESSING => 'processing',
                        \App\Enums\SupportStatus::RESOLVED   => 'resolved',
                        \App\Enums\SupportStatus::CLOSED     => 'closed',
                        default => 'pending',
                    };
                    $chatCount = $s->supportChats()->count();
                    $searchText = e(($s->subject ?? '').' '.($s->user->name ?? '').' '.($s->department->title ?? '').' '.($s->service ?? ''));
                @endphp
                <div class="wc-sp-card" data-status="{{ $statusKey }}" data-search="{{ $searchText }}">
                    {{-- Barre latérale statut --}}
                    <div class="wc-sp-card-bar wc-sp-bar-{{ $statusKey }}"></div>

                    {{-- Contenu --}}
                    <div class="wc-sp-card-body">
                        <div class="wc-sp-card-top">
                            <div class="wc-sp-card-info">
                                <a href="{{ route('merchant-panel.support.view', $s->id) }}" class="wc-sp-card-subject">
                                    {{ $s->subject }}
                                </a>
                                <div class="wc-sp-card-meta">
                                    <span class="wc-sp-chip {{ $s->status_color }}">{{ $s->status_label }}</span>
                                    @if($s->priority)
                                        <span class="wc-sp-prio {{ $s->priority_color }}">{{ $s->priority }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="wc-sp-card-actions">
                                <a href="{{ route('merchant-panel.support.view', $s->id) }}" class="wc-btn wc-btn-soft wc-btn-sm" title="{{ __('levels.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('merchant-panel.support.edit', $s->id) }}" class="wc-btn wc-btn-soft wc-btn-sm" title="{{ __('levels.edit') }}">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('merchant-panel.support.delete', $s->id) }}" method="POST" class="m-0" onsubmit="return confirm('Supprimer ce ticket ?')">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="wc-btn wc-btn-danger-soft wc-btn-sm" title="{{ __('levels.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="wc-sp-card-bottom">
                            <span class="wc-sp-card-dept">
                                <i class="fas fa-building"></i> {{ $s->department->title ?? '—' }}
                            </span>
                            <span class="wc-sp-card-service">
                                <i class="fas fa-cog"></i> {{ $s->service ?? '—' }}
                            </span>
                            @if($chatCount > 0)
                                <span class="wc-sp-card-chat">
                                    <i class="fas fa-comments"></i> {{ $chatCount }}
                                </span>
                            @endif
                            <span class="wc-sp-card-date">
                                <i class="fas fa-calendar"></i> {{ dateFormat($s->date) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach

            <div id="wcSpEmpty" class="d-none">
                <div class="wc-empty !py-10">
                    <div class="wc-empty-icon"><i class="fas fa-search"></i></div>
                    <p class="wc-empty-title">Aucun résultat</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection()

@push('styles')
<style>
/* ─── Filter bar ─── */
.wc-sp-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    border: 1px solid #e7ebe9;
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}

.wc-sp-filter { gap: 5px; }
.wc-sp-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}
.wc-sp-dot-pending    { background: #f59e0b; }
.wc-sp-dot-processing { background: #3b82f6; }
.wc-sp-dot-resolved   { background: #10b981; }
.wc-sp-dot-closed     { background: #94a3b8; }

/* ─── Ticket list ─── */
.wc-sp-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* ─── Ticket card ─── */
.wc-sp-card {
    display: flex;
    background: #fff;
    border: 1px solid #e7ebe9;
    border-radius: 14px;
    overflow: hidden;
    transition: transform .12s ease, box-shadow .12s ease;
}
.wc-sp-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(15,23,42,.06);
}

.wc-sp-card-bar {
    width: 5px;
    flex-shrink: 0;
}
.wc-sp-bar-pending    { background: #f59e0b; }
.wc-sp-bar-processing { background: #3b82f6; }
.wc-sp-bar-resolved   { background: #10b981; }
.wc-sp-bar-closed     { background: #cbd5e1; }

.wc-sp-card-body {
    flex: 1;
    padding: 14px 18px;
    min-width: 0;
}

.wc-sp-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.wc-sp-card-info { flex: 1; min-width: 0; }

.wc-sp-card-subject {
    font-size: 14px;
    font-weight: 800;
    color: #1e293b;
    text-decoration: none;
    display: block;
    line-height: 1.35;
    transition: color .1s ease;
}
.wc-sp-card-subject:hover { color: #059669; }

.wc-sp-card-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    flex-wrap: wrap;
}

.wc-sp-chip {
    display: inline-flex;
    align-items: center;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 3px 10px;
    border-radius: 6px;
}
.wc-st-chip-pending    { background: #fffbeb; color: #b45309; }
.wc-st-chip-processing { background: #eff6ff; color: #1d4ed8; }
.wc-st-chip-resolved   { background: #ecfdf5; color: #047857; }
.wc-st-chip-closed     { background: #f1f5f9; color: #64748b; }

.wc-sp-prio {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.wc-st-prio-high   { background: #fef2f2; color: #dc2626; }
.wc-st-prio-medium { background: #fffbeb; color: #b45309; }
.wc-st-prio-low    { background: #f0fdf4; color: #16a34a; }

.wc-sp-card-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

/* ─── Bottom meta ─── */
.wc-sp-card-bottom {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px dashed #e7ebe9;
    flex-wrap: wrap;
}
.wc-sp-card-dept,
.wc-sp-card-service,
.wc-sp-card-chat,
.wc-sp-card-date {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11.5px;
    font-weight: 600;
    color: #94a3b8;
}
.wc-sp-card-dept i, .wc-sp-card-service i,
.wc-sp-card-chat i, .wc-sp-card-card-date i { font-size: 10px; }

.wc-sp-card-chat {
    color: #64748b;
    font-weight: 700;
}

.wc-sp-card.is-hidden { display: none; }

@media (max-width: 640px) {
    .wc-sp-bar { flex-direction: column; }
    .wc-sp-card-top { flex-direction: column; }
    .wc-sp-card-actions { justify-content: flex-start; }
}
</style>
@endpush

@push('scripts')
<script>
"use strict";
(function () {
    var search = document.getElementById('wcSpSearch');
    var cards  = Array.prototype.slice.call(document.querySelectorAll('.wc-sp-card'));
    var btns   = Array.prototype.slice.call(document.querySelectorAll('.wc-sp-filter'));
    var counter= document.getElementById('wcSpCounter');
    var empty  = document.getElementById('wcSpEmpty');
    var active = 'all';

    function norm(s) { return (s || '').toLowerCase().replace(/\s+/g, ' ').trim(); }

    function applyFilters() {
        var term = search ? norm(search.value) : '';
        var visible = 0;
        cards.forEach(function (c) {
            var matchStatus = active === 'all' || c.dataset.status === active;
            var matchSearch = term === '' || norm(c.dataset.search).indexOf(term) !== -1;
            var show = matchStatus && matchSearch;
            c.classList.toggle('is-hidden', !show);
            if (show) visible++;
        });
        if (counter) counter.textContent = visible + ' ticket' + (visible !== 1 ? 's' : '');
        if (empty) empty.classList.toggle('d-none', visible > 0);
    }

    btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            btns.forEach(function (b) {
                b.classList.remove('active', 'wc-btn-primary');
                b.classList.add('wc-btn-soft');
            });
            btn.classList.add('active', 'wc-btn-primary');
            btn.classList.remove('wc-btn-soft');
            active = btn.dataset.filter;
            applyFilters();
        });
    });

    if (search) search.addEventListener('input', applyFilters);
    applyFilters();
})();
</script>
@endpush
