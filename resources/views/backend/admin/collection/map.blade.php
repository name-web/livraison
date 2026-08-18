@extends('backend.partials.master')
@section('title', 'Carte temps réel')
@section('maincontent')

<div class="container-fluid p-0" style="height: calc(100vh - 60px);">

    {{-- Header overlay --}}
    <div class="position-absolute" style="top:10px;left:10px;z-index:1000;">
        <a href="{{ route('admin.collection.index') }}" class="btn btn-white shadow-sm">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div id="map" style="width:100%;height:100%;"></div>

    {{-- Légende --}}
    <div class="position-absolute bg-white shadow rounded p-3" style="bottom:20px;left:10px;z-index:1000;">
        <h6 class="mb-2">Légende</h6>
        <p class="mb-1"><span style="display:inline-block;width:12px;height:12px;background:#28a745;border-radius:50%;"></span> Livreur disponible</p>
        <p class="mb-1"><span style="display:inline-block;width:12px;height:12px;background:#ffc107;border-radius:50%;"></span> Livreur occupé</p>
        <p class="mb-1"><span style="display:inline-block;width:12px;height:12px;background:#dc3545;border-radius:50%;"></span> Collecte en cours</p>
        <p class="mb-0" id="statsRealtime">
            <small class="text-muted">Chargement...</small>
        </p>
    </div>
</div>

@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .marker-available { background:#28a745; border:2px solid #fff; border-radius:50%; width:14px; height:14px; box-shadow:0 0 6px rgba(0,0,0,0.3); }
    .marker-busy { background:#ffc107; border:2px solid #fff; border-radius:50%; width:14px; height:14px; box-shadow:0 0 6px rgba(0,0,0,0.3); }
    .marker-collection { background:#dc3545; border:2px solid #fff; border-radius:50%; width:14px; height:14px; box-shadow:0 0 6px rgba(0,0,0,0.3); }
</style>
@endsection

@section('script')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Coordonnées par défaut (Abidjan, Côte d'Ivoire)
    const map = L.map('map').setView([5.3600, -4.0083], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const deliverymen = @json($deliverymen);
    const collections = @json($activeCollections);

    const markers = {};

    // Placer les livreurs
    deliverymen.forEach(dm => {
        const icon = L.divIcon({
            className: 'marker-available',
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });

        const marker = L.marker([dm.lat, dm.lng], { icon })
            .addTo(map)
            .bindPopup(`<strong>${dm.name}</strong><br>ID: ${dm.id}`);

        markers['dm_' + dm.id] = marker;
    });

    // Placer les points de collecte
    collections.forEach(c => {
        if (c.pickup_lat && c.pickup_long) {
            const icon = L.divIcon({
                className: 'marker-collection',
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });

            L.marker([c.pickup_lat, c.pickup_long], { icon })
                .addTo(map)
                .bindPopup(`
                    <strong>Collecte #${c.id}</strong><br>
                    ${c.merchant?.business_name || c.merchant?.user?.name || ''}<br>
                    ${c.parcel_count} colis<br>
                    ${new Intl.NumberFormat().format(c.total_cash_collection)} FCFA
                `);
        }
    });

    // Stats temps réel
    function updateStats() {
        fetch('{{ route("admin.collection.stats") }}')
            .then(r => r.json())
            .then(data => {
                document.getElementById('statsRealtime').innerHTML =
                    `<small class="text-muted">En attente: ${data.pending} | Actives: ${data.active} | Livreurs dispo: ${data.available}</small>`;
            });
    }

    updateStats();
    setInterval(updateStats, 30000);

    // Écouter les updates GPS via Pusher/Socket
    if (typeof Echo !== 'undefined') {
        Echo.private('admin.map')
            .listen('.deliveryman.location.updated', (e) => {
                const dm = e.deliveryman;
                const key = 'dm_' + dm.id;
                if (markers[key]) {
                    markers[key].setLatLng([dm.lat, dm.lng]);
                }
            });

        Echo.private('admin.collections')
            .listen('.collection.status.changed', () => {
                updateStats();
            });
    }
});
</script>
@endsection
