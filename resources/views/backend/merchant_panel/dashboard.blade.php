@extends('backend.partials.master')
@section('title')
    {{ __('merchant.merchant_dashboard') }}
@endsection
@section('maincontent')
@php
    $counts   = $data['counts'];
    $sales    = $data['sales'];

    $total      = (int) $counts['total'];
    $delivered  = (int) $counts['delivered'];
    $onGoing    = (int) $counts['on_going'];
    $shopsCount = (int) $counts['shops'];

    $successRate = $total > 0 ? ($delivered / $total) * 100 : 0;

    $hour = (int) now()->format('G');
    $hello = $hour >= 18 ? __('sidebar.dashboard_hello_evening') : __('sidebar.dashboard_hello');

    $last7Dates   = array_slice($series['dates'], -7);
    $last7Totals  = array_slice($series['totals'], -7);

    $zoneMax = collect($shops)->max('parcels') ?? 0;
@endphp

<div class="container-fluid dashboard-content">

    {{-- Filter form (server-side PRG) --}}
    <form action="{{ route('merchant-panel.dashboard.filter') }}" method="POST" id="wcFilterForm" class="hidden">
        @csrf
        <input type="hidden" name="date" id="wcFilterDate" value="{{ $dateRange }}">
    </form>

    <div class="gc-greeting">
        <div>
            <h1>{{ $hello }}, {{ Auth::user()->name }}</h1>
            <p>{{ __('sidebar.dashboard_overview') }}</p>
        </div>
        <button type="button" id="gcDateBtn" class="gc-datebtn">
            <i class="fas fa-calendar-alt"></i>
            <span>{{ $period ? \Carbon\Carbon::parse($period['from'])->format('d/m/Y') . ' – ' . \Carbon\Carbon::parse($period['to'])->format('d/m/Y') : __('merchant.current_month') }}</span>
        </button>
    </div>

    {{-- HERO : chiffre d'affaires + tendance --}}
    <section class="gc-hero">
        <div class="gc-hero-left">
            <small>{{ __('merchant.chiffre_affaires') }}</small>
            <h2 class="gc-countup" data-target="{{ $sales['sale'] }}" data-format="compact">0</h2>
            <p>{{ $currency }} {{ __('merchant.generes_periode') }}</p>
        </div>
        <div class="gc-hero-right">
            <div id="gcTrendChart"></div>
        </div>
    </section>

    {{-- KPIs --}}
    <div class="gc-kpis">
        <div class="gc-kpi-card">
            <div class="gc-kpi-top">
                <span class="gc-kpi-label">{{ __('menus.parcel') }}</span>
                <span class="gc-kpi-icon"><i class="fas fa-box"></i></span>
            </div>
            <div class="gc-kpi-value gc-countup" data-target="{{ $total }}">0</div>
            <span class="gc-kpi-sub">
                <b class="{{ $parcelDelta < 0 ? 'neg' : '' }}">{{ $parcelDelta > 0 ? '+' : '' }}{{ number_format($parcelDelta, 0, ',', ' ') }}</b>
                {{ __('merchant.ce_mois') }}
            </span>
        </div>

        <div class="gc-kpi-card">
            <div class="gc-kpi-top">
                <span class="gc-kpi-label">{{ __('merchant.livres') }}</span>
                <span class="gc-kpi-icon"><i class="fas fa-check-circle"></i></span>
            </div>
            <div class="gc-kpi-value gc-countup" data-target="{{ $delivered }}">0</div>
            <span class="gc-kpi-sub"><b>{{ number_format($successRate, 1, ',', ' ') }}%</b> {{ __('merchant.reussite') }}</span>
        </div>

        <div class="gc-kpi-card">
            <div class="gc-kpi-top">
                <span class="gc-kpi-label">{{ __('merchant.en_cours') }}</span>
                <span class="gc-kpi-icon"><i class="fas fa-truck"></i></span>
            </div>
            <div class="gc-kpi-value gc-countup" data-target="{{ $onGoing }}">0</div>
            <span class="gc-kpi-sub">{{ __('merchant.colis_actifs') }}</span>
        </div>

        <div class="gc-kpi-card">
            <div class="gc-kpi-top">
                <span class="gc-kpi-label">{{ __('merchant.boutiques') }}</span>
                <span class="gc-kpi-icon"><i class="fas fa-store"></i></span>
            </div>
            <div class="gc-kpi-value gc-countup" data-target="{{ $shopsCount }}">0</div>
            <span class="gc-kpi-sub">{{ __('merchant.actives') }}</span>
        </div>
    </div>

    {{-- Zones + Aujourd'hui --}}
    <div class="gc-grid">

        <section class="gc-card">
            <div class="gc-card-head">
                <h2>{{ __('merchant.zones_actives') }}</h2>
                <span>{{ __('merchant.top_boutiques') }}</span>
            </div>
            @if (count($shops) === 0)
                <div class="gc-empty">
                    <div class="gc-empty-icon"><i class="fas fa-store"></i></div>
                    <p class="gc-empty-title">{{ __('merchant.aucune_boutique') }}</p>
                    <p class="gc-empty-description">{{ __('merchant.aucune_boutique_desc') }}</p>
                </div>
            @else
                @foreach ($shops as $shop)
                    <div class="gc-zone">
                        <div class="gc-zone-head">
                            <span>{{ $shop['name'] }}</span>
                            <strong>{{ $zoneMax > 0 ? round(($shop['parcels'] / $zoneMax) * 100) : 0 }}%</strong>
                        </div>
                        <div class="gc-progress">
                            <span style="--p:{{ $zoneMax > 0 ? round(($shop['parcels'] / $zoneMax) * 100) : 0 }}%"></span>
                        </div>
                    </div>
                @endforeach
            @endif
        </section>

        <section class="gc-card gc-today">
            <div class="gc-card-head">
                <h2>{{ __('merchant.colis_aujourdhui') }}</h2>
                <span class="gc-delta {{ str_starts_with($todayDeltaLabel, '-') ? 'neg' : '' }}">{{ $todayDeltaLabel }}</span>
            </div>
            <div class="gc-today-num gc-countup" data-target="{{ $todayCount ?? 0 }}">0</div>
            <div class="gc-today-spark" id="gcTodaySpark"></div>
        </section>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript">
"use strict";

/* ---------- compteurs animés ---------- */
function gcCompact(n) {
    n = parseFloat(n) || 0;
    if (n >= 1000000) { var v = (n / 1000000).toFixed(2).replace(/0+$/, '').replace(/\.$/, ''); return v.replace('.', ',') + 'M'; }
    if (n >= 1000)    { var v = (n / 1000).toFixed(2).replace(/0+$/, '').replace(/\.$/, ''); return v.replace('.', ',') + 'k'; }
    return Math.round(n).toLocaleString('fr-FR');
}

function gcCountUp(el) {
    var target = parseFloat(el.getAttribute('data-target')) || 0;
    var compact = el.getAttribute('data-format') === 'compact';
    var duration = 900;
    var start = null;
    function tick(now) {
        if (!start) start = now;
        var p = Math.min((now - start) / duration, 1);
        var eased = 1 - Math.pow(1 - p, 3);
        var val = target * eased;
        el.textContent = compact ? gcCompact(val) : Math.round(val).toLocaleString('fr-FR');
        if (p < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
}

(function () {
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { gcCountUp(e.target); io.unobserve(e.target); }
        });
    }, { threshold: 0.4 });
    document.querySelectorAll('.gc-countup').forEach(function (el) { io.observe(el); });
})();

/* ---------- graphique de tendance (hero) ---------- */
(function () {
    var el = document.getElementById('gcTrendChart');
    if (!el || typeof ApexCharts === 'undefined') return;
    new ApexCharts(el, {
        chart: { type: 'area', height: 200, toolbar: { show: false }, fontFamily: 'Inter', sparkline: { enabled: false } },
        series: [
            { name: '{{ __('menus.parcel') }}', data: @json(array_values($series['totals'])) },
            { name: '{{ __('merchant.livres') }}', data: @json(array_values($series['delivers'])) }
        ],
        xaxis: { categories: @json($series['dates']), labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { show: false } },
        stroke: { curve: 'smooth', width: 2 },
        colors: ['#ffffff', '#bfe8d2'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .35, opacityTo: 0 } },
        grid: { show: false },
        dataLabels: { enabled: false },
        tooltip: { theme: 'dark', x: { show: true } },
        legend: { show: false }
    }).render();
})();

/* ---------- mini sparkline "aujourd'hui" ---------- */
(function () {
    var el = document.getElementById('gcTodaySpark');
    if (!el || typeof ApexCharts === 'undefined') return;
    new ApexCharts(el, {
        chart: { type: 'bar', height: 70, toolbar: { show: false }, sparkline: { enabled: true } },
        series: [{ name: '7 jours', data: @json(array_values($last7Totals)) }],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
        colors: ['#087443'],
        xaxis: { categories: @json($last7Dates), labels: { show: false } },
        yaxis: { show: false },
        grid: { show: false },
        tooltip: { enabled: false }
    }).render();
})();

/* ---------- filtre de période ---------- */
$(function () {
    var $btn = $('#gcDateBtn');
    if (!$btn.length || typeof $.fn.daterangepicker !== 'function') return;

    var startDate = @json(\Carbon\Carbon::parse($period['from'] ?? now()->startOfMonth())->startOfDay());
    var endDate   = @json(\Carbon\Carbon::parse($period['to'] ?? now())->endOfDay());

    $btn.daterangepicker({
        startDate: startDate,
        endDate: endDate,
        autoApply: false,
        opens: 'left',
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'Appliquer',
            cancelLabel: 'Annuler',
            fromLabel: 'De',
            toLabel: 'À',
            daysOfWeek: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
            monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
        }
    }, function (start, end) {
        $('#wcFilterDate').val(start.format('MM/DD/YYYY') + ' To ' + end.format('MM/DD/YYYY'));
        $('#wcFilterForm').submit();
    });
});
</script>
@endpush