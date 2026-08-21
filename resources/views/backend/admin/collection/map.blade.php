@extends('backend.partials.master')
@section('title', 'Carte temps réel')
@section('maincontent')

<style>
.cl-map-page{height:calc(100vh - 60px);position:relative;overflow:hidden;font-family:Inter,Circular Std,sans-serif}
#map{width:100%;height:100%;z-index:1}
.cl-map-top{position:absolute;top:12px;left:12px;z-index:1000;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.cl-map-btn{display:inline-flex;align-items:center;gap:7px;background:#fff;border:1px solid #e7ebe9;border-radius:12px;padding:9px 14px;font-size:12.5px;font-weight:700;color:#1e293b;box-shadow:0 4px 14px rgba(15,23,42,.08);text-decoration:none;transition:all .2s ease;cursor:pointer}
.cl-map-btn:hover{box-shadow:0 6px 20px rgba(15,23,42,.14);text-decoration:none;color:#059669}
.cl-map-live{display:inline-flex;align-items:center;gap:6px;background:#ecfdf5;border:1px solid #a7f3d0;color:#059669;border-radius:999px;padding:7px 14px;font-size:11px;font-weight:800;box-shadow:0 4px 14px rgba(15,23,42,.08)}
.cl-map-live i{font-size:7px;animation:clMapPulse 1.6s ease infinite}
@keyframes clMapPulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.75)}}
.cl-map-panel{position:absolute;top:12px;right:12px;z-index:1000;width:300px;max-width:calc(100vw - 24px);background:#fff;border-radius:16px;box-shadow:0 12px 40px rgba(15,23,42,.16);border:1px solid #e7ebe9;display:flex;flex-direction:column;max-height:calc(100% - 24px)}
.cl-map-panel-head{padding:13px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:8px}
.cl-map-panel-head h5{margin:0;font-size:13.5px;font-weight:800;color:#1e293b}
.cl-map-panel-head .cl-badge-live{font-size:9.5px;font-weight:800;background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;border-radius:999px;padding:2px 8px}
.cl-map-list{overflow-y:auto;padding:8px;flex:1}
.cl-map-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:12px;cursor:pointer;transition:background .15s ease;border:1px solid transparent}
.cl-map-item:hover{background:#f8fafc;border-color:#e7ebe9}
.cl-map-item-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0}
.cl-map-item-name{font-size:12.5px;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cl-map-item-sub{font-size:10.5px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cl-map-legend{position:absolute;bottom:12px;left:12px;z-index:1000;background:#fff;border-radius:14px;box-shadow:0 8px 30px rgba(15,23,42,.12);border:1px solid #e7ebe9;padding:12px 16px;font-size:11.5px}
.cl-map-legend h6{margin:0 0 8px;font-size:12px;font-weight:800;color:#1e293b}
.cl-map-legend p{margin:0 0 5px;display:flex;align-items:center;gap:8px;color:#475569}
.cl-map-legend p:last-child{margin:0}
.marker-dm{width:16px;height:16px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 0 3px rgba(15,23,42,.15),0 2px 6px rgba(15,23,42,.3)}
.marker-dm.available{background:#22c55e}
.marker-dm.busy{background:#f59e0b}
.marker-col{width:18px;height:18px;border-radius:50% 50% 50% 4px;transform:rotate(-45deg);border:2px solid #fff;box-shadow:0 0 0 3px rgba(15,23,42,.15),0 2px 6px rgba(15,23,42,.3);display:flex;align-items:center;justify-content:center}
.marker-col i{transform:rotate(45deg);font-size:8px;color:#fff}
.marker-col.s1{background:#ef4444}.marker-col.s2{background:#f59e0b}.marker-col.s3{background:#3b82f6}.marker-col.s4{background:#8b5cf6}
.cl-map-pop{font-family:Inter,Circular Std,sans-serif;font-size:12px;min-width:170px}
.cl-map-pop .pop-title{font-weight:800;font-size:13px;color:#1e293b}
.cl-map-pop .pop-row{display:flex;justify-content:space-between;gap:10px;margin-top:4px;color:#475569}
.cl-map-pop a{color:#059669;font-weight:700;text-decoration:none}
</style>

<div class="cl-map-page">

    <div id="map"></div>

    {{-- Top bar --}}
    <div class="cl-map-top">
        <a href="{{ route('admin.collection.index') }}" class="cl-map-btn"><i class="fas fa-arrow-left"></i> Retour</a>
        <span class="cl-map-live"><i class="fas fa-circle"></i> LIVE</span>
    </div>

    {{-- Panneau actifs --}}
    <div class="cl-map-panel">
        <div class="cl-map-panel-head">
            <i class="fas fa-route" style="color:#059669;font-size:13px;"></i>
            <h5>Collectes actives</h5>
            <span class="cl-badge-live" id="activeCount">{{ $activeCollections->count() }}</span>
            <span style="margin-left:auto;font-size:10.5px;color:#94a3b8;font-weight:600;" id="lastUpdate">—</span>
        </div>
        <div class="cl-map-list" id="activeList">
            @forelse($activeCollections as $c)
            <div class="cl-map-item" data-lat="{{ $c->pickup_lat }}" data-lng="{{ $c->pickup_long }}" onclick="focusCollection(this)">
                <span class="cl-map-item-dot" style="background:{{ ['#ef4444','#f59e0b','#3b82f6','#8b5cf6'][$c->status - 1] ?? '#ef4444' }};"></span>
                <div style="flex:1;min-width:0;">
                    <div class="cl-map-item-name">Collecte #{{ $c->id }} — {{ $c->merchant->business_name ?? $c->merchant->user->name ?? 'Marchand' }}</div>
                    <div class="cl-map-item-sub">{{ $c->parcel_count }} colis · {{ number_format($c->total_cash_collection, 0, ',', ' ') }} FCFA · {{ $c->status_label }}@if($c->deliveryMan?->user?->name) · {{ $c->deliveryMan->user->name }}@endif</div>
                </div>
                <a href="{{ route('admin.collection.show', $c->id) }}" class="cl-map-btn" style="padding:4px 8px;font-size:10.5px;border-radius:8px;flex-shrink:0;"><i class="fas fa-eye"></i></a>
            </div>
            @empty
            <div style="text-align:center;padding:28px 12px;color:#94a3b8;">
                <i class="fas fa-truck-loading" style="font-size:22px;display:block;margin-bottom:8px;opacity:.5;"></i>
                <p style="font-size:12px;margin:0;font-weight:600;">Aucune collecte active</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Légende --}}
    <div class="cl-map-legend">
        <h6>Légende</h6>
        <p><span style="width:11px;height:11px;border-radius:50%;background:#22c55e;display:inline-block;"></span> Livreur disponible</p>
        <p><span style="width:11px;height:11px;border-radius:50%;background:#f59e0b;display:inline-block;"></span> Livreur occupé</p>
        <p><span style="width:12px;height:12px;border-radius:3px;background:#ef4444;display:inline-block;transform:rotate(45deg);"></span> Collecte active</p>
        <p id="statsRealtime" style="color:#94a3b8;font-size:10.5px;">Chargement...</p>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
"use strict";
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([5.3600, -4.0083], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);

    const deliverymen = @json($deliverymen);
    const collections = @json($activeCollections);
    const dmMarkers = {};
    const colMarkers = {};

    /* ── Livreurs ── */
    deliverymen.forEach(function(dm) {
        if (!dm.lat || !dm.lng) return;
        const marker = L.marker([dm.lat, dm.lng], {
            icon: L.divIcon({ className: '', html: '<div class="marker-dm ' + (dm.is_available ? 'available' : 'busy') + '"></div>', iconSize: [16, 16], iconAnchor: [8, 8] })
        }).addTo(map).bindPopup(
            '<div class="cl-map-pop">'
            + '<div class="pop-title">' + dm.name + '</div>'
            + '<div class="pop-row"><span>' + (dm.is_available ? 'Disponible' : 'Occupé') + '</span></div>'
            + '<div class="pop-row"><a href="https://www.google.com/maps?q=' + dm.lat + ',' + dm.lng + '" target="_blank">Ouvrir dans Google Maps</a></div>'
            + '</div>'
        );
        dmMarkers['dm_' + dm.id] = marker;
    });

    /* ── Collectes actives ── */
    const statusColor = ['#ef4444', '#f59e0b', '#3b82f6', '#8b5cf6'];
    collections.forEach(function(c) {
        if (!c.pickup_lat || !c.pickup_long) return;
        const marker = L.marker([c.pickup_lat, c.pickup_long], {
            icon: L.divIcon({ className: '', html: '<div class="marker-col s' + Math.min(c.status, 4) + '"><i class="fas fa-box"></i></div>', iconSize: [18, 18], iconAnchor: [9, 18] })
        }).addTo(map).bindPopup(
            '<div class="cl-map-pop">'
            + '<div class="pop-title">Collecte #' + c.id + '</div>'
            + '<div class="pop-row"><span>' + (c.merchant?.business_name || c.merchant?.user?.name || 'Marchand') + '</span></div>'
            + '<div class="pop-row"><span>' + c.parcel_count + ' colis · ' + new Intl.NumberFormat('fr-FR').format(c.total_cash_collection) + ' FCFA</span></div>'
            + '<div class="pop-row"><span>' + c.status_label + '</span></div>'
            + '<div class="pop-row"><a href="' + '{{ url("admin/collection") }}/' + c.id + '">Voir les détails</a></div>'
            + '</div>'
        );
        colMarkers['col_' + c.id] = marker;
    });

    /* ── Focus depuis le panneau ── */
    window.focusCollection = function(el) {
        if (!el.dataset.lat || !el.dataset.lng) return;
        map.flyTo([el.dataset.lat, el.dataset.lng], 15);
    };

    /* ── Stats temps réel ── */
    function updateStats() {
        fetch('{{ route("admin.collection.stats") }}')
            .then(function(r) { return r.json(); })
            .then(function(d) {
                document.getElementById('statsRealtime').innerHTML =
                    'En attente : ' + d.pending + ' · Actives : ' + d.active + ' · Livreurs dispo : ' + d.available;
                var count = document.getElementById('activeCount');
                if (count && d.active != null) count.textContent = d.active;
                document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString('fr-FR');
            });
    }
    updateStats();
    setInterval(updateStats, 20000);

    /* ── GPS live : déplacement des marqueurs ── */
    if (typeof Echo !== 'undefined') {
        Echo.private('admin.map').listen('.deliveryman.location.updated', function(e) {
            const dm = e.deliveryman;
            const key = 'dm_' + dm.id;
            if (dmMarkers[key] && dm.lat && dm.lng) {
                dmMarkers[key].setLatLng([dm.lat, dm.lng]);
            }
        });
        Echo.private('admin.collections').listen('.collection.status.changed', function(e) {
            updateStats();
            if (e.collection && (e.collection.status === 5 || e.collection.status === 6)) {
                const key = 'col_' + e.collection.id;
                if (colMarkers[key]) { map.removeLayer(colMarkers[key]); delete colMarkers[key]; }
                setTimeout(function() { location.reload(); }, 3000);
            } else if (e.collection && e.collection.status >= 1 && e.collection.status <= 4) {
                setTimeout(function() { location.reload(); }, 3000);
            }
        });
    }
});
</script>
@endpush
@endsection