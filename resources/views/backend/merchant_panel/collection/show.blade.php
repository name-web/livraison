@extends('backend.partials.master')
@section('title', 'Collecte #'.$collection->id)
@section('maincontent')

@php
    $isCancelled = $collection->status == 6;
    $statusBadge = match($collection->status) {
        1 => 'wc-badge-warning',
        2 => 'wc-badge-info',
        3 => 'wc-badge-transit',
        4 => 'wc-badge-success',
        5 => 'wc-badge-delivered',
        6 => 'wc-badge-error',
        default => 'wc-badge-neutral',
    };
    $steps = [
        ['s' => 1, 'label' => 'Créée', 'icon' => 'fas fa-plus-circle', 'desc' => 'Collecte planifiée'],
        ['s' => 2, 'label' => 'Assignée', 'icon' => 'fas fa-user-check', 'desc' => 'Livreur trouvé'],
        ['s' => 3, 'label' => 'Ramassage', 'icon' => 'fas fa-motorcycle', 'desc' => 'En déplacement'],
        ['s' => 4, 'label' => 'Collectée', 'icon' => 'fas fa-box-open', 'desc' => 'Colis récupérés'],
        ['s' => 5, 'label' => 'Terminée', 'icon' => 'fas fa-check-double', 'desc' => 'Mission achevée'],
    ];
@endphp

<div class="container-fluid dashboard-content">

    {{-- ─── HEADER ─────────────────────────────────── --}}
    <div class="wc-page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('merchant-panel.collection.index') }}" class="wc-btn wc-btn-soft wc-btn-sm !min-w-0 !p-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="wc-page-title">Collecte #{{ $collection->id }}</h1>
                <p class="wc-page-subtitle">Détails de la collecte</p>
            </div>
            <span class="wc-badge {{ $statusBadge }}">{{ $collection->status_label }}</span>
        </div>
        <div class="wc-toolbar">
            @if(in_array($collection->status, [1, 2]))
            <button type="button" class="wc-btn wc-btn-primary wc-btn-sm" onclick="document.getElementById('addParcelModal').classList.remove('hidden');document.body.style.overflow='hidden';">
                <i class="fas fa-plus"></i> Ajouter un colis
            </button>
            <form action="{{ route('merchant-panel.collection.cancel', $collection->id) }}" method="POST" class="m-0" onsubmit="var r=prompt('Raison de l\'annulation (optionnel) :');if(r===null)return false;this.insertAdjacentHTML('beforeend','<input type=\"hidden\" name=\"cancel_reason\" value=\"'+r+'\">');return confirm('Annuler cette collecte ?');">
                @csrf
                <button type="submit" class="wc-btn wc-btn-danger-soft wc-btn-sm">
                    <i class="fas fa-times"></i> Annuler
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($isCancelled)
    <div class="wc-card mb-5 border-l-4 border-l-red-500 bg-red-50">
        <div class="p-4 flex items-start gap-3">
            <div class="wc-card-icon !w-10 !h-10 bg-red-100 text-red-600 flex-shrink-0"><i class="fas fa-ban"></i></div>
            <div>
                <p class="font-bold text-red-700 m-0 text-[14px]">Collecte annulée</p>
                @if($collection->cancel_reason)
                <p class="text-red-600 text-[13px] m-0 mt-1">{{ $collection->cancel_reason }}</p>
                @endif
                @if($collection->cancelled_at)
                <p class="text-red-500 text-[11px] m-0 mt-1">Annulée le {{ $collection->cancelled_at->format('d/m/Y à H:i') }}</p>
                @endif
                <p class="text-red-500 text-[11px] m-0 mt-1">Les colis ont été remis en attente de collecte.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- ─── COLONNE GAUCHE : Infos + Timeline ─── --}}
        <div class="xl:col-span-2 space-y-5">

            {{-- Timeline --}}
            <div class="wc-card animate-wcFadeUp">
                <div class="wc-card-header">
                    <div class="flex items-center gap-3">
                        <div class="wc-card-icon bg-[#ecfdf5] text-[#059669]"><i class="fas fa-route"></i></div>
                        <div>
                            <h3 class="wc-card-title">Progression</h3>
                            <p class="text-[12px] text-wc-muted m-0">Suivi de la collecte en temps réel</p>
                        </div>
                    </div>
                </div>
                <div class="wc-card-body">
                    <div class="relative">
                        @foreach($steps as $i => $step)
                        <div class="flex items-start gap-4 {{ $i < count($steps) - 1 ? 'pb-6' : '' }}">
                            {{-- Connector line --}}
                            @if($i < count($steps) - 1)
                            <div class="absolute left-[18px] top-[38px] w-[2px] h-[calc(100%-52px)] {{ $collection->status > $step['s'] ? 'bg-wc-primary' : 'bg-gray-200' }}"></div>
                            @endif

                            {{-- Dot --}}
                            <div class="relative z-10 flex-shrink-0">
                                <div class="w-[38px] h-[38px] rounded-full flex items-center justify-center text-sm transition-all
                                    {{ $collection->status >= $step['s']
                                        ? 'bg-wc-primary text-white shadow-md shadow-wc-primary/20'
                                        : 'bg-gray-100 text-gray-400 border-2 border-gray-200' }}
                                    {{ $collection->status == $step['s'] ? 'ring-4 ring-wc-primary/15 scale-110' : '' }}">
                                    <i class="{{ $step['icon'] }}"></i>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 pt-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-[14px] {{ $collection->status >= $step['s'] ? 'text-wc-ink' : 'text-wc-muted-2' }}">
                                        {{ $step['label'] }}
                                    </span>
                                    @if($collection->status == $step['s'])
                                    <span class="wc-badge wc-badge-success" style="font-size:10px;">Actuel</span>
                                    @endif
                                </div>
                                <p class="text-[12px] {{ $collection->status >= $step['s'] ? 'text-wc-muted' : 'text-wc-muted-2' }} m-0 mt-0.5">
                                    {{ $step['desc'] }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Parcels --}}
            <div class="wc-card animate-wcFadeUp" style="animation-delay:.08s">
                <div class="wc-card-header">
                    <div class="flex items-center gap-3">
                        <div class="wc-card-icon bg-[#eff6ff] text-[#2563eb]"><i class="fas fa-box"></i></div>
                        <div>
                            <h3 class="wc-card-title">Colis ({{ $collection->parcels->count() }})</h3>
                            <p class="text-[12px] text-wc-muted m-0">Colis inclus dans cette collecte</p>
                        </div>
                    </div>
                </div>
                @if($collection->parcels->count() > 0)
                <div class="wc-table-wrap">
                    <table class="wc-table">
                        <thead>
                            <tr>
                                <th>Tracking</th>
                                <th>Destinataire</th>
                                <th>Adresse</th>
                                <th>Cash</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($collection->parcels as $p)
                            @php $picked = $p->pivot->status == 1; @endphp
                            <tr>
                                <td><code class="text-[11px] font-bold text-wc-primary bg-wc-primary-soft px-2 py-0.5 rounded-md">{{ $p->tracking_id }}</code></td>
                                <td class="font-semibold text-wc-ink text-[13px]">{{ $p->customer_name }}</td>
                                <td class="text-[12px] text-wc-muted-2 max-w-[200px] truncate">{{ $p->customer_address }}</td>
                                <td class="font-extrabold text-wc-primary wc-tabular">{{ number_format($p->cash_collection, 0, ',', ' ') }} <span class="text-[10px] text-wc-muted-2 font-normal">FCFA</span></td>
                                <td><span class="wc-badge {{ $picked ? 'wc-badge-success' : 'wc-badge-neutral' }}">{{ $picked ? 'Ramassé' : 'En attente' }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="wc-empty !py-8">
                    <p class="wc-empty-title">Aucun colis</p>
                </div>
                @endif
            </div>
        </div>

        {{-- ─── COLONNE DROITE : Résumé ────────────── --}}
        <div class="space-y-5">

            {{-- Summary card --}}
            <div class="wc-card animate-wcFadeUp" style="animation-delay:.04s">
                <div class="wc-card-header">
                    <div class="flex items-center gap-3">
                        <div class="wc-card-icon bg-[#fffbeb] text-[#d97706]"><i class="fas fa-info-circle"></i></div>
                        <h3 class="wc-card-title">Résumé</h3>
                    </div>
                </div>
                <div class="wc-card-body">
                    <div class="space-y-4">
                        <div>
                            <p class="wc-label m-0 mb-1">Boutique</p>
                            <p class="font-bold text-wc-ink m-0 text-[14px]">{{ $collection->shop?->name ?? '—' }}</p>
                            @if($collection->shop?->address)
                            <p class="text-[12px] text-wc-muted-2 m-0 mt-0.5">{{ $collection->shop->address }}</p>
                            @endif
                        </div>
                        <div class="border-t border-wc-border pt-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="wc-label m-0 mb-1">Date</p>
                                    <p class="font-bold text-wc-ink m-0">{{ $collection->collection_date ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="wc-label m-0 mb-1">Créneau</p>
                                    <p class="font-bold text-wc-ink m-0">{{ $collection->time_slot ?? '—' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-wc-border pt-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="wc-label m-0 mb-1">Colis</p>
                                    <p class="font-extrabold text-wc-ink m-0 text-xl">{{ $collection->parcel_count }}</p>
                                </div>
                                <div>
                                    <p class="wc-label m-0 mb-1">Cash total</p>
                                    <p class="font-extrabold text-wc-primary m-0 text-xl wc-tabular">{{ number_format($collection->total_cash_collection, 0, ',', ' ') }}</p>
                                    <p class="text-[10px] text-wc-muted-2 m-0">FCFA</p>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-wc-border pt-3">
                            <p class="wc-label m-0 mb-2">Livreur</p>
                            @if($collection->deliveryMan)
                            <div class="flex items-center gap-3">
                                <div class="wc-avatar !bg-[#eff6ff] !text-[#2563eb]">
                                    {{ strtoupper(mb_substr(trim($collection->deliveryMan->user->name), 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-wc-ink m-0 text-[13px]">{{ $collection->deliveryMan->user->name }}</p>
                                    @if(in_array($collection->status, [2, 3]) && $collection->deliveryMan->current_location_lat)
                                    <a href="https://www.google.com/maps?q={{ $collection->deliveryMan->current_location_lat }},{{ $collection->deliveryMan->current_location_long }}"
                                       target="_blank" class="text-[11px] text-wc-info font-semibold hover:underline">
                                        <i class="fas fa-map-marker-alt mr-1"></i>Voir sur la carte
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @else
                            <p class="text-[13px] text-wc-muted-2 italic m-0">En attente d'affectation...</p>
                            @endif
                        </div>
                        @if($collection->note)
                        <div class="border-t border-wc-border pt-3">
                            <p class="wc-label m-0 mb-1">Note</p>
                            <p class="text-[13px] text-wc-ink-2 m-0">{{ $collection->note }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Timestamps --}}
            <div class="wc-card animate-wcFadeUp" style="animation-delay:.1s">
                <div class="wc-card-header">
                    <h3 class="wc-card-title"><i class="fas fa-clock text-wc-muted-2 mr-2"></i>Horodatages</h3>
                </div>
                <div class="wc-card-body">
                    <div class="space-y-3 text-[12.5px]">
                        <div class="flex justify-between">
                            <span class="text-wc-muted">Créée</span>
                            <span class="font-semibold text-wc-ink">{{ $collection->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($collection->assigned_at)
                        <div class="flex justify-between">
                            <span class="text-wc-muted">Assignée</span>
                            <span class="font-semibold text-wc-ink">{{ $collection->assigned_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                        @if($collection->picked_up_at)
                        <div class="flex justify-between">
                            <span class="text-wc-muted">Ramassage</span>
                            <span class="font-semibold text-wc-ink">{{ $collection->picked_up_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                        @if($collection->collected_at)
                        <div class="flex justify-between">
                            <span class="text-wc-muted">Collectée</span>
                            <span class="font-semibold text-wc-ink">{{ $collection->collected_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                        @if($collection->cancelled_at)
                        <div class="flex justify-between">
                            <span class="text-red-500">Annulée</span>
                            <span class="font-semibold text-red-600">{{ $collection->cancelled_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(in_array($collection->status, [1, 2]))
{{-- MODAL : Ajouter un colis à cette collecte --}}
<div id="addParcelModal" class="wc-modal hidden" onclick="if(event.target===this){this.classList.add('hidden');document.body.style.overflow='';}">
    <div class="wc-modal-content" style="max-width:560px;">
        <div class="wc-modal-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-[#ecfdf5] text-[#059669]"><i class="fas fa-plus-circle"></i></div>
                <h3 class="wc-modal-title">Ajouter un colis à la collecte #{{ $collection->id }}</h3>
            </div>
            <button type="button" class="wc-btn wc-btn-soft wc-btn-sm !min-w-0 !p-2" onclick="document.getElementById('addParcelModal').classList.add('hidden');document.body.style.overflow='';"><i class="fas fa-times"></i></button>
        </div>
        <div class="wc-modal-body">
            <p class="text-[13px] text-wc-muted mb-3">Seuls les colis <strong>PENDING</strong> de votre compte sont éligibles. La boutique du colis doit correspondre à celle de la collecte.</p>
            <div class="wc-form-group">
                <label class="wc-label"><i class="fas fa-search text-wc-primary mr-1"></i> Rechercher un colis</label>
                <input type="text" id="addParcelSearch" class="wc-input" placeholder="Numéro de tracking..." oninput="searchParcelToAdd(this.value)">
            </div>
            <div id="addParcelResults" class="space-y-2 mt-3"></div>
            <div id="addParcelMsg" class="hidden mt-3 p-3 rounded-lg text-[13px]"></div>
        </div>
    </div>
</div>
<script>
"use strict";
var SEARCH_TIMEOUT = null;
function searchParcelToAdd(q) {
    clearTimeout(SEARCH_TIMEOUT);
    var results = document.getElementById('addParcelResults');
    var msg = document.getElementById('addParcelMsg');
    results.innerHTML = '';
    msg.classList.add('hidden');
    if (q.length < 2) return;
    SEARCH_TIMEOUT = setTimeout(function() {
        fetch('{{ route('merchant-panel.collection.available-parcels') }}?search=' + encodeURIComponent(q), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (!d.success || !d.parcels.length) {
                results.innerHTML = '<p class="text-[13px] text-wc-muted-2 text-center py-3">Aucun colis trouvé.</p>';
                return;
            }
            results.innerHTML = d.parcels.map(function(p) {
                return '<div class="flex items-center gap-3 p-3 border border-wc-border rounded-xl hover:bg-wc-primary-faint transition-colors">'
                    + '<div class="wc-avatar !w-9 !h-9 !text-[11px] !bg-[#ecfdf5] !text-[#059669]">' + (p.customer_name||'?').substring(0,2).toUpperCase() + '</div>'
                    + '<div class="flex-1 min-w-0">'
                    + '<code class="text-[11px] font-bold text-wc-primary bg-wc-primary-soft px-2 py-0.5 rounded-md">' + p.tracking_id + '</code>'
                    + ' <span class="text-[13px] font-semibold text-wc-ink">' + (p.customer_name||'') + '</span>'
                    + '<div class="text-[11px] text-wc-muted-2 truncate">' + (p.customer_address||'') + '</div>'
                    + '</div>'
                    + '<button type="button" class="wc-btn wc-btn-primary wc-btn-sm" onclick="addParcelToCollection(' + p.id + ', this)"><i class="fas fa-plus"></i> Ajouter</button>'
                    + '</div>';
            }).join('');
        });
    }, 300);
}
function addParcelToCollection(parcelId, btn) {
    btn.disabled = true;
    btn.innerHTML = '<div class="wc-spinner !w-3 !h-3 !border-2 !border-white/30 !border-t-white"></div>';
    fetch('{{ url("merchant/collection") }}/{{ $collection->id }}/add-parcel', {
        method: 'POST',
        body: JSON.stringify({ parcel_id: parcelId }),
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Request-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        var msg = document.getElementById('addParcelMsg');
        msg.classList.remove('hidden');
        if (d.success) {
            msg.className = 'mt-3 p-3 rounded-lg text-[13px] bg-green-50 text-green-700 border border-green-200';
            msg.textContent = d.message;
            setTimeout(function() { location.reload(); }, 1500);
        } else {
            msg.className = 'mt-3 p-3 rounded-lg text-[13px] bg-red-50 text-red-700 border border-red-200';
            msg.textContent = d.message;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus"></i> Ajouter';
        }
    })
    .catch(function() {
        var msg = document.getElementById('addParcelMsg');
        msg.classList.remove('hidden');
        msg.className = 'mt-3 p-3 rounded-lg text-[13px] bg-red-50 text-red-700 border border-red-200';
        msg.textContent = 'Erreur réseau.';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus"></i> Ajouter';
    });
}
</script>
@endif
@endsection
