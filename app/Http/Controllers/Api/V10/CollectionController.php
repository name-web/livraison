<?php

namespace App\Http\Controllers\Api\V10;

use App\Enums\CashTrackingStatus;
use App\Enums\CollectionStatus;
use App\Http\Controllers\Controller;
use App\Models\Backend\CashTracking;
use App\Models\Backend\Collection;
use App\Services\CollectionService;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    use ApiReturnFormatTrait;

    public function __construct(
        protected CollectionService $collectionService
    ) {}

    /**
     * POST /api/v10/collection/location-update
     * Mettre à jour la position GPS du livreur.
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $deliveryman = Auth::user()->deliveryman;

        $this->collectionService->updateLocation(
            $deliveryman,
            $request->lat,
            $request->lng
        );

        return $this->responseWithSuccess('Position mise à jour.', [], 200);
    }

    /**
     * GET /api/v10/collection/my-collections
     * Liste des collectes du livreur connecté.
     */
    public function myCollections(): JsonResponse
    {
        $deliveryman = Auth::user()->deliveryman;

        $collections = Collection::where('delivery_man_id', $deliveryman->id)
            ->with('parcels', 'merchant', 'shop')
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->responseWithSuccess('Mes collectes.', $collections, 200);
    }

    /**
     * GET /api/v10/collection/{id}
     * Détails d'une collecte.
     */
    public function show(Collection $collection): JsonResponse
    {
        $deliveryman = Auth::user()->deliveryman;

        if ($collection->delivery_man_id !== $deliveryman->id) {
            return $this->responseWithError('Non autorisé.', [], 403);
        }

        $collection->load('parcels', 'merchant', 'cashTrackings', 'shop');

        return $this->responseWithSuccess('Détails collecte.', $collection, 200);
    }

    /**
     * POST /api/v10/collection/{id}/accept
     * Accepter une collecte assignée → PICKING_UP.
     */
    public function accept(Collection $collection): JsonResponse
    {
        $deliveryman = Auth::user()->deliveryman;

        if ($collection->delivery_man_id !== $deliveryman->id) {
            return $this->responseWithError('Non autorisé.', [], 403);
        }

        if ($collection->status !== CollectionStatus::ASSIGNED) {
            return $this->responseWithError('Collecte non assignée.', [], 400);
        }

        $this->collectionService->updateStatus($collection, CollectionStatus::PICKING_UP);

        return $this->responseWithSuccess('Collecte acceptée.', $collection, 200);
    }

    /**
     * POST /api/v10/collection/{id}/pickup-complete
     * Tous les colis ramassés → COLLECTED.
     */
    public function pickupComplete(Collection $collection): JsonResponse
    {
        $deliveryman = Auth::user()->deliveryman;

        if ($collection->delivery_man_id !== $deliveryman->id) {
            return $this->responseWithError('Non autorisé.', [], 403);
        }

        if ($collection->status !== CollectionStatus::PICKING_UP) {
            return $this->responseWithError('Collecte non en cours de ramassage.', [], 400);
        }

        $this->collectionService->updateStatus($collection, CollectionStatus::COLLECTED);

        return $this->responseWithSuccess('Ramassage terminé.', $collection, 200);
    }

    /**
     * POST /api/v10/collection/{id}/complete
     * Mission terminée → COMPLETED. Livreur libéré.
     */
    public function complete(Collection $collection): JsonResponse
    {
        $deliveryman = Auth::user()->deliveryman;

        if ($collection->delivery_man_id !== $deliveryman->id) {
            return $this->responseWithError('Non autorisé.', [], 403);
        }

        if (! in_array($collection->status, [
            CollectionStatus::COLLECTED,
            CollectionStatus::PICKING_UP,
        ])) {
            return $this->responseWithError('Statut invalide pour compléter.', [], 400);
        }

        $this->collectionService->updateStatus($collection, CollectionStatus::COMPLETED);

        return $this->responseWithSuccess('Mission terminée. Livreur disponible.', $collection, 200);
    }

    /**
     * POST /api/v10/collection/cash/collect
     * Encaisser le cash pour un colis COD.
     * Idempotent : rejette si déjà encaissé.
     */
    public function collectCash(Request $request): JsonResponse
    {
        $request->validate([
            'cash_tracking_id' => 'required|exists:cash_trackings,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $tracking = CashTracking::findOrFail($request->cash_tracking_id);
        $deliveryman = Auth::user()->deliveryman;

        if ($tracking->delivery_man_id !== $deliveryman->id) {
            return $this->responseWithError('Non autorisé.', [], 403);
        }

        try {
            $tracking = $this->collectionService->collectCash($tracking, $request->amount);

            return $this->responseWithSuccess('Cash encaissé.', $tracking, 200);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 400);
        }
    }

    /**
     * POST /api/v10/collection/cash/handover
     * Remettre le cash à WeCourier.
     * Idempotent : rejette si déjà réconcilié ou montant invalide.
     */
    public function handOverCash(Request $request): JsonResponse
    {
        $request->validate([
            'cash_tracking_id' => 'required|exists:cash_trackings,id',
            'amount' => 'required|numeric|min:0',
            'handed_to' => 'required|string|max:255',
        ]);

        $tracking = CashTracking::findOrFail($request->cash_tracking_id);
        $deliveryman = Auth::user()->deliveryman;

        if ($tracking->delivery_man_id !== $deliveryman->id) {
            return $this->responseWithError('Non autorisé.', [], 403);
        }

        try {
            $tracking = $this->collectionService->handOverCash(
                $tracking,
                $request->amount,
                $request->handed_to
            );

            return $this->responseWithSuccess('Cash remis.', $tracking, 200);
        } catch (\Exception $e) {
            return $this->responseWithError($e->getMessage(), [], 400);
        }
    }

    /**
     * GET /api/v10/collection/cash/summary
     * Résumé de caisse du livreur.
     */
    public function cashSummary(): JsonResponse
    {
        $deliveryman = Auth::user()->deliveryman;

        $trackings = CashTracking::where('delivery_man_id', $deliveryman->id)->get();

        $summary = [
            'total_expected' => $trackings->sum('amount_expected'),
            'total_collected' => $trackings->sum('amount_collected'),
            'total_handed_over' => $trackings->sum('amount_handed_over'),
            'total_remaining' => $trackings->sum('amount_remaining'),
            'pending_count' => $trackings->where('status', CashTrackingStatus::PENDING)->count(),
            'anomaly_count' => $trackings->where('status', CashTrackingStatus::ANOMALY)->count(),
        ];

        return $this->responseWithSuccess('Résumé de caisse.', $summary, 200);
    }
}
