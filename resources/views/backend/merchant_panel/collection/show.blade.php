@extends('backend.partials.master')
@section('title', 'Collecte #'.$collection->id)
@section('maincontent')

@php
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
            <form action="{{ route('merchant-panel.collection.cancel', $collection->id) }}" method="POST" class="m-0" onsubmit="return confirm('Annuler cette collecte ?')">
                @csrf
                <button type="submit" class="wc-btn wc-btn-danger-soft wc-btn-sm">
                    <i class="fas fa-times"></i> Annuler
                </button>
            </form>
            @endif
        </div>
    </div>

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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
