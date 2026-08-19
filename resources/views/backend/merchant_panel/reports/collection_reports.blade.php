@extends('backend.partials.master')
@section('title')
    Rapports · Collectes
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">Rapports Collectes</h1>
            <p class="wc-page-subtitle">Rapports · Récapitulatif de toutes vos collectes</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="exportCollectionTable" data-title="Rapport Collectes" data-filename="RapportCollectes" class="wc-btn wc-btn-soft wc-btn-sm"><i class="fas fa-file-excel"></i> Exporter</button>
            <a href="{{ route('merchant-panel.collection.index') }}" class="wc-btn wc-btn-primary wc-btn-sm"><i class="fas fa-truck-loading"></i> Voir les collectes</a>
        </div>
    </div>

    {{-- Filtre --}}
    <div class="wc-filter">
        <form action="{{ route('merchant-panel.collection.filter.reports') }}" method="GET" class="m-0">
            @csrf
            <div class="flex items-end gap-2 flex-wrap">
                <div class="wc-form-group m-0 flex-1 min-w-[240px] max-w-[380px]">
                    <label class="wc-label" for="date">Période</label>
                    <input type="text" autocomplete="off" id="date" name="parcel_date" placeholder="Sélectionnez une période" class="wc-input date_range_picker" value="{{ old('parcel_date', $request->parcel_date) }}">
                </div>
                <button type="submit" class="wc-btn wc-btn-primary"><i class="fa fa-filter text-[12px]"></i> Filtrer</button>
                <a href="{{ route('merchant-panel.collection.reports') }}" class="wc-btn wc-btn-outline"><i class="fa fa-eraser text-[12px]"></i> Effacer</a>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    @if($stats)
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
        <div class="wc-card !py-4 !px-5">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon !w-10 !h-10 bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-truck-loading"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-wc-muted uppercase tracking-wide m-0">Total</p>
                    <p class="text-[22px] font-extrabold text-wc-ink m-0 wc-tabular">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="wc-card !py-4 !px-5">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon !w-10 !h-10 bg-wc-success-soft text-wc-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-wc-muted uppercase tracking-wide m-0">Terminées</p>
                    <p class="text-[22px] font-extrabold text-wc-success m-0 wc-tabular">{{ $stats['completed'] }}</p>
                </div>
            </div>
        </div>
        <div class="wc-card !py-4 !px-5">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon !w-10 !h-10 bg-wc-warning-soft text-wc-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-wc-muted uppercase tracking-wide m-0">En attente</p>
                    <p class="text-[22px] font-extrabold text-wc-warning m-0 wc-tabular">{{ $stats['pending'] + $stats['assigned'] }}</p>
                </div>
            </div>
        </div>
        <div class="wc-card !py-4 !px-5">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon !w-10 !h-10 bg-wc-danger-soft text-wc-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-wc-muted uppercase tracking-wide m-0">Annulées</p>
                    <p class="text-[22px] font-extrabold text-wc-danger m-0 wc-tabular">{{ $stats['cancelled'] }}</p>
                </div>
            </div>
        </div>
        <div class="wc-card !py-4 !px-5">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon !w-10 !h-10 bg-wc-info-soft text-wc-info">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-wc-muted uppercase tracking-wide m-0">Colis collectés</p>
                    <p class="text-[22px] font-extrabold text-wc-info m-0 wc-tabular">{{ $stats['total_parcels'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphique évolution --}}
    <div class="wc-card mb-6">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-chart-area"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">Évolution des collectes</h3>
                    <p class="text-[12px] text-wc-muted m-0">Visualisez la tendance de vos collectes</p>
                </div>
            </div>
            <div class="flex items-center gap-1 bg-wc-bg rounded-lg p-0.5">
                <button type="button" id="chartBtnDay" class="px-3 py-1.5 text-[12px] font-semibold rounded-md bg-wc-primary text-white transition-all" onclick="switchChart('day')">Par jour</button>
                <button type="button" id="chartBtnWeek" class="px-3 py-1.5 text-[12px] font-semibold rounded-md text-wc-muted hover:text-wc-ink transition-all" onclick="switchChart('week')">Par semaine</button>
            </div>
        </div>
        <div class="p-4">
            <div id="collectionEvolutionChart"></div>
        </div>
    </div>

    {{-- Résumé financier --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="wc-card">
            <div class="wc-card-header !min-h-[50px] !py-3">
                <div class="flex items-center gap-2.5">
                    <div class="wc-card-icon !w-8 !h-8 bg-wc-primary-soft text-wc-primary"><i class="fas fa-money-bill-wave"></i></div>
                    <h3 class="wc-card-title text-[13px]">Cash collecté</h3>
                </div>
            </div>
            <div class="px-4 pb-4">
                <p class="text-[24px] font-extrabold text-wc-ink m-0 wc-tabular">{{ formatPrice($stats['total_cash']) }}</p>
            </div>
        </div>
        <div class="wc-card">
            <div class="wc-card-header !min-h-[50px] !py-3">
                <div class="flex items-center gap-2.5">
                    <div class="wc-card-icon !w-8 !h-8 bg-wc-success-soft text-wc-success"><i class="fas fa-shipping-fast"></i></div>
                    <h3 class="wc-card-title text-[13px]">Frais de livraison</h3>
                </div>
            </div>
            <div class="px-4 pb-4">
                <p class="text-[24px] font-extrabold text-wc-ink m-0 wc-tabular">{{ formatPrice($stats['total_delivery']) }}</p>
            </div>
        </div>
        <div class="wc-card">
            <div class="wc-card-header !min-h-[50px] !py-3">
                <div class="flex items-center gap-2.5">
                    <div class="wc-card-icon !w-8 !h-8 bg-wc-info-soft text-wc-info"><i class="fas fa-percentage"></i></div>
                    <h3 class="wc-card-title text-[13px]">Taux de complétion</h3>
                </div>
            </div>
            <div class="px-4 pb-4">
                @php $rate = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0; @endphp
                <div class="flex items-end gap-2">
                    <p class="text-[24px] font-extrabold text-wc-ink m-0 wc-tabular">{{ $rate }}%</p>
                    <div class="flex-1 h-2 bg-wc-border rounded-full overflow-hidden mb-1.5">
                        <div class="h-full bg-wc-success rounded-full transition-all" style="width: {{ $rate }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Tableau détaillé --}}
    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-list"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">Détail des collectes</h3>
                    <p class="text-[12px] text-wc-muted m-0">{{ $stats ? $stats['total'].' collecte(s)' : 'Aucun filtre appliqué' }}</p>
                </div>
            </div>
        </div>

        @if($collections->count() > 0)
        <div class="wc-table-wrap">
            <table class="wc-table" id="collectionReportTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Créneau</th>
                        <th>Boutique</th>
                        <th>Livreur</th>
                        <th>Statut</th>
                        <th class="text-right">Colis</th>
                        <th class="text-right">Cash collecté</th>
                        <th class="text-right">Frais livraison</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @foreach($collections as $c)
                    <tr>
                        <td class="wc-tabular text-wc-muted-2">{{ $i++ }}</td>
                        <td class="text-wc-ink font-semibold">{{ $c->collection_date ? \Carbon\Carbon::parse($c->collection_date)->format('d/m/Y') : '—' }}</td>
                        <td><span class="wc-badge wc-badge-neutral">{{ $c->time_slot ?: '—' }}</span></td>
                        <td>{{ $c->shop?->name ?? '—' }}</td>
                        <td>{{ $c->deliveryMan?->user?->name ?? 'En attente' }}</td>
                        <td>{!! match($c->status) {
                            \App\Enums\CollectionStatus::COMPLETED => '<span class="wc-badge wc-badge-success">Terminée</span>',
                            \App\Enums\CollectionStatus::CANCELLED => '<span class="wc-badge wc-badge-danger">Annulée</span>',
                            \App\Enums\CollectionStatus::PENDING_ASSIGNMENT => '<span class="wc-badge wc-badge-warning">En attente</span>',
                            \App\Enums\CollectionStatus::ASSIGNED => '<span class="wc-badge wc-badge-info">Affectée</span>',
                            \App\Enums\CollectionStatus::PICKING_UP => '<span class="wc-badge wc-badge-primary">Ramassage</span>',
                            \App\Enums\CollectionStatus::COLLECTED => '<span class="wc-badge wc-badge-success">Collectée</span>',
                            default => '<span class="wc-badge wc-badge-neutral">—</span>',
                        } !!}</td>
                        <td class="text-right font-bold text-wc-ink wc-tabular">{{ $c->parcel_count }}</td>
                        <td class="text-right font-bold text-wc-primary wc-tabular">{{ formatPrice($c->total_cash_collection) }}</td>
                        <td class="text-right font-bold text-wc-ink wc-tabular">{{ formatPrice($c->total_delivery_amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-extrabold">
                        <td colspan="6" class="text-right text-wc-ink">Totaux</td>
                        <td class="text-right text-wc-primary wc-tabular">{{ $collections->sum('parcel_count') }}</td>
                        <td class="text-right text-wc-primary wc-tabular">{{ formatPrice($collections->sum('total_cash_collection')) }}</td>
                        <td class="text-right text-wc-ink wc-tabular">{{ formatPrice($collections->sum('total_delivery_amount')) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="wc-empty">
            <div class="wc-empty-icon"><i class="fas fa-truck-loading"></i></div>
            <p class="wc-empty-title">Aucune collecte trouvée</p>
            <p class="wc-empty-description">Appliquez un filtre de période pour générer le rapport.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
    <script>
        /* Export Excel */
        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.getElementById('exportCollectionTable');
            if (btn) {
                btn.addEventListener('click', function () {
                    var table = document.getElementById('collectionReportTable');
                    if (!table) return;
                    var clone = table.cloneNode(true);
                    clone.querySelectorAll('tfoot').forEach(function(t){ t.remove(); });
                    var $tmp = jQuery('<table>').append(clone);
                    $tmp.find('th, td').css({ 'padding': '6px 10px', 'border': '1px solid #ccc' });
                    $tmp.table2excel({ name: btn.dataset.title || 'Rapport', filename: btn.dataset.filename || 'rapport' });
                });
            }
        });
    </script>
    <script src="{{ static_asset('backend/js/reports/jquery.table2excel.min.js') }}"></script>

    {{-- ── Graphique évolution collectes ── --}}
    <script>
    "use strict";

    var COLLECT_CHART = null;
    var CHART_DATA = {
        day: {
            labels: @json($chartDaily['labels'] ?? []),
            total: @json($chartDaily['total'] ?? []),
            parcels: @json($chartDaily['parcels'] ?? []),
            cash: @json($chartDaily['cash'] ?? [])
        },
        week: {
            labels: @json($chartWeekly['labels'] ?? []),
            total: @json($chartWeekly['total'] ?? []),
            parcels: @json($chartWeekly['parcels'] ?? []),
            cash: @json($chartWeekly['cash'] ?? [])
        }
    };

    function buildChart(mode) {
        var d = CHART_DATA[mode];
        if (!d || !d.labels.length) return;

        var el = document.getElementById('collectionEvolutionChart');
        if (!el || typeof ApexCharts === 'undefined') return;

        if (COLLECT_CHART) { COLLECT_CHART.destroy(); }

        COLLECT_CHART = new ApexCharts(el, {
            chart: {
                type: 'area',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                sparkline: { enabled: false },
                dropShadow: { enabled: false }
            },
            series: [
                { name: 'Collectes', data: d.total },
                { name: 'Colis', data: d.parcels }
            ],
            colors: ['#087443', '#10B981'],
            stroke: { curve: 'smooth', width: 2.5 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.25,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: d.labels,
                labels: {
                    style: { fontSize: '11px', colors: '#94A3B8' },
                    rotate: d.labels.length > 15 ? -45 : 0
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: [
                { title: { text: 'Collectes', style: { fontSize: '11px', color: '#087443' } }, labels: { style: { fontSize: '11px', colors: '#64748B' } }, min: 0 },
                { opposite: true, title: { text: 'Colis', style: { fontSize: '11px', color: '#10B981' } }, labels: { style: { fontSize: '11px', colors: '#64748B' } }, min: 0 }
            ],
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '12px',
                markers: { radius: 3, width: 12, height: 12 },
                itemMargin: { horizontal: 12 }
            },
            grid: {
                borderColor: '#F1F5F9',
                strokeDashArray: 4,
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } }
            },
            tooltip: {
                shared: true,
                intersect: false,
                theme: 'light',
                style: { fontSize: '12px' },
                y: {
                    formatter: function (val) {
                        return val + ' unité(s)';
                    }
                }
            },
            dataLabels: { enabled: false },
            noData: {
                text: 'Aucune donnée pour cette période',
                style: { fontSize: '14px', color: '#94A3B8' }
            }
        });

        COLLECT_CHART.render();
    }

    function switchChart(mode) {
        document.getElementById('chartBtnDay').className = mode === 'day'
            ? 'px-3 py-1.5 text-[12px] font-semibold rounded-md bg-wc-primary text-white transition-all'
            : 'px-3 py-1.5 text-[12px] font-semibold rounded-md text-wc-muted hover:text-wc-ink transition-all';
        document.getElementById('chartBtnWeek').className = mode === 'week'
            ? 'px-3 py-1.5 text-[12px] font-semibold rounded-md bg-wc-primary text-white transition-all'
            : 'px-3 py-1.5 text-[12px] font-semibold rounded-md text-wc-muted hover:text-wc-ink transition-all';
        buildChart(mode);
    }

    document.addEventListener('DOMContentLoaded', function () {
        buildChart('day');
    });
    </script>
@endpush
