@extends('backend.partials.master')
@section('title', 'Détails Collecte #' . $collection->id)
@push('styles')
@include('backend.admin.collection._styles')
<style>
.cl-timeline{position:relative}
.cl-timeline-step{display:flex;align-items:flex-start;gap:14px;position:relative;padding-bottom:22px}
.cl-timeline-step:last-child{padding-bottom:0}
.cl-timeline-line{position:absolute;left:18px;top:42px;bottom:4px;width:2px}
.cl-timeline-dot{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;position:relative;z-index:1;transition:all .3s ease}
.cl-timeline-dot.done{background:var(--cl-green);color:#fff;box-shadow:0 4px 10px rgba(5,150,105,.25)}
.cl-timeline-dot.active{background:var(--cl-green);color:#fff;box-shadow:0 0 0 5px rgba(5,150,105,.15);transform:scale(1.08)}
.cl-timeline-dot.todo{background:#f1f5f9;color:#94a3b8;border:2px solid #e2e8f0}
.cl-summary-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f1f5f9}
.cl-summary-row:last-child{border-bottom:none}
.cl-gps-card{background:linear-gradient(135deg,#059669,#047857);border:none;border-radius:16px;color:#fff}
.cl-status-banner{border-radius:14px;padding:14px 16px;display:flex;align-items:flex-start;gap:12px;margin-bottom:18px}
.cl-status-banner.cancelled{background:#fef2f2;border:1px solid #fecaca}
.cl-btn-soft[disabled]{opacity:.45;cursor:not-allowed;transform:none!important}
.cl-toast{position:fixed;top:18px;right:18px;z-index:3000;display:flex;align-items:center;gap:10px;padding:12px 18px;border-radius:12px;font-size:13px;font-weight:600;box-shadow:0 10px 30px rgba(15,23,42,.18);animation:clToastIn .3s ease both}
.cl-toast.success{background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0}
.cl-toast.error{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
@keyframes clToastIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
</style>
@endpush
@section('maincontent')

@php
    $isCancelled = $collection->status == 6;
    $badgeMap = [1 => 'cl-badge-warning', 2 => 'cl-badge-info', 3 => 'cl-badge-transit', 4 => 'cl-badge-success', 5 => 'cl-badge-delivered', 6 => 'cl-badge-error'];
    $steps = [
        ['s' => 1, 'label' => 'Créée', 'icon' => 'fas fa-plus-circle', 'desc' => 'Collecte planifiée par le marchand'],
        ['s' => 2, 'label' => 'Affectée', 'icon' => 'fas fa-user-check', 'desc' => 'Un livreur a été assigné'],
        ['s' => 3, 'label' => 'Ramassage', 'icon' => 'fas fa-motorcycle', 'desc' => 'Le livreur est en route'],
        ['s' => 4, 'label' => 'Collectée', 'icon' => 'fas fa-box-open', 'desc' => 'Colis récupérés chez le marchand'],
        ['s' => 5, 'label' => 'Terminée', 'icon' => 'fas fa-check-double', 'desc' => 'Mission achevée, livreur libéré'],
    ];
@endphp

<div class="container-fluid dashboard-content cl-col-page">

    {{-- ─── HEADER ─────────────────────────────────── --}}
    <div class="cl-header">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <a href="{{ route('admin.collection.index') }}" class="cl-btn cl-btn-soft cl-btn-sm cl-btn-icon" title="Retour"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h1 class="cl-title">Collecte <span class="cl-tabular">#{{ $collection->id }}</span></h1>
                <p class="cl-subtitle">Détails, suivi et gestion de la collecte</p>
            </div>
            <span class="cl-badge {{ $badgeMap[$collection->status] ?? 'cl-badge-neutral' }}" id="statusBadge">{{ $collection->status_label }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;" id="actionBar">
            @if($collection->status == 1 && !$collection->delivery_man_id)
            <a href="{{ route('admin.collection.index', ['status' => 1]) }}" class="cl-btn cl-btn-soft cl-btn-sm"><i class="fas fa-user-plus"></i> Affecter un livreur</a>
            @endif
            @if(in_array($collection->status, [2, 3, 4]))
            <button type="button" class="cl-btn cl-btn-primary cl-btn-sm" data-status="3" id="actStart" {{ $collection->status != 2 ? 'disabled' : '' }}><i class="fas fa-truck"></i> Démarrer ramassage</button>
            <button type="button" class="cl-btn cl-btn-soft cl-btn-sm" data-status="4" id="actCollected" {{ $collection->status != 3 ? 'disabled' : '' }}><i class="fas fa-check"></i> Marquer collectée</button>
            <button type="button" class="cl-btn cl-btn-primary cl-btn-sm" data-status="5" id="actComplete" {{ !in_array($collection->status, [3, 4]) ? 'disabled' : '' }}><i class="fas fa-flag-checkered"></i> Terminer</button>
            <button type="button" class="cl-btn cl-btn-danger cl-btn-sm" data-status="6" id="actCancel"><i class="fas fa-times"></i> Annuler</button>
            @endif
        </div>
    </div>

    @if($isCancelled)
    <div class="cl-status-banner cancelled">
        <div style="width:38px;height:38px;border-radius:10px;background:#fee2e2;color:#dc2626;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-ban"></i></div>
        <div>
            <p class="cl-fw-8" style="color:#b91c1c;margin:0;font-size:13.5px;">Collecte annulée</p>
            @if($collection->cancel_reason)<p class="cl-fs-12" style="color:#dc2626;margin:2px 0 0;">Raison : {{ $collection->cancel_reason }}</p>@endif
            @if($collection->cancelled_at)<p class="cl-fs-11" style="color:#f87171;margin:2px 0 0;">Annulée le {{ $collection->cancelled_at->format('d/m/Y à H:i') }} — les colis ont été remis en attente.</p>@endif
        </div>
    </div>
    @endif

    <div class="row">
        {{-- ─── Colonne principale ─────────────────── --}}
        <div class="col-lg-8">
            {{-- Timeline --}}
            <div class="cl-card mb-4" style="animation:clFadeUp .35s ease both;">
                <div class="cl-card-head" style="display:flex;align-items:center;gap:10px;padding:16px 18px;border-bottom:1px solid var(--cl-border);">
                    <div style="width:34px;height:34px;border-radius:10px;background:var(--cl-green-soft);color:var(--cl-green);display:flex;align-items:center;justify-content:center;"><i class="fas fa-route"></i></div>
                    <div>
                        <h3 class="cl-fs-15 cl-fw-8 cl-ink" style="margin:0;">Progression</h3>
                        <p class="cl-fs-12 cl-muted" style="margin:0;">Suivi de la collecte en temps réel</p>
                    </div>
                </div>
                <div style="padding:20px 18px;">
                    <div class="cl-timeline" id="timeline">
                        @foreach($steps as $i => $step)
                        <div class="cl-timeline-step">
                            @if($i < count($steps) - 1)
                            <div class="cl-timeline-line {{ $collection->status > $step['s'] ? 'done' : 'todo' }}" style="background:{{ $collection->status > $step['s'] ? 'var(--cl-green)' : '#e5e7eb' }};"></div>
                            @endif
                            <div class="cl-timeline-dot {{ $collection->status >= $step['s'] ? ($collection->status == $step['s'] ? 'active' : 'done') : 'todo' }}"><i class="{{ $step['icon'] }}"></i></div>
                            <div style="flex:1;padding-top:4px;">
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <span class="cl-fs-14 cl-fw-7 {{ $collection->status >= $step['s'] ? 'cl-ink' : 'cl-muted-2' }}">{{ $step['label'] }}</span>
                                    @if($collection->status == $step['s'])
                                    <span class="cl-badge cl-badge-success" style="font-size:9.5px;">Actuel</span>
                                    @endif
                                </div>
                                <p class="cl-fs-12 {{ $collection->status >= $step['s'] ? 'cl-muted' : 'cl-muted-2' }}" style="margin:2px 0 0;">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Colis --}}
            <div class="cl-card mb-4" style="animation:clFadeUp .35s .06s ease both;">
                <div class="cl-card-head" style="display:flex;align-items:center;gap:10px;padding:16px 18px;border-bottom:1px solid var(--cl-border);">
                    <div style="width:34px;height:34px;border-radius:10px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;"><i class="fas fa-box"></i></div>
                    <div>
                        <h3 class="cl-fs-15 cl-fw-8 cl-ink" style="margin:0;">Colis ({{ $collection->parcels->count() }})</h3>
                        <p class="cl-fs-12 cl-muted" style="margin:0;">Colis inclus dans cette collecte</p>
                    </div>
                </div>
                @if($collection->parcels->count() > 0)
                <div class="cl-table-wrap">
                    <table class="cl-table">
                        <thead><tr><th>Tracking</th><th>Destinataire</th><th>Adresse</th><th>Cash</th><th>Frais</th><th>Statut colis</th></tr></thead>
                        <tbody>
                            @foreach($collection->parcels as $p)
                            @php $picked = $p->pivot->status == 1; @endphp
                            <tr>
                                <td><code class="cl-code">{{ $p->tracking_id }}</code></td>
                                <td class="cl-fw-6 cl-ink cl-fs-13">{{ $p->customer_name }}</td>
                                <td class="cl-fs-12 cl-muted-2 cl-truncate" style="max-width:180px;">{{ $p->customer_address }}</td>
                                <td class="cl-fw-8 cl-green cl-tabular">{{ number_format($p->cash_collection, 0, ',', ' ') }}</td>
                                <td class="cl-fw-6 cl-ink-2 cl-tabular">{{ number_format($p->total_delivery_amount, 0, ',', ' ') }}</td>
                                <td><span class="cl-badge {{ $picked ? 'cl-badge-success' : 'cl-badge-neutral' }}">{{ $picked ? 'Ramassé' : 'En attente' }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="cl-empty">
                    <div class="cl-empty-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-box-open"></i></div>
                    <p class="cl-empty-title">Aucun colis</p>
                    <p class="cl-empty-desc">Cette collecte ne contient aucun colis.</p>
                </div>
                @endif
            </div>

            {{-- Suivi de caisse COD --}}
            @if($collection->cashTrackings->count() > 0)
            <div class="cl-card mb-4" style="animation:clFadeUp .35s .12s ease both;">
                <div class="cl-card-head" style="display:flex;align-items:center;gap:10px;padding:16px 18px;border-bottom:1px solid var(--cl-border);">
                    <div style="width:34px;height:34px;border-radius:10px;background:#fffbeb;color:#d97706;display:flex;align-items:center;justify-content:center;"><i class="fas fa-coins"></i></div>
                    <div>
                        <h3 class="cl-fs-15 cl-fw-8 cl-ink" style="margin:0;">Suivi de caisse COD</h3>
                        <p class="cl-fs-12 cl-muted" style="margin:0;">Encaissement, remise et réconciliation</p>
                    </div>
                    @php
                        $sumExpected = $collection->cashTrackings->sum('amount_expected');
                        $sumCollected = $collection->cashTrackings->sum('amount_collected');
                        $sumHanded = $collection->cashTrackings->sum('amount_handed_over');
                    @endphp
                    <div style="margin-left:auto;display:flex;gap:6px;flex-wrap:wrap;">
                        <span class="cl-badge cl-badge-neutral">Attendu {{ number_format($sumExpected, 0, ',', ' ') }}</span>
                        <span class="cl-badge cl-badge-transit">Encaissé {{ number_format($sumCollected, 0, ',', ' ') }}</span>
                        <span class="cl-badge cl-badge-delivered">Remis {{ number_format($sumHanded, 0, ',', ' ') }}</span>
                    </div>
                </div>
                <div class="cl-table-wrap">
                    <table class="cl-table">
                        <thead><tr><th>Colis</th><th>Attendu</th><th>Encaissé</th><th>Remis</th><th>Restant</th><th>Statut</th></tr></thead>
                        <tbody>
                            @foreach($collection->cashTrackings as $ct)
                            <tr>
                                <td><code class="cl-code">{{ $ct->parcel?->tracking_id ?? '#' . $ct->parcel_id }}</code></td>
                                <td class="cl-fw-6 cl-tabular">{{ number_format($ct->amount_expected, 0, ',', ' ') }}</td>
                                <td class="cl-fw-6 cl-tabular">{{ number_format($ct->amount_collected, 0, ',', ' ') }}</td>
                                <td class="cl-fw-6 cl-tabular">{{ number_format($ct->amount_handed_over, 0, ',', ' ') }}</td>
                                <td class="cl-fw-7 {{ $ct->amount_remaining > 0 ? 'cl-green' : 'cl-muted-2' }} cl-tabular">{{ number_format($ct->amount_remaining, 0, ',', ' ') }}</td>
                                <td>
                                    <span class="cl-badge {{ match($ct->status) { 1 => 'cl-badge-warning', 2 => 'cl-badge-info', 3 => 'cl-badge-transit', 4 => 'cl-badge-success', 5 => 'cl-badge-error', default => 'cl-badge-neutral' } }}">
                                        {{ $ct->status_label }}
                                    </span>
                                    @if($ct->anomaly_note)<div class="cl-fs-10 cl-muted-2 mt-1">{{ $ct->anomaly_note }}</div>@endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- ─── Colonne latérale ───────────────────── --}}
        <div class="col-lg-4">

            {{-- Livreur GPS --}}
            @if($collection->deliveryMan)
            <div class="cl-card cl-gps-card mb-4" style="animation:clFadeUp .35s .04s ease both;">
                <div style="padding:18px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:15px;"><i class="fas fa-motorcycle"></i></div>
                        <div>
                            <p style="font-weight:800;margin:0;font-size:14px;">{{ $collection->deliveryMan->user->name }}</p>
                            <p style="opacity:.75;margin:0;font-size:11.5px;">{{ $collection->deliveryMan->user->mobile ?? 'Livreur' }}</p>
                        </div>
                        <span style="margin-left:auto;width:10px;height:10px;border-radius:50%;background:#4ade80;box-shadow:0 0 0 4px rgba(74,222,128,.25);animation:clPulse 1.6s ease infinite;"></span>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:14px;background:rgba(255,255,255,.12);border-radius:12px;padding:10px 12px;">
                        <div>
                            <p style="opacity:.65;margin:0;font-size:10.5px;text-transform:uppercase;letter-spacing:.05em;">Position GPS</p>
                            <p style="margin:2px 0 0;font-weight:700;font-size:12px;" id="gpsCoords">{{ $collection->deliveryMan->current_location_lat ? number_format($collection->deliveryMan->current_location_lat, 6).', '.number_format($collection->deliveryMan->current_location_long, 6) : '—' }}</p>
                        </div>
                        @if($collection->deliveryMan->current_location_lat)
                        <a href="https://www.google.com/maps?q={{ $collection->deliveryMan->current_location_lat }},{{ $collection->deliveryMan->current_location_long }}" target="_blank" class="cl-btn cl-btn-sm" style="background:rgba(255,255,255,.2);color:#fff;border:1px solid rgba(255,255,255,.3);"><i class="fas fa-external-link-alt"></i> Carte</a>
                        @endif
                    </div>
                    @if(in_array($collection->status, [3, 4]))
                    <p class="cl-fs-11" style="margin:10px 0 0;opacity:.75;"><i class="fas fa-satellite-dish"></i> Position mise à jour en direct</p>
                    @endif
                </div>
            </div>
            @else
            <div class="cl-card mb-4" style="animation:clFadeUp .35s .04s ease both;">
                <div style="padding:18px;text-align:center;">
                    <div style="width:44px;height:44px;border-radius:12px;background:#fffbeb;color:#d97706;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:15px;"><i class="fas fa-user-clock"></i></div>
                    <p class="cl-fs-13 cl-fw-7 cl-ink" style="margin:0;">En attente d'affectation</p>
                    <p class="cl-fs-12 cl-muted" style="margin:4px 0 12px;">Aucun livreur n'est encore assigné à cette collecte.</p>
                    <a href="{{ route('admin.collection.index', ['status' => 1]) }}" class="cl-btn cl-btn-primary cl-btn-sm"><i class="fas fa-user-plus"></i> Affecter depuis la liste</a>
                </div>
            </div>
            @endif

            {{-- Résumé --}}
            <div class="cl-card mb-4" style="animation:clFadeUp .35s .08s ease both;">
                <div class="cl-card-head" style="display:flex;align-items:center;gap:10px;padding:16px 18px;border-bottom:1px solid var(--cl-border);">
                    <div style="width:34px;height:34px;border-radius:10px;background:#f5f3ff;color:#7c3aed;display:flex;align-items:center;justify-content:center;"><i class="fas fa-info-circle"></i></div>
                    <h3 class="cl-fs-15 cl-fw-8 cl-ink" style="margin:0;">Résumé</h3>
                </div>
                <div style="padding:14px 18px 16px;">
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Marchand</span><span class="cl-fs-13 cl-fw-7 cl-ink" style="text-align:right;">{{ $collection->merchant->business_name ?? $collection->merchant->user->name }}</span></div>
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Boutique</span><span class="cl-fs-13 cl-fw-7 cl-ink" style="text-align:right;">{{ $collection->shop?->name ?? '—' }}</span></div>
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Adresse</span><span class="cl-fs-12 cl-ink-2" style="text-align:right;max-width:55%;">{{ $collection->pickup_address ?? $collection->shop?->address ?? '—' }}</span></div>
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Date</span><span class="cl-fs-13 cl-fw-7 cl-ink">{{ $collection->collection_date ? \Carbon\Carbon::parse($collection->collection_date)->format('d/m/Y') : '—' }}</span></div>
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Créneau</span><span class="cl-fs-13 cl-fw-7 cl-ink">{{ $collection->time_slot ?? '—' }}</span></div>
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Colis</span><span class="cl-fs-14 cl-fw-8 cl-ink cl-tabular">{{ $collection->parcel_count }}</span></div>
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Cash total</span><span class="cl-fs-14 cl-fw-8 cl-green cl-tabular">{{ number_format($collection->total_cash_collection, 0, ',', ' ') }} FCFA</span></div>
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Frais total</span><span class="cl-fs-13 cl-fw-7 cl-ink cl-tabular">{{ number_format($collection->total_delivery_amount, 0, ',', ' ') }} FCFA</span></div>
                    @if($collection->note)
                    <div style="margin-top:12px;background:var(--cl-bg-soft);border-radius:10px;padding:10px 12px;">
                        <p class="cl-fs-11 cl-fw-7 cl-muted" style="margin:0 0 3px;">Note</p>
                        <p class="cl-fs-12 cl-ink-2" style="margin:0;">{{ $collection->note }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Horodatages --}}
            <div class="cl-card" style="animation:clFadeUp .35s .12s ease both;">
                <div class="cl-card-head" style="display:flex;align-items:center;gap:10px;padding:16px 18px;border-bottom:1px solid var(--cl-border);">
                    <h3 class="cl-fs-15 cl-fw-8 cl-ink" style="margin:0;"><i class="fas fa-clock" style="color:var(--cl-muted-2);margin-right:6px;"></i>Horodatages</h3>
                </div>
                <div style="padding:12px 18px 14px;">
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Créée</span><span class="cl-fs-12 cl-fw-7 cl-ink cl-tabular">{{ $collection->created_at->format('d/m/Y H:i') }}</span></div>
                    @if($collection->assigned_at)
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Affectée</span><span class="cl-fs-12 cl-fw-7 cl-ink cl-tabular">{{ $collection->assigned_at->format('d/m/Y H:i') }}</span></div>
                    @endif
                    @if($collection->picked_up_at)
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Ramassage</span><span class="cl-fs-12 cl-fw-7 cl-ink cl-tabular">{{ $collection->picked_up_at->format('d/m/Y H:i') }}</span></div>
                    @endif
                    @if($collection->collected_at)
                    <div class="cl-summary-row"><span class="cl-fs-12 cl-muted">Collectée</span><span class="cl-fs-12 cl-fw-7 cl-ink cl-tabular">{{ $collection->collected_at->format('d/m/Y H:i') }}</span></div>
                    @endif
                    @if($collection->cancelled_at)
                    <div class="cl-summary-row"><span class="cl-fs-12" style="color:#dc2626;">Annulée</span><span class="cl-fs-12 cl-fw-7 cl-tabular" style="color:#dc2626;">{{ $collection->cancelled_at->format('d/m/Y H:i') }}</span></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
"use strict";

var colStatus = {{ $collection->status }};
var colStatusLabel = { 1: 'En attente', 2: 'Affectée', 3: 'En cours de ramassage', 4: 'Collectée', 5: 'Terminée', 6: 'Annulée' };
var colBadgeMap = { 1: 'cl-badge-warning', 2: 'cl-badge-info', 3: 'cl-badge-transit', 4: 'cl-badge-success', 5: 'cl-badge-delivered', 6: 'cl-badge-error' };

function showToast(message, type) {
    var t = document.createElement('div');
    t.className = 'cl-toast ' + (type || 'success');
    t.innerHTML = '<i class="fas fa-' + (type === 'error' ? 'exclamation-circle' : 'check-circle') + '"></i><span>' + message + '</span>';
    document.body.appendChild(t);
    setTimeout(function() { t.style.opacity = '0'; t.style.transition = 'opacity .3s ease'; }, 3200);
    setTimeout(function() { t.remove(); }, 3600);
}

/* ── Actions statut (AJAX + SweetAlert2) ── */
document.querySelectorAll('#actionBar .cl-btn[data-status]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var target = parseInt(this.dataset.status, 10);
        var labels = { 3: 'Démarrer le ramassage', 4: 'Marquer la collecte comme collectée', 5: 'Terminer cette collecte (libère le livreur)', 6: 'Annuler cette collecte' };
        var title = target === 6 ? 'Annuler la collecte ?' : 'Confirmer : ' + labels[target];

        Swal.fire({
            title: title,
            text: target === 6 ? 'Les colis seront remis en attente et le livreur libéré.' : (target === 5 ? 'Le livreur redevient disponible et pourra recevoir de nouvelles missions.' : 'Cette action met à jour le statut de la collecte en temps réel.'),
            icon: target === 6 ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: target === 6 ? 'Oui, annuler' : 'Confirmer',
            cancelButtonText: cancel || 'Annuler',
            confirmButtonColor: target === 6 ? '#dc2626' : '#059669',
            reverseButtons: true
        }).then(function(result) {
            if (!result.isConfirmed) return;
            var original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="cl-spin" style="border-color:rgba(255,255,255,.35);border-top-color:#fff;"></span>';

            fetch('{{ route("admin.collection.status", $collection->id) }}', {
                method: 'PUT',
                body: new URLSearchParams({ status: target }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function(r) {
                return r.json().catch(function() { return { success: false, message: 'Erreur serveur.' }; });
            })
            .then(function(d) {
                if (d.success) {
                    showToast(d.message || 'Statut mis à jour.');
                    setTimeout(function() { location.reload(); }, 1400);
                } else {
                    showToast(d.message || 'Erreur.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = original;
                }
            })
            .catch(function() {
                showToast('Erreur réseau.', 'error');
                btn.disabled = false;
                btn.innerHTML = original;
            });
        });
    });
});

/* ── GPS polling ── */
@if($collection->deliveryMan && in_array($collection->status, [2, 3, 4]))
setInterval(function() {
    fetch('{{ route("admin.collection.deliveryman.location", $collection->deliveryMan->id) }}', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.lat && d.lng) {
                var el = document.getElementById('gpsCoords');
                if (el) el.textContent = Number(d.lat).toFixed(6) + ', ' + Number(d.lng).toFixed(6);
            }
        });
}, 15000);
@endif

/* ── Echo : mise à jour live ── */
if (typeof Echo !== 'undefined') {
    Echo.private('admin.collections').listen('.collection.status.changed', function(e) {
        if (!e.collection || e.collection.id !== {{ $collection->id }}) return;
        if (e.collection.status !== colStatus) {
            var badge = document.getElementById('statusBadge');
            if (badge) {
                badge.textContent = colStatusLabel[e.collection.status] || 'Inconnu';
                badge.className = 'cl-badge ' + (colBadgeMap[e.collection.status] || 'cl-badge-neutral');
            }
            colStatus = e.collection.status;
            showToast('Collecte #' + e.collection.id + ' : ' + (colStatusLabel[e.collection.status] || 'statut mis à jour'));
            setTimeout(function() { location.reload(); }, 2500);
        }
    });
}
</script>
@endpush
@endsection