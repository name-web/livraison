@extends('backend.partials.master')
@section('title')
    {{ __('news_offer.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Header --}}
    <div class="wc-page-header animate-wcFadeUp">
        <div>
            <h1 class="wc-page-title">{{ __('news_offer.title') }}</h1>
            <p class="wc-page-subtitle">Actualités et offres de la plateforme.</p>
        </div>
    </div>

    {{-- Recherche --}}
    <div class="wc-nw-search animate-wcFadeUp" style="animation-delay:.03s">
        <div class="relative flex-1 min-w-[220px]">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[12px] pointer-events-none"></i>
            <input type="text" id="wcNwSearch" class="wc-input !pl-9 !py-2 !text-[12.5px]" placeholder="Rechercher une actualité...">
        </div>
        <span class="text-[12.5px] text-wc-muted wc-tabular whitespace-nowrap" id="wcNwCounter"></span>
    </div>

    @if($news_offers->isEmpty())
        <div class="wc-card">
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-newspaper"></i></div>
                <p class="wc-empty-title">Aucune actualité</p>
            </div>
        </div>
    @else
        <div class="wc-nw-grid animate-wcFadeUp" style="animation-delay:.06s">
            @foreach($news_offers as $n)
                @php
                    $hasImage = !empty($n->upload?->original) && $n->upload->original !== '';
                    $imgUrl   = $hasImage ? $n->image : '';
                    $searchText = e(($n->title ?? '').' '.($n->user->name ?? '').' '.strip_tags($n->description ?? ''));
                @endphp
                <div class="wc-nw-card" data-search="{{ $searchText }}">
                    {{-- Image hero --}}
                    <div class="wc-nw-card-img">
                        @if($hasImage)
                            <img src="{{ $imgUrl }}" alt="{{ $n->title }}" loading="lazy">
                        @else
                            <div class="wc-nw-card-img-placeholder">
                                <i class="fas fa-newspaper"></i>
                            </div>
                        @endif
                        <div class="wc-nw-card-date">
                            <i class="fas fa-calendar-day"></i>
                            {{ \Carbon\Carbon::parse($n->date ?? $n->created_at)->format('d M') }}
                        </div>
                    </div>

                    {{-- Corps --}}
                    <div class="wc-nw-card-body">
                        <h3 class="wc-nw-card-title">{{ $n->title }}</h3>
                        <p class="wc-nw-card-desc">{!! Str::limit(strip_tags($n->description ?? ''), 120) !!}</p>
                        <div class="wc-nw-card-footer">
                            <div class="wc-nw-card-author">
                                <div class="wc-nw-card-avatar">
                                    {{ strtoupper(substr($n->user->name ?? 'A', 0, 1)) }}
                                </div>
                                <span>{{ $n->user->name ?? '—' }}</span>
                            </div>
                            <button class="wc-btn wc-btn-soft wc-btn-sm wc-nw-toggle" data-index="{{ $loop->index }}">
                                <i class="fas fa-expand-alt text-[10px]"></i> Lire
                            </button>
                        </div>
                    </div>

                    {{-- Détail (expandable) --}}
                    <div class="wc-nw-card-detail" id="wcNwDetail{{ $loop->index }}">
                        <div class="wc-nw-card-detail-inner">
                            <div class="text-[13.5px] text-wc-ink-2 leading-relaxed">{!! $n->description !!}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-center mt-6 animate-wcFadeUp" style="animation-delay:.09s">
            {{ $news_offers->links() }}
        </div>
    @endif
</div>
@endsection()

@push('styles')
<style>
/* ─── Search ─── */
.wc-nw-search {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #fff;
    border: 1px solid #e7ebe9;
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 18px;
}

/* ─── Grid ─── */
.wc-nw-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

/* ─── Card ─── */
.wc-nw-card {
    background: #fff;
    border: 1px solid #e7ebe9;
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform .15s ease, box-shadow .15s ease;
}
.wc-nw-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(15,23,42,.08);
}

/* ─── Image hero ─── */
.wc-nw-card-img {
    position: relative;
    width: 100%;
    height: 180px;
    overflow: hidden;
    background: #f1f5f9;
}
.wc-nw-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .3s ease;
}
.wc-nw-card:hover .wc-nw-card-img img {
    transform: scale(1.04);
}
.wc-nw-card-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: #cbd5e1;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
}

.wc-nw-card-date {
    position: absolute;
    top: 10px;
    right: 10px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(6px);
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.wc-nw-card-date i { font-size: 10px; color: #64748b; }

/* ─── Body ─── */
.wc-nw-card-body {
    flex: 1;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
}

.wc-nw-card-title {
    font-size: 15px;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 6px;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.wc-nw-card-desc {
    font-size: 12.5px;
    color: #64748b;
    line-height: 1.55;
    margin: 0 0 auto;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.wc-nw-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}

.wc-nw-card-author {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
}
.wc-nw-card-avatar {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: linear-gradient(135deg, #059669, #10b981);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    flex-shrink: 0;
}

/* ─── Detail expandable ─── */
.wc-nw-card-detail {
    max-height: 0;
    overflow: hidden;
    transition: max-height .35s ease;
}
.wc-nw-card.is-open .wc-nw-card-detail {
    max-height: 600px;
}
.wc-nw-card-detail-inner {
    padding: 0 18px 18px;
    border-top: 1px dashed #e7ebe9;
    padding-top: 14px;
}

.wc-nw-card.is-hidden { display: none; }

@media (max-width: 991.98px) {
    .wc-nw-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 575.98px) {
    .wc-nw-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@push('scripts')
<script>
"use strict";
(function () {
    var search  = document.getElementById('wcNwSearch');
    var cards   = Array.prototype.slice.call(document.querySelectorAll('.wc-nw-card'));
    var counter = document.getElementById('wcNwCounter');
    var toggles = Array.prototype.slice.call(document.querySelectorAll('.wc-nw-toggle'));

    function norm(s) { return (s || '').toLowerCase().replace(/\s+/g, ' ').trim(); }

    function applySearch() {
        var term = search ? norm(search.value) : '';
        var visible = 0;
        cards.forEach(function (c) {
            var match = term === '' || norm(c.dataset.search).indexOf(term) !== -1;
            c.classList.toggle('is-hidden', !match);
            if (match) visible++;
        });
        if (counter) counter.textContent = visible + ' actualité' + (visible !== 1 ? 's' : '');
    }

    toggles.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var card = btn.closest('.wc-nw-card');
            var isOpen = card.classList.contains('is-open');
            card.classList.toggle('is-open', !isOpen);
            btn.innerHTML = isOpen
                ? '<i class="fas fa-expand-alt text-[10px]"></i> Lire'
                : '<i class="fas fa-compress-alt text-[10px]"></i> Réduire';
        });
    });

    if (search) search.addEventListener('input', applySearch);
    applySearch();
})();
</script>
@endpush
