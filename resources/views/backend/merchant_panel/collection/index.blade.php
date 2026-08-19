@extends('backend.partials.master')
@section('title', 'Collectes')
@section('maincontent')

@php
    $todayStr = now()->toDateString();
    $filters = [
        'all' => ['label' => 'Toutes', 'icon' => 'fas fa-list-ul', 'key' => 'all'],
        'today' => ['label' => "Aujourd'hui", 'icon' => 'fas fa-calendar-day', 'key' => 'today'],
        'upcoming' => ['label' => 'À venir', 'icon' => 'fas fa-clock', 'key' => 'upcoming'],
        'active' => ['label' => 'En cours', 'icon' => 'fas fa-route', 'key' => 'active'],
        'completed' => ['label' => 'Terminées', 'icon' => 'fas fa-check-circle', 'key' => 'completed'],
        'cancelled' => ['label' => 'Annulées', 'icon' => 'fas fa-ban', 'key' => 'cancelled'],
        'pending_assignment' => ['label' => 'En attente', 'icon' => 'fas fa-hourglass-half', 'key' => 'pending_assignment'],
    ];
    // Groupes de date pour les filtres colis
    $dateGroups = collect($parcelOptions)->pluck('date_label')->unique()->values()->all();
@endphp

<style>
/* ── Wizard animations ── */
@keyframes wcSlideIn { from { opacity:0; transform:translateX(24px); } to { opacity:1; transform:translateX(0); } }
@keyframes wcSlideOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(-24px); } }
@keyframes wcPopIn { 0% { opacity:0; transform:scale(.92); } 100% { opacity:1; transform:scale(1); } }
@keyframes wcCheckDraw { 0% { stroke-dashoffset:36; } 100% { stroke-dashoffset:0; } }
@keyframes wcShimmer { 0% { background-position:-200% 0; } 100% { background-position:200% 0; } }
@keyframes wcBounce { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-6px); } }

.wc-wizard-step { display:none; }
.wc-wizard-step.active { display:block; animation:wcSlideIn .35s cubic-bezier(.4,0,.2,1) both; }
.wc-wizard-step.exit { animation:wcSlideOut .25s cubic-bezier(.4,0,.2,1) both; }

.wc-step-dot { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; transition:all .3s ease; border:2px solid #e5e7eb; color:#9ca3af; background:#fff; }
.wc-step-dot.active { border-color:#059669; color:#fff; background:#059669; box-shadow:0 0 0 4px rgba(5,150,105,.15); }
.wc-step-dot.done { border-color:#059669; color:#fff; background:#059669; }
.wc-step-line { flex:1; height:2px; background:#e5e7eb; transition:background .4s ease; }
.wc-step-line.done { background:#059669; }

.wc-parcel-card { border:1.5px solid #e7ebe9; border-radius:12px; padding:12px 14px; cursor:pointer; transition:all .2s ease; position:relative; overflow:hidden; }
.wc-parcel-card:hover { border-color:#a7f3d0; background:#f0fdf4; }
.wc-parcel-card.selected { border-color:#059669; background:#ecfdf5; box-shadow:0 0 0 3px rgba(5,150,105,.1); }
.wc-parcel-card.selected::after { content:'✓'; position:absolute; top:8px; right:10px; width:22px; height:22px; border-radius:50%; background:#059669; color:#fff; font-size:11px; font-weight:800; display:flex; align-items:center; justify-content:center; animation:wcPopIn .25s ease; }
.wc-parcel-card input { display:none; }

.wc-success-check { width:80px; height:80px; margin:0 auto; }
.wc-success-check circle { fill:none; stroke:#059669; stroke-width:3; stroke-linecap:round; }
.wc-success-check .wc-check-ring { stroke-dasharray:226; stroke-dashoffset:226; animation:wcCheckDraw .6s .2s cubic-bezier(.4,0,.2,1) forwards; }
.wc-success-check .wc-check-mark { stroke-dasharray:36; stroke-dashoffset:36; animation:wcCheckDraw .4s .6s cubic-bezier(.4,0,.2,1) forwards; }

.wc-slot-chip { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:999px; border:1.5px solid #e7ebe9; background:#fff; color:#64748b; font-size:13px; font-weight:600; cursor:pointer; transition:all .2s ease; }
.wc-slot-chip:hover { border-color:#a7f3d0; color:#059669; background:#f0fdf4; }
.wc-slot-chip.active { border-color:#059669; color:#fff; background:#059669; }
.wc-slot-chip i { font-size:11px; }

.wc-search-glow:focus { box-shadow:0 0 0 4px rgba(5,150,105,.1), 0 2px 8px rgba(5,150,105,.08); }

.wc-summary-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #f1f5f9; }
.wc-summary-row:last-child { border-bottom:none; }

.wc-fab-create { position:fixed; bottom:24px; right:24px; z-index:1050; width:56px; height:56px; border-radius:16px; background:linear-gradient(135deg,#059669,#047857); color:#fff; border:none; font-size:22px; cursor:pointer; box-shadow:0 8px 24px rgba(5,150,105,.3); transition:all .25s ease; display:flex; align-items:center; justify-content:center; }
.wc-fab-create:hover { transform:translateY(-3px) scale(1.05); box-shadow:0 12px 32px rgba(5,150,105,.4); }
@media(min-width:1280px) { .wc-fab-create { display:none; } }
</style>

<div class="container-fluid dashboard-content">

    {{-- ─── HEADER ─────────────────────────────────── --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title"><i class="fas fa-truck-loading text-wc-primary"></i> Collectes</h1>
            <p class="wc-page-subtitle">Planifiez, suivez et gérez vos collectes de colis</p>
        </div>
        <div class="wc-toolbar">
            <button type="button" class="wc-btn wc-btn-primary wc-btn-float" onclick="openWizard()">
                <i class="fas fa-plus"></i> Planifier une collecte
            </button>
        </div>
    </div>

    {{-- ─── KPI CARDS ──────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.02s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">Collectes aujourd'hui</p>
                <div class="wc-card-icon bg-[#ecfdf5] text-[#059669]"><i class="fas fa-calendar-check"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ $stats['today'] }}</p>
            <p class="wc-kpi-sub neutral m-0"><i class="fas fa-circle text-[6px]"></i> prévues</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.06s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">En cours</p>
                <div class="wc-card-icon bg-[#eff6ff] text-[#2563eb]"><i class="fas fa-route"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ $stats['active'] }}</p>
            <p class="wc-kpi-sub positive m-0"><i class="fas fa-circle text-[6px]"></i> {{ $stats['pending_assignment'] }} en attente</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.1s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">Terminées</p>
                <div class="wc-card-icon bg-[#f5f3ff] text-[#7c3aed]"><i class="fas fa-check-double"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ $stats['completed'] }}</p>
            <p class="wc-kpi-sub neutral m-0"><i class="fas fa-circle text-[6px]"></i> aujourd'hui</p>
        </div>
        <div class="wc-kpi-card animate-wcFadeUp" style="animation-delay:.14s">
            <div class="flex items-center justify-between">
                <p class="wc-kpi-label m-0">Colis en attente</p>
                <div class="wc-card-icon bg-[#fffbeb] text-[#d97706]"><i class="fas fa-box"></i></div>
            </div>
            <p class="wc-kpi-value m-0">{{ $stats['pending_parcels'] }}</p>
            <p class="wc-kpi-sub neutral m-0"><i class="fas fa-circle text-[6px]"></i> à collecter</p>
        </div>
    </div>

    {{-- ─── GPS LIVE PANEL ─────────────────────────── --}}
    @if($gpsCollection && $gpsCollection->deliveryMan && $gpsCollection->deliveryMan->current_location_lat)
    <div class="wc-card mb-5 animate-wcFadeUp" style="animation-delay:.18s;border:none;background:linear-gradient(135deg,#059669,#047857);">
        <div class="p-5 text-white">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center"><i class="fas fa-motorcycle text-white text-lg"></i></div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-green-400 rounded-full border-2 border-green-700 animate-pulse"></span>
                    </div>
                    <div>
                        <h3 class="text-white font-extrabold text-lg m-0">{{ $gpsCollection->deliveryMan->user->name }}</h3>
                        <p class="text-white/70 text-xs m-0">Collecte #{{ $gpsCollection->id }} · {{ $gpsCollection->status_label }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-white/60 text-xs m-0 mb-1">Position GPS</p>
                        <p class="text-white font-bold text-sm m-0" id="gpsCoords">{{ $gpsCollection->deliveryMan->current_location_lat ?? '—' }}, {{ $gpsCollection->deliveryMan->current_location_long ?? '—' }}</p>
                    </div>
                    @if($gpsCollection->deliveryMan->current_location_lat)
                    <a href="https://www.google.com/maps?q={{ $gpsCollection->deliveryMan->current_location_lat }},{{ $gpsCollection->deliveryMan->current_location_long }}" target="_blank" class="wc-btn !bg-white/20 !border-white/30 !text-white wc-btn-sm"><i class="fas fa-external-link-alt"></i> Ouvrir</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ─── FILTERS + TABLE ────────────────────────── --}}
    <div class="wc-card mb-5 animate-wcFadeUp" style="animation-delay:.22s">
        <div class="flex items-center gap-3 flex-wrap px-4 py-3 border-b border-wc-border bg-[#f8fafc]">
            <div class="flex items-center gap-1.5 flex-1 flex-wrap" id="colFilters">
                @foreach($filters as $key => $f)
                <a href="{{ route('merchant-panel.collection.index', ['filter' => $key]) }}" class="wc-btn {{ $filter === $key ? 'wc-btn-primary' : 'wc-btn-soft' }} wc-btn-sm wc-col-filter transition-all">
                    <i class="{{ $f['icon'] }}" style="font-size:11px;"></i> {{ $f['label'] }}
                    @if($key === 'pending_assignment' && $stats['pending_assignment'] > 0)
                    <span class="ml-0.5 px-1.5 py-0.5 text-[10px] font-bold rounded-full {{ $filter === $key ? 'bg-white/25' : 'bg-wc-primary-soft text-wc-primary' }}">{{ $stats['pending_assignment'] }}</span>
                    @endif
                </a>
                @endforeach
            </div>
            <span class="text-[12.5px] text-wc-muted wc-tabular whitespace-nowrap">{{ $collections->total() }} résultat(s)</span>
        </div>

        @if($collections->count() > 0)
        <div class="wc-table-wrap">
            <table class="wc-table">
                <thead><tr>
                    <th style="width:60px;">#</th><th>Boutique</th><th>Date & Créneau</th><th>Colis</th><th>Cash</th><th>Livreur</th><th>Statut</th><th class="text-right">Actions</th>
                </tr></thead>
                <tbody>
                    @foreach($collections as $c)
                    @php
                        $statusBadge = match($c->status) { 1=>'wc-badge-warning', 2=>'wc-badge-info', 3=>'wc-badge-transit', 4=>'wc-badge-success', 5=>'wc-badge-delivered', 6=>'wc-badge-error', default=>'wc-badge-neutral' };
                        $steps = [['s'=>1,'icon'=>'fas fa-plus-circle'],['s'=>2,'icon'=>'fas fa-user-check'],['s'=>3,'icon'=>'fas fa-motorcycle'],['s'=>4,'icon'=>'fas fa-box-open'],['s'=>5,'icon'=>'fas fa-check-double']];
                    @endphp
                    <tr class="animate-wcRowIn" style="animation-delay:{{ $loop->iteration * 0.03 }}s">
                        <td class="wc-tabular font-bold text-wc-ink">#{{ $c->id }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="wc-avatar !bg-[#ecfdf5] !text-[#059669] !w-8 !h-8 !text-xs">{{ strtoupper(mb_substr(trim($c->shop?->name ?? '?'), 0, 2)) }}</div>
                                <div><div class="font-bold text-wc-ink text-[13px]">{{ $c->shop?->name ?? '—' }}</div>@if($c->shop?->address)<div class="text-[11px] text-wc-muted-2 truncate max-w-[160px]">{{ $c->shop->address }}</div>@endif</div>
                            </div>
                        </td>
                        <td>
                            <div class="font-semibold text-wc-ink text-[13px]">{{ $c->collection_date ?? '—' }}</div>
                            @if($c->time_slot)<div class="text-[11px] text-wc-muted-2"><i class="fas fa-clock mr-1"></i>{{ $c->time_slot }}</div>@endif
                        </td>
                        <td>
                            <div class="flex items-center gap-1.5"><span class="font-extrabold text-wc-ink text-lg">{{ $c->parcel_count }}</span><span class="text-[11px] text-wc-muted-2">colis</span></div>
                            <div class="flex items-center gap-0 mt-1.5">
                                @foreach($steps as $i=>$st)<div class="flex items-center"><span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[7px] {{ $c->status>=$st['s']?'bg-wc-primary text-white':'bg-gray-200 text-gray-400' }} {{ $c->status==$st['s']?'ring-2 ring-wc-primary/30':'' }}"><i class="{{ $st['icon'] }}" style="font-size:6px;"></i></span></div>@if($i<count($steps)-1)<span class="flex-1 h-[2px] min-w-[12px] {{ $c->status>$st['s']?'bg-wc-primary':'bg-gray-200' }}"></span>@endif
                                @endforeach
                            </div>
                        </td>
                        <td><span class="font-extrabold text-wc-primary text-[14px] wc-tabular">{{ number_format($c->total_cash_collection, 0, ',', ' ') }}</span><span class="text-[10px] text-wc-muted-2 ml-0.5">FCFA</span></td>
                        <td>
                            @if($c->deliveryMan)<div class="flex items-center gap-2"><div class="relative"><div class="wc-avatar !w-7 !h-7 !text-[10px] !bg-[#eff6ff] !text-[#2563eb]">{{ strtoupper(mb_substr(trim($c->deliveryMan->user->name), 0, 1)) }}</div>@if(in_array($c->status,[2,3]))<span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-400 rounded-full border-2 border-white"></span>@endif</div><span class="text-[12.5px] font-semibold text-wc-ink-2">{{ $c->deliveryMan->user->name }}</span></div>@else<span class="text-[12px] text-wc-muted-2 italic">En attente</span>@endif
                        </td>
                        <td><span class="wc-badge {{ $statusBadge }}">{{ $c->status_label }}</span></td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if(in_array($c->status,[1,2]))<form action="{{ route('merchant-panel.collection.cancel', $c->id) }}" method="POST" class="m-0" onsubmit="var r=prompt('Raison de l\'annulation (optionnel) :');if(r===null)return false;this.insertAdjacentHTML('beforeend','<input type=\"hidden\" name=\"cancel_reason\" value=\"'+r+'\">');return confirm('Annuler cette collecte ?');">@csrf<button type="submit" class="wc-btn wc-btn-danger-soft wc-btn-sm" title="Annuler"><i class="fas fa-times"></i></button></form>@endif
                                @if(in_array($c->status,[2,3]) && $c->deliveryMan)<a href="https://www.google.com/maps?q={{ $c->deliveryMan->current_location_lat }},{{ $c->deliveryMan->current_location_long }}" target="_blank" class="wc-btn wc-btn-soft wc-btn-sm !text-wc-info" title="GPS"><i class="fas fa-map-marker-alt"></i></a>@endif
                                <a href="{{ route('merchant-panel.collection.detail', $c->id) }}" class="wc-btn wc-btn-soft wc-btn-sm" title="Détails"><i class="fas fa-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
            <p class="m-0 text-[12.5px] text-wc-muted">Affichage <span class="font-bold text-wc-ink">{{ $collections->firstItem() }}</span> à <span class="font-bold text-wc-ink">{{ $collections->lastItem() }}</span> sur <span class="font-bold text-wc-ink">{{ $collections->total() }}</span></p>
            <span class="flex items-center gap-1">{{ $collections->withQueryString()->links() }}</span>
        </div>
        @else
        <div class="wc-empty">
            <div class="wc-empty-icon"><i class="fas fa-truck-loading"></i></div>
            <p class="wc-empty-title">Aucune collecte</p>
            <p class="wc-empty-description">Planifiez votre première collecte pour récupérer vos colis.</p>
            <button type="button" class="wc-btn wc-btn-primary wc-btn-sm mt-2" onclick="openWizard()"><i class="fas fa-plus"></i> Planifier une collecte</button>
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- MODAL : Planifier une collecte (formulaire unique)        --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="createModal" class="wc-modal hidden" onclick="if(event.target===this)closeModal()">
    <div class="wc-modal-content" style="max-width:720px;">

        <div class="wc-modal-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-[#ecfdf5] text-[#059669]"><i class="fas fa-truck-loading"></i></div>
                <h3 class="wc-modal-title">Planifier une collecte</h3>
            </div>
            <button type="button" class="wc-btn wc-btn-soft wc-btn-sm !min-w-0 !p-2" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>

        <form id="createColForm" method="POST" action="{{ route('merchant-panel.collection.store') }}">
            @csrf
            <div class="wc-modal-body">

                {{-- ── Ligne 1 : Boutique + Date + Créneau ── --}}
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="wc-form-group">
                        <label class="wc-label"><i class="fas fa-store text-wc-primary mr-1"></i> Boutique</label>
                        <select name="shop_id" id="col_shop" class="wc-select">
                            @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ $shop->default_shop ? 'selected' : '' }}>{{ $shop->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="wc-form-group">
                        <label class="wc-label"><i class="fas fa-calendar text-wc-primary mr-1"></i> Date</label>
                        <input type="date" name="collection_date" id="col_date" class="wc-input" value="{{ $todayStr }}" min="{{ $todayStr }}">
                    </div>
                    <div class="wc-form-group">
                        <label class="wc-label"><i class="fas fa-clock text-wc-primary mr-1"></i> Créneau</label>
                        <select name="time_slot" id="col_slot" class="wc-select">
                            <option value="08:00-10:00">08:00 – 10:00</option>
                            <option value="10:00-12:00">10:00 – 12:00</option>
                            <option value="12:00-14:00">12:00 – 14:00</option>
                            <option value="14:00-16:00">14:00 – 16:00</option>
                            <option value="16:00-18:00">16:00 – 18:00</option>
                        </select>
                    </div>
                </div>

                {{-- ── Ligne 2 : Note ── --}}
                <div class="wc-form-group mb-4">
                    <label class="wc-label">Note <span class="text-wc-muted font-normal">(optionnel)</span></label>
                    <input type="text" name="note" class="wc-input" placeholder="Instructions pour le livreur..." maxlength="500">
                </div>

                {{-- ── Sélection des colis ── --}}
                <div class="border-t border-wc-border pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <label class="wc-label m-0">
                            <i class="fas fa-box text-wc-primary mr-1"></i> Colis à récupérer
                        </label>
                        <span class="text-[12px] font-bold text-wc-primary wc-tabular" id="parcelCountLabel">0 sélectionné(s) · 0 FCFA</span>
                    </div>

                    @if($pendingParcels->count() > 0)
                    {{-- Barre d'outils : recherche + filtres date + select all --}}
                    <div class="flex items-center gap-2 flex-wrap mb-3">
                        <div class="relative flex-1 min-w-[180px]">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[12px] pointer-events-none"></i>
                            <input type="text" id="parcelSearch" class="wc-input wc-input-sm !pl-8" placeholder="Rechercher...">
                        </div>
                        <div class="flex items-center gap-1" id="dateFilters">
                            <button type="button" class="wc-btn wc-btn-primary wc-btn-sm date-filter-btn" data-date="all">Tous</button>
                            @foreach($dateGroups as $dg)
                            <button type="button" class="wc-btn wc-btn-soft wc-btn-sm date-filter-btn" data-date="{{ $dg }}">{{ $dg }}</button>
                            @endforeach
                        </div>
                        <label class="flex items-center gap-1.5 cursor-pointer flex-shrink-0">
                            <input type="checkbox" class="wc-checkbox" id="selectAllP" onchange="toggleAll(this.checked)">
                            <span class="text-[11.5px] font-semibold text-wc-muted">Tout</span>
                        </label>
                    </div>

                    {{-- Liste des colis --}}
                    <div class="border border-wc-border rounded-xl overflow-hidden max-h-[320px] overflow-y-auto scrollbar-thin" id="parcelGrid">
                        @foreach($parcelOptions as $po)
                        <label class="flex items-center gap-3 px-4 py-2.5 border-b border-wc-border last:border-b-0 hover:bg-wc-primary-faint cursor-pointer transition-colors"
                               for="pc_{{ $po['id'] }}"
                               data-date="{{ $po['date_label'] }}"
                               data-search="{{ mb_strtolower($po['name'].' '.$po['tracking'].' '.$po['address']) }}">
                            <input type="checkbox" name="parcel_ids[]" value="{{ $po['id'] }}" class="wc-checkbox parcel-cb" id="pc_{{ $po['id'] }}" onchange="refreshCount()">
                            <div class="wc-avatar !w-8 !h-8 !text-[10px] !bg-[#ecfdf5] !text-[#059669] flex-shrink-0">{{ strtoupper(mb_substr(trim($po['name']), 0, 2)) }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <code class="text-[11px] font-bold text-wc-primary bg-wc-primary-soft px-2 py-0.5 rounded-md">{{ $po['tracking'] }}</code>
                                    <span class="text-[13px] font-semibold text-wc-ink">{{ $po['name'] }}</span>
                                    <span class="text-[10px] text-wc-muted-2 bg-gray-100 px-1.5 py-0.5 rounded">{{ $po['date_label'] }}</span>
                                </div>
                                <div class="text-[11px] text-wc-muted-2 truncate mt-0.5">{{ $po['address'] }}</div>
                            </div>
                            <span class="font-extrabold text-[13px] text-wc-primary wc-tabular whitespace-nowrap flex-shrink-0">{{ number_format($po['cash'], 0, ',', ' ') }} <span class="text-[10px] text-wc-muted-2 font-normal">FCFA</span></span>
                        </label>
                        @endforeach
                    </div>

                    <div id="noResult" class="hidden text-center py-6 text-wc-muted-2">
                        <i class="fas fa-search text-xl mb-2 block opacity-40"></i>
                        <p class="text-[12.5px]">Aucun colis ne correspond.</p>
                    </div>
                    @else
                    <div class="wc-empty !py-8">
                        <div class="wc-empty-icon !bg-[#ecfdf5]"><i class="fas fa-check-circle"></i></div>
                        <p class="wc-empty-title">Tout est collecté !</p>
                        <p class="wc-empty-description">Aucun colis en attente.</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="wc-modal-footer">
                <button type="button" class="wc-btn wc-btn-soft" onclick="closeModal()">Annuler</button>
                <button type="submit" class="wc-btn wc-btn-success" id="submitBtn">
                    <i class="fas fa-truck"></i> Créer la collecte
                </button>
            </div>
        </form>
    </div>
</div>

{{-- FAB mobile --}}
<button class="wc-fab-create" onclick="openWizard()" title="Planifier une collecte"><i class="fas fa-plus"></i></button>

@endsection


@push('scripts')
<script>
"use strict";

/* ── Modal open/close ── */
function openWizard() {
    document.getElementById('createModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.body.style.overflow = '';
}

/* ── Filtres date ── */
var activeDateFilter = 'all';
var dateBtns = document.querySelectorAll('.date-filter-btn');
var searchInput = document.getElementById('parcelSearch');
var noResult = document.getElementById('noResult');

function applyFilters() {
    var q = searchInput ? searchInput.value.toLowerCase().trim() : '';
    var labels = document.querySelectorAll('#parcelGrid > label');
    var visible = 0;
    labels.forEach(function(row) {
        var matchDate = activeDateFilter === 'all' || row.dataset.date === activeDateFilter;
        var matchSearch = !q || row.dataset.search.indexOf(q) !== -1;
        var show = matchDate && matchSearch;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    if (noResult) noResult.classList.toggle('hidden', visible > 0);
}

dateBtns.forEach(function(btn) {
    btn.addEventListener('click', function() {
        dateBtns.forEach(function(b) { b.classList.remove('wc-btn-primary'); b.classList.add('wc-btn-soft'); });
        btn.classList.remove('wc-btn-soft'); btn.classList.add('wc-btn-primary');
        activeDateFilter = btn.dataset.date;
        applyFilters();
    });
});

if (searchInput) searchInput.addEventListener('input', applyFilters);

/* ── Select all / refresh count ── */
function toggleAll(checked) {
    document.querySelectorAll('#parcelGrid > label').forEach(function(row) {
        if (row.style.display === 'none') return;
        var cb = row.querySelector('.parcel-cb');
        if (cb) cb.checked = checked;
    });
    refreshCount();
}

function refreshCount() {
    var cbs = document.querySelectorAll('.parcel-cb:checked');
    var n = cbs.length;
    var cash = 0;
    cbs.forEach(function(cb) {
        var row = cb.closest('label');
        if (!row) return;
        var raw = row.querySelector('.font-extrabold');
        if (raw) cash += parseFloat(raw.textContent.replace(/\s/g, '').replace(',', '.')) || 0;
    });
    var el = document.getElementById('parcelCountLabel');
    if (el) el.textContent = n + ' sélectionné(s) · ' + new Intl.NumberFormat('fr-FR').format(cash) + ' FCFA';
}

/* ── Form submit AJAX ── */
document.getElementById('createColForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var cbs = document.querySelectorAll('.parcel-cb:checked');
    if (cbs.length === 0) {
        var grid = document.getElementById('parcelGrid');
        grid.style.animation = 'none'; grid.offsetHeight; grid.style.animation = 'wcShake .4s ease';
        return;
    }
    var btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<div class="wc-spinner !w-4 !h-4 !border-2 !border-white/30 !border-t-white"></div> Création...';

    var fd = new FormData(this);
    fd.delete('parcel_ids[]');
    cbs.forEach(function(cb) { fd.append('parcel_ids[]', cb.value); });

    fetch(this.action, {
        method: 'POST', body: fd,
        headers: { 'X-Request-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) { location.reload(); }
        else { btn.disabled = false; btn.innerHTML = '<i class="fas fa-truck"></i> Créer la collecte'; alert(d.message || 'Erreur.'); }
    })
    .catch(function() { btn.disabled = false; btn.innerHTML = '<i class="fas fa-truck"></i> Créer la collecte'; alert('Erreur réseau.'); });
});

/* ── GPS polling ── */
var gpsId = {{ $gpsCollection ? $gpsCollection->id : 'null' }};
if (gpsId) {
    setInterval(function() {
        fetch('{{ url("merchant/collection") }}/' + gpsId + '/tracking', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.collection && d.collection.delivery_man) {
                var el = document.getElementById('gpsCoords');
                if (el && d.collection.delivery_man.lat) el.textContent = d.collection.delivery_man.lat + ', ' + d.collection.delivery_man.lng;
            }
        });
    }, 10000);
}

/* ── Echo ── */
if (typeof Echo !== 'undefined') {
    Echo.private('merchant.collection.{{ Auth::user()->merchant->id }}').listen('.collection.status.changed', function() { location.reload(); });
}
</script>
<style>@keyframes wcShake{0%,100%{transform:translateX(0)}20%{transform:translateX(-6px)}40%{transform:translateX(6px)}60%{transform:translateX(-4px)}80%{transform:translateX(4px)}}</style>
@endpush
