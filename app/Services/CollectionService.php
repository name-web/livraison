<?php

namespace App\Services;

use App\Enums\CashTrackingStatus;
use App\Enums\CollectionStatus;
use App\Enums\ParcelStatus;
use App\Events\CollectionStatusChanged;
use App\Events\DeliverymanLocationUpdated;
use App\Models\Backend\CashTracking;
use App\Models\Backend\Collection;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Merchant;
use App\Models\Backend\Parcel;
use App\Models\Backend\ParcelEvent;
use App\Models\MerchantShops;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CollectionService
{
    /**
     * Créer une collecte planifiée.
     * Le marchand choisit la date, le créneau, la boutique et les colis.
     * Dispatch uniquement si scheduled_at est déjà passé.
     */
    public function createCollection(
        Merchant $merchant,
        array $parcelIds,
        ?int $shopId = null,
        ?string $collectionDate = null,
        ?string $timeSlot = null,
        ?string $note = null,
    ): Collection {
        return DB::transaction(function () use ($merchant, $parcelIds, $shopId, $collectionDate, $timeSlot, $note) {

            // 1. Vérifier que les colis existent et sont PENDING
            $parcels = Parcel::whereIn('id', $parcelIds)
                ->where('merchant_id', $merchant->id)
                ->where('status', ParcelStatus::PENDING)
                ->get();

            if ($parcels->count() !== count($parcelIds)) {
                throw new \Exception('Certains colis ne sont pas disponibles ou ne vous appartiennent pas.');
            }

            // 2. Vérifier qu'aucun de ces colis n'est déjà dans une collecte active
            $parcelsInActiveCollection = Parcel::whereIn('id', $parcelIds)
                ->whereHas('collections', function ($q) {
                    $q->whereNotIn('collections.status', [
                        CollectionStatus::COMPLETED,
                        CollectionStatus::CANCELLED,
                    ]);
                })
                ->pluck('id');

            if ($parcelsInActiveCollection->isNotEmpty()) {
                throw new \Exception('Le colis #'.$parcelsInActiveCollection->first().' est déjà dans une collecte active.');
            }

            // 3. Récupérer la boutique
            $shop = $shopId ? MerchantShops::find($shopId) : $merchant->getActiveShopAttribute();

            // 4. Calculer la date/heure planifiée
            $scheduledDate = $collectionDate ?? now()->toDateString();
            $scheduledAt = $collectionDate
                ? Carbon::parse($collectionDate.' '.($timeSlot ? explode('-', $timeSlot)[0] : '09:00'))
                : now();

            // 5. Créer la collecte
            $collection = Collection::create([
                'merchant_id' => $merchant->id,
                'shop_id' => $shop?->id,
                'status' => CollectionStatus::PENDING_ASSIGNMENT,
                'pickup_address' => $shop?->address,
                'pickup_lat' => $shop?->merchant_lat,
                'pickup_long' => $shop?->merchant_long,
                'collection_date' => $scheduledDate,
                'time_slot' => $timeSlot,
                'scheduled_at' => $scheduledAt,
                'parcel_count' => $parcels->count(),
                'total_cash_collection' => $parcels->sum('cash_collection'),
                'total_delivery_amount' => $parcels->sum('total_delivery_amount'),
                'note' => $note,
            ]);

            // 6. Attacher les colis + mettre à jour leur statut
            foreach ($parcels as $parcel) {
                $collection->parcels()->attach($parcel->id, ['status' => 1]);
                $parcel->update(['status' => ParcelStatus::PICKUP_ASSIGN]);
            }

            // 7. Dispatch UNIQUEMENT si scheduled_at est déjà passé
            if ($scheduledAt->isPast() || $scheduledAt->lte(now())) {
                $this->dispatchDeliveryman($collection);
            }

            return $collection;
        });
    }

    /**
     * Affecter automatiquement un livreur disponible.
     * Priorité au livreur de la même zone (hub), puis fallback.
     * Utilise lockForUpdate() pour éviter les race conditions.
     */
    public function dispatchDeliveryman(Collection $collection): ?DeliveryMan
    {
        $deliveryman = null;

        DB::transaction(function () use ($collection, &$deliveryman) {

            // Déterminer le hub cible (de la boutique de la collecte)
            $targetHubId = $collection->shop
                ? ($collection->shop->hub_id ?? null)
                : null;

            // 1. Chercher un livreur disponible dans la même zone (lockForUpdate)
            if ($targetHubId) {
                $deliveryman = DeliveryMan::where('is_available', true)
                    ->where('status', 1) // ACTIVE
                    ->where('current_hub_id', $targetHubId)
                    ->lockForUpdate()
                    ->first();
            }

            // 2. Fallback : n'importe quel livreur disponible
            if (! $deliveryman) {
                $deliveryman = DeliveryMan::where('is_available', true)
                    ->where('status', 1) // ACTIVE
                    ->lockForUpdate()
                    ->first();
            }

            if (! $deliveryman) {
                return; // La collecte reste PENDING_ASSIGNMENT
            }

            // 3. Affecter (même transaction, pas de transaction imbriquée)
            $deliveryman->update(['is_available' => false]);

            $collection->update([
                'delivery_man_id' => $deliveryman->id,
                'status' => CollectionStatus::ASSIGNED,
                'assigned_at' => now(),
            ]);

            foreach ($collection->parcels as $parcel) {
                ParcelEvent::create([
                    'parcel_id' => $parcel->id,
                    'pickup_man_id' => $deliveryman->id,
                    'parcel_status' => ParcelStatus::PICKUP_ASSIGN,
                    'note' => 'Collecte #'.$collection->id.' assignée au livreur '.$deliveryman->user->name,
                    'created_by' => $deliveryman->user_id,
                ]);
            }
        });

        // Broadcast APRÈS commit
        if ($deliveryman) {
            broadcast(new CollectionStatusChanged($collection->fresh()));
        }

        return $deliveryman;
    }

    /**
     * Affecter un livreur spécifique à une collecte (affectation manuelle admin).
     * Utilise lockForUpdate pour protéger la réservation.
     */
    public function assignDeliveryman(Collection $collection, DeliveryMan $deliveryman): DeliveryMan
    {
        DB::transaction(function () use ($collection, $deliveryman) {

            // Vérification atomique avec lock
            $dm = DeliveryMan::where('id', $deliveryman->id)
                ->lockForUpdate()
                ->first();

            if (! $dm || ! $dm->is_available) {
                throw new \Exception('Ce livreur n\'est plus disponible.');
            }

            // Marquer le livreur comme occupé
            $dm->update(['is_available' => false]);

            // Mettre à jour la collecte
            $collection->update([
                'delivery_man_id' => $dm->id,
                'status' => CollectionStatus::ASSIGNED,
                'assigned_at' => now(),
            ]);

            // Créer les ParcelEvent
            foreach ($collection->parcels as $parcel) {
                ParcelEvent::create([
                    'parcel_id' => $parcel->id,
                    'pickup_man_id' => $dm->id,
                    'parcel_status' => ParcelStatus::PICKUP_ASSIGN,
                    'note' => 'Collecte #'.$collection->id.' assignée au livreur '.$dm->user->name,
                    'created_by' => $dm->user_id,
                ]);
            }
        });

        // Broadcast APRÈS commit
        broadcast(new CollectionStatusChanged($collection->fresh()));

        return $deliveryman;
    }

    /**
     * Mettre à jour le statut d'une collecte.
     * Cycle : PENDING → ASSIGNED → PICKING_UP → COLLECTED → COMPLETED
     *
     * COLLECTED ne libère PAS le livreur.
     * Le livreur ne redevient AVAILABLE qu'à COMPLETED ou CANCELLED.
     */
    public function updateStatus(Collection $collection, int $newStatus): Collection
    {
        DB::transaction(function () use ($collection, $newStatus) {

            $updateData = ['status' => $newStatus];

            match ($newStatus) {
                CollectionStatus::PICKING_UP => $updateData['picked_up_at'] = now(),
                CollectionStatus::COLLECTED => $updateData['collected_at'] = now(),
                default => null,
            };

            $collection->update($updateData);

            // COLLECTED : créer les CashTracking MAIS ne PAS libérer le livreur
            if ($newStatus === CollectionStatus::COLLECTED) {
                $this->createCashTrackings($collection);
            }

            // COMPLETED ou CANCELLED : libérer le livreur
            if (in_array($newStatus, [CollectionStatus::COMPLETED, CollectionStatus::CANCELLED])) {
                $this->releaseDeliveryman($collection);
            }
        });

        // Broadcast APRÈS commit
        broadcast(new CollectionStatusChanged($collection->fresh()));

        return $collection;
    }

    /**
     * Libérer un livreur après COMPLETED/CANCELLED.
     * 1. Vérifie qu'il n'a pas d'autres collectes actives.
     * 2. Libère is_available.
     * 3. Recherche une collecte PENDING_ASSIGNMENT dont scheduled_at est déjà passé.
     * 4. Priorise la même zone (hub).
     * 5. Attribue automatiquement.
     */
    public function releaseDeliveryman(Collection $collection): void
    {
        if (! $collection->delivery_man_id) {
            return;
        }

        $deliverymanId = $collection->delivery_man_id;

        // Vérifier si le livreur a d'autres collectes actives
        $hasOtherActive = Collection::where('delivery_man_id', $deliverymanId)
            ->where('id', '!=', $collection->id)
            ->whereNotIn('collections.status', [
                CollectionStatus::COMPLETED,
                CollectionStatus::CANCELLED,
            ])
            ->exists();

        if ($hasOtherActive) {
            return; // Le livreur reste occupé
        }

        // Libérer le livreur
        DeliveryMan::where('id', $deliverymanId)->update([
            'is_available' => true,
        ]);

        $deliveryman = DeliveryMan::find($deliverymanId);

        if (! $deliveryman) {
            return;
        }

        // Chercher une collecte PENDING_ASSIGNMENT dont le scheduled_at est déjà passé
        $pendingCollection = Collection::where('status', CollectionStatus::PENDING_ASSIGNMENT)
            ->whereNull('delivery_man_id')
            ->where('scheduled_at', '<=', now())
            ->orderBy('scheduled_at')
            ->first();

        if (! $pendingCollection) {
            return;
        }

        // Utiliser dispatchDeliveryman pour la priorisation zone + lockForUpdate
        $this->dispatchDeliveryman($pendingCollection);
    }

    /**
     * Créer les entrées de suivi de caisse pour chaque colis COD.
     */
    public function createCashTrackings(Collection $collection): void
    {
        foreach ($collection->parcels as $parcel) {
            if ($parcel->cash_collection > 0) {
                // Idempotence : ne pas créer si déjà existant
                $exists = CashTracking::where('parcel_id', $parcel->id)
                    ->where('collection_id', $collection->id)
                    ->exists();

                if (! $exists) {
                    CashTracking::create([
                        'collection_id' => $collection->id,
                        'parcel_id' => $parcel->id,
                        'delivery_man_id' => $collection->delivery_man_id,
                        'merchant_id' => $collection->merchant_id,
                        'amount_expected' => $parcel->cash_collection,
                        'amount_collected' => 0,
                        'amount_remaining' => $parcel->cash_collection,
                        'status' => CashTrackingStatus::PENDING,
                    ]);
                }
            }
        }
    }

    /**
     * Encaisser le cash pour un colis COD.
     * Idempotent : vérifie le statut avant modification.
     */
    public function collectCash(CashTracking $tracking, float $amount): CashTracking
    {
        if ($tracking->status !== CashTrackingStatus::PENDING) {
            throw new \Exception('Ce colis a déjà été encaissé.');
        }

        $tracking->update([
            'amount_collected' => $amount,
            'amount_remaining' => max(0, $tracking->amount_expected - $amount),
            'status' => $amount >= $tracking->amount_expected
                ? CashTrackingStatus::COLLECTED
                : CashTrackingStatus::ANOMALY,
            'collected_at' => now(),
            'anomaly_note' => $amount < $tracking->amount_expected
                ? 'Écart de '.number_format($tracking->amount_expected - $amount, 2).' FCFA'
                : null,
        ]);

        return $tracking;
    }

    /**
     * Remettre le cash à WeCourier.
     * Idempotent : vérifie que le montant ne dépasse pas l'encaissé.
     */
    public function handOverCash(CashTracking $tracking, float $amount, string $handedTo): CashTracking
    {
        if ($tracking->status === CashTrackingStatus::RECONCILED) {
            throw new \Exception('Déjà réconcilié.');
        }

        if ($amount > $tracking->amount_collected) {
            throw new \Exception('Le montant remis ne peut pas dépasser le montant encaissé.');
        }

        $tracking->update([
            'amount_handed_over' => $amount,
            'amount_remaining' => $tracking->amount_collected - $amount,
            'status' => CashTrackingStatus::HANDED_OVER,
            'handed_over_to' => $handedTo,
            'handed_over_at' => now(),
        ]);

        return $tracking;
    }

    /**
     * Mettre à jour la position GPS d'un livreur.
     */
    public function updateLocation(DeliveryMan $deliveryman, float $lat, float $long): void
    {
        $deliveryman->update([
            'current_location_lat' => $lat,
            'current_location_long' => $long,
        ]);

        // Broadcast vers admin.map + deliveryman.location.{id} + marchands concernés
        broadcast(new DeliverymanLocationUpdated($deliveryman));
    }

    /**
     * Statistiques pour un marchand.
     */
    public function getMerchantStats(Merchant $merchant): array
    {
        return [
            'today_collections' => Collection::where('merchant_id', $merchant->id)
                ->forDate(now()->toDateString())->count(),
            'today_parcels' => Collection::where('merchant_id', $merchant->id)
                ->forDate(now()->toDateString())->sum('parcel_count'),
            'pending_parcels' => Parcel::where('merchant_id', $merchant->id)
                ->where('status', ParcelStatus::PENDING)->count(),
            'active_collections' => Collection::where('merchant_id', $merchant->id)
                ->active()->count(),
            'total_cash_today' => Collection::where('merchant_id', $merchant->id)
                ->forDate(now()->toDateString())->sum('total_cash_collection'),
        ];
    }
}
