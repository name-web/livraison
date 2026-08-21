@extends('backend.partials.master')
@section('title', 'Gestion des Collectes')
@push('styles')
@include('backend.admin.collection._styles')
@endpush
@section('maincontent')

@php
    $statusPills = [
        '' => ['label' => 'Toutes', 'icon' => 'fas fa-list-ul'],
        '1' => ['label' => 'En attente', 'icon' => 'fas fa-hourglass-half'],
        '2' => ['label' => 'Affectée', 'icon' => 'fas fa-user-check'],
        '3' => ['label' => 'Ramassage', 'icon' => 'fas fa-motorcycle'],
        '4' => ['label' => 'Collectée', 'icon' => 'fas fa-box-open'],
        '5' => ['label' => 'Terminée', 'icon' => 'fas fa-check-double'],
        '6' => ['label' => 'Annulée', 'icon' => 'fas fa-ban'],
    ];
    $steps = [
        ['s' => 1, 'icon' => 'fas fa-plus-circle'],
        ['s' => 2, 'icon' => 'fas fa-user-check'],
        ['s' => 3, 'icon' => 'fas fa-motorcycle'],
        ['s' => 4, 'icon' => 'fas fa-box-open'],
        ['s' => 5, 'icon' => 'fas fa-check-double'],
    ];
    $badgeMap = [1 => 'cl-badge-warning', 2 => 'cl-badge-info', 3 => 'cl-badge-transit', 4 => 'cl-badge-success', 5 => 'cl-badge-delivered', 6 => 'cl-badge-error'];
    $status = (string) request('status', '');
    $date = request('date', '');
    $merchantFilter = request('merchant_id', '');
@endphp

<div class="container-fluid dashboard-content cl-col-page">

    {{-- ─── HEADER ─────────────────────────────────── --}}
    <div class="cl-header">
        <div>
            <h1 class="cl-title"><i class="fas fa-truck-loading" style="color:var(--cl-green);margin-right:8px;"></i>Collectes</h1>
            <p class="cl-subtitle">Suivi et gestion des collectes en temps réel</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <span class="cl-live"><i class="fas fa-circle"></i> LIVE</span>
            <a href="{{ route('admin.collection.map') }}" class="cl-btn cl-btn-primary">
                <i class="fas fa-map-marked-alt"></i> Carte temps réel
            </a>
        </div>
    </div>

    {{-- ─── KPI ────────────────────────────────────── --}}
    <div class="cl-kpi-grid">
        <div class="cl-kpi" style="animation-delay:.02s">
            <div class="cl-kpi-top">
                <p class="cl-kpi-label">Total collectes</p>
                <div class="cl-kpi-icon" style="background:#f1f5f9;color:#475569;"><i class="fas fa-boxes"></i></div>
            </div>
            <p class="cl-kpi-value" id="kpiTotal">{{ $stats['total'] }}</p>
            <p class="cl-kpi-sub"><span class="cl-dot cl-dot-blue"></span> {{ $stats['today'] }} aujourd'hui</p>
        </div>
        <div class="cl-kpi" style="animation-delay:.06s">
            <div class="cl-kpi-top">
                <p class="cl-kpi-label">En attente</p>
                <div class="cl-kpi-icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-hourglass-half"></i></div>
            </div>
            <p class="cl-kpi-value" id="kpiPending" style="color:#d97706;">{{ $stats['pending'] }}</p>
            <p class="cl-kpi-sub"><span class="cl-dot cl-dot-amber"></span> à affecter</p>
        </div>
        <div class="cl-kpi" style="animation-delay:.1s">
            <div class="cl-kpi-top">
                <p class="cl-kpi-label">Actives</p>
                <div class="cl-kpi-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-route"></i></div>
            </div>
            <p class="cl-kpi-value" id="kpiActive" style="color:#2563eb;">{{ $stats['active'] }}</p>
            <p class="cl-kpi-sub"><span class="cl-dot cl-dot-blue"></span> en cours</p>
        </div>
        <div class="cl-kpi" style="animation-delay:.14s">
            <div class="cl-kpi-top">
                <p class="cl-kpi-label">Terminées</p>
                <div class="cl-kpi-icon" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-check-double"></i></div>
            </div>
            <p class="cl-kpi-value" id="kpiCompleted" style="color:#16a34a;">{{ $stats['completed'] }}</p>
            <p class="cl-kpi-sub"><span class="cl-dot cl-dot-green"></span> missions achevées</p>
        </div>
        <div class="cl-kpi" style="animation-delay:.18s">
            <div class="cl-kpi-top">
                <p class="cl-kpi-label">Livreurs dispo</p>
                <div class="cl-kpi-icon" style="background:#ecfdf5;color:#059669;"><i class="fas fa-motorcycle"></i></div>
            </div>
            <p class="cl-kpi-value" id="kpiAvailable">{{ $stats['available'] }}</p>
            <p class="cl-kpi-sub"><span class="cl-dot cl-dot-green"></span> prêts à collecter</p>
        </div>
    </div>

    {{-- ─── LISTE ──────────────────────────────────── --}}
    <div class="cl-card">

        {{-- Filtres --}}
        <div class="cl-filter-bar">
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;" id="statusPills">
                @foreach($statusPills as $key => $pill)
                <a href="{{ route('admin.collection.index', array_filter(['status' => $key, 'date' => $date, 'merchant_id' => $merchantFilter])) }}"
                   class="cl-pill {{ $status === (string) $key ? 'active' : '' }}">
                    <i class="{{ $pill['icon'] }}" style="font-size:10px;"></i> {{ $pill['label'] }}
                </a>
                @endforeach
            </div>
            <div style="flex:1;"></div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <input type="date" class="cl-input" id="clDate" value="{{ $date }}" title="Filtrer par date">
                <select class="cl-select" id="clMerchant" style="width:auto;max-width:190px;" title="Filtrer par marchand">
                    <option value="">Tous les marchands</option>
                    @foreach($merchants as $m)
                    <option value="{{ $m->id }}" {{ $merchantFilter == $m->id ? 'selected' : '' }}>{{ $m->business_name ?? $m->user->name }}</option>
                    @endforeach
                </select>
                <div style="position:relative;">
                    <i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:11px;color:var(--cl-muted-2);pointer-events:none;"></i>
                    <input type="text" id="clSearch" class="cl-input" style="padding-left:30px;min-width:170px;" placeholder="Recherche rapide...">
                </div>
            </div>
        </div>

        @if($collections->count() > 0)
        <div class="cl-table-wrap">
            <table class="cl-table">
                <thead><tr>
                    <th>#</th><th>Date & Créneau</th><th>Marchand</th><th>Colis</th><th>Cash</th><th>Livreur</th><th>Statut</th><th style="text-align:right;">Actions</th>
                </tr></thead>
                <tbody id="clTbody">
                    @foreach($collections as $c)
                    @php
                        $needle = mb_strtolower(
                            '#'.$c->id.' '.$c->created_at->format('d/m/Y H:i')
                            .' '.($c->merchant->business_name ?? $c->merchant->user->name ?? '')
                            .' '.($c->deliveryMan?->user?->name ?? '')
                            .' '.$c->status_label
                        );
                    @endphp
                    <tr data-search="{{ $needle }}">
                        <td class="cl-fw-8 cl-ink cl-tabular">#{{ $c->id }}</td>
                        <td>
                            <div class="cl-fw-6 cl-ink cl-fs-13">{{ $c->collection_date ? \Carbon\Carbon::parse($c->collection_date)->format('d/m/Y') : $c->created_at->format('d/m/Y') }}</div>
                            <div class="cl-fs-11 cl-muted-2">
                                <i class="fas fa-clock mr-1"></i>{{ $c->time_slot ?? $c->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td>
                            <div class="cl-fw-6 cl-ink cl-fs-13 cl-truncate" style="max-width:170px;">{{ $c->merchant->business_name ?? $c->merchant->user->name ?? '—' }}</div>
                            @if($c->shop?->name)<div class="cl-fs-11 cl-muted-2 cl-truncate" style="max-width:170px;">{{ $c->shop->name }}</div>@endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span class="cl-fw-8 cl-ink cl-fs-15 cl-tabular">{{ $c->parcel_count }}</span>
                                <span class="cl-steps">
                                    @foreach($steps as $i => $st)
                                    <span class="cl-step {{ $c->status >= $st['s'] ? ($c->status == $st['s'] ? 'active' : 'done') : 'todo' }}"><i class="{{ $st['icon'] }}"></i></span>
                                    @if($i < count($steps) - 1)<span class="cl-step-line {{ $c->status > $st['s'] ? 'done' : 'todo' }}"></span>@endif
                                    @endforeach
                                </span>
                            </div>
                        </td>
                        <td><span class="cl-fw-8 cl-green cl-fs-14 cl-tabular">{{ number_format($c->total_cash_collection, 0, ',', ' ') }}</span> <span class="cl-fs-10 cl-muted-2">FCFA</span></td>
                        <td>
                            @if($c->deliveryMan)
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span class="cl-avatar" style="background:#eff6ff;color:#2563eb;">{{ strtoupper(mb_substr(trim($c->deliveryMan->user->name), 0, 1)) }}</span>
                                <span class="cl-fs-12 cl-fw-6 cl-ink-2 cl-truncate" style="max-width:120px;">{{ $c->deliveryMan->user->name }}</span>
                            </div>
                            @else
                            <span class="cl-fs-12 cl-muted-2" style="font-style:italic;">En attente</span>
                            @endif
                        </td>
                        <td><span class="cl-badge {{ $badgeMap[$c->status] ?? 'cl-badge-neutral' }}">{{ $c->status_label }}</span></td>
                        <td style="text-align:right;">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                @if(in_array($c->status, [2, 3]) && $c->deliveryMan && $c->deliveryMan->current_location_lat)
                                <a href="https://www.google.com/maps?q={{ $c->deliveryMan->current_location_lat }},{{ $c->deliveryMan->current_location_long }}" target="_blank" class="cl-btn cl-btn-soft cl-btn-sm cl-btn-icon" title="GPS livreur"><i class="fas fa-map-marker-alt" style="color:#2563eb;"></i></a>
                                @endif
                                @if($c->status == 1 && !$c->delivery_man_id)
                                <button type="button" class="cl-btn cl-btn-soft cl-btn-sm cl-btn-icon cl-assign-btn" data-id="{{ $c->id }}" data-name="#{{ $c->id }}" title="Affecter un livreur"><i class="fas fa-user-plus" style="color:var(--cl-green);"></i></button>
                                @endif
                                <a href="{{ route('admin.collection.show', $c->id) }}" class="cl-btn cl-btn-soft cl-btn-sm cl-btn-icon" title="Détails"><i class="fas fa-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="cl-pager">
            <span>Affichage <span class="cl-fw-7 cl-ink">{{ $collections->firstItem() }}</span> à <span class="cl-fw-7 cl-ink">{{ $collections->lastItem() }}</span> sur <span class="cl-fw-7 cl-ink">{{ $collections->total() }}</span></span>
            <span>{{ $collections->withQueryString()->links() }}</span>
        </div>
        @else
        <div class="cl-empty">
            <div class="cl-empty-icon"><i class="fas fa-truck-loading"></i></div>
            <p class="cl-empty-title">Aucune collecte trouvée</p>
            <p class="cl-empty-desc">Modifiez vos filtres ou attendez de nouvelles collectes planifiées par les marchands.</p>
        </div>
        @endif
    </div>
</div>

{{-- ─── MODAL : Affecter un livreur ────────────────── --}}
<div class="cl-modal-overlay" id="assignModal">
    <div class="cl-modal">
        <div class="cl-modal-head">
            <h3 class="cl-modal-title"><i class="fas fa-user-plus" style="color:var(--cl-green);margin-right:6px;"></i>Affecter un livreur</h3>
            <button type="button" class="cl-btn cl-btn-soft cl-btn-sm cl-btn-icon" onclick="closeAssignModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="assignForm" method="POST" action="">
            @csrf
            <div class="cl-modal-body">
                <p class="cl-fs-12 cl-muted" style="margin:0 0 14px;">Collecte <span class="cl-fw-7 cl-ink" id="assignTarget">#</span> — seuls les livreurs disponibles sont proposés.</p>
                <label class="cl-label">Livreur disponible</label>
                <select name="delivery_man_id" class="cl-select" required>
                    <option value="">Sélectionner...</option>
                    @foreach($deliverymen as $dm)
                    @if($dm->is_available)
                    <option value="{{ $dm->id }}">{{ $dm->user->name }}{{ $dm->user->mobile ? ' — '.$dm->user->mobile : '' }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="cl-modal-foot">
                <button type="button" class="cl-btn cl-btn-soft" onclick="closeAssignModal()">Annuler</button>
                <button type="submit" class="cl-btn cl-btn-primary" id="assignSubmit"><i class="fas fa-check"></i> Affecter</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
"use strict";

/* ── Modal assignation ── */
function openAssignModal(id, label) {
    document.getElementById('assignModal').classList.add('show');
    document.getElementById('assignForm').action = '{{ url("admin/collection") }}/' + id + '/assign';
    document.getElementById('assignTarget').textContent = label;
    document.body.style.overflow = 'hidden';
}
function closeAssignModal() {
    document.getElementById('assignModal').classList.remove('show');
    document.body.style.overflow = '';
}
document.querySelectorAll('.cl-assign-btn').forEach(function(btn) {
    btn.addEventListener('click', function() { openAssignModal(this.dataset.id, this.dataset.name); });
});
document.getElementById('assignModal').addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});

/* ── Recherche client rapide ── */
var searchInput = document.getElementById('clSearch');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        var q = this.value.toLowerCase().trim();
        document.querySelectorAll('#clTbody tr').forEach(function(row) {
            row.style.display = !q || (row.dataset.search || '').indexOf(q) !== -1 ? '' : 'none';
        });
    });
}

/* ── Filtres date / marchand : navigation avec params ── */
function reloadWith(params) {
    var url = new URL(window.location.href);
    Object.keys(params).forEach(function(k) {
        if (params[k] === '' || params[k] === null) { url.searchParams.delete(k); }
        else { url.searchParams.set(k, params[k]); }
    });
    window.location.href = url.toString();
}
var dateInput = document.getElementById('clDate');
if (dateInput) dateInput.addEventListener('change', function() { reloadWith({ date: this.value }); });
var merchantSelect = document.getElementById('clMerchant');
if (merchantSelect) merchantSelect.addEventListener('change', function() { reloadWith({ merchant_id: this.value }); });

/* ── Stats temps réel (polling + Echo) ── */
function refreshStats() {
    fetch('{{ route("admin.collection.stats") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            var set = function(id, v) { var el = document.getElementById(id); if (el) el.textContent = v; };
            set('kpiPending', d.pending);
            set('kpiActive', d.active);
            set('kpiCompleted', d.completed);
            set('kpiAvailable', d.available);
        })
        .catch(function() {});
}
setInterval(refreshStats, 30000);

if (typeof Echo !== 'undefined') {
    Echo.private('admin.collections').listen('.collection.status.changed', function(e) {
        refreshStats();
        if (e.collection && e.collection.status === 1) {
            setTimeout(function() { location.reload(); }, 1500);
        } else {
            setTimeout(function() { location.reload(); }, 4000);
        }
    });
}
</script>
@endpush
@endsection