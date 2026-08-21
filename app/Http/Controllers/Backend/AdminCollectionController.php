<?php

namespace App\Http\Controllers\Backend;

use App\Enums\CollectionStatus;
use App\Http\Controllers\Controller;
use App\Models\Backend\Collection;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Merchant;
use App\Services\CollectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCollectionController extends Controller
{
    public function __construct(
        protected CollectionService $collectionService
    ) {}

    /**
     * Liste de toutes les collectes.
     */
    public function index(Request $request)
    {
        $query = Collection::with('merchant.user', 'deliveryMan.user', 'parcels', 'shop');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('collection_date', $request->date);
        }

        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        $collections = $query->orderByDesc('created_at')->paginate(15);

        $stats = [
            'total' => Collection::count(),
            'pending' => Collection::where('status', CollectionStatus::PENDING_ASSIGNMENT)->count(),
            'active' => Collection::active()->count(),
            'completed' => Collection::where('status', CollectionStatus::COMPLETED)->count(),
            'today' => Collection::whereDate('collection_date', now()->toDateString())->count(),
            'available' => DeliveryMan::available()->count(),
        ];

        $merchants = Merchant::with('user')->get();
        $deliverymen = DeliveryMan::with('user')->get();

        return view('backend.admin.collection.index', compact(
            'collections',
            'stats',
            'merchants',
            'deliverymen',
        ));
    }

    /**
     * Détails d'une collecte.
     */
    public function show(Collection $collection)
    {
        $collection->load('merchant.user', 'deliveryMan.user', 'parcels', 'cashTrackings', 'shop');

        return view('backend.admin.collection.show', compact('collection'));
    }

    /**
     * Affecter manuellement un livreur à une collecte.
     * Utilise lockForUpdate pour éviter les race conditions.
     */
    public function assign(Request $request, Collection $collection)
    {
        $request->validate([
            'delivery_man_id' => 'required|exists:delivery_man,id',
        ]);

        $deliveryman = DeliveryMan::findOrFail($request->delivery_man_id);

        if (! $deliveryman->is_available) {
            $msg = 'Ce livreur n\'est pas disponible.';

            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 400)
                : back()->with('error', $msg);
        }

        try {
            $this->collectionService->assignDeliveryman($collection, $deliveryman);

            $msg = 'Livreur affecté avec succès.';

            return $request->ajax()
                ? response()->json(['success' => true, 'message' => $msg])
                : back()->with('success', $msg);
        } catch (\Exception $e) {
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $e->getMessage()], 400)
                : back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mettre à jour le statut d'une collecte (admin).
     */
    public function updateStatus(Request $request, Collection $collection)
    {
        $request->validate([
            'status' => 'required|integer|in:'.implode(',', [
                CollectionStatus::PICKING_UP,
                CollectionStatus::COLLECTED,
                CollectionStatus::COMPLETED,
                CollectionStatus::CANCELLED,
            ]),
        ]);

        $this->collectionService->updateStatus($collection, $request->status);

        $msg = 'Statut mis à jour.';

        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('success', $msg);
    }

    /**
     * Vue carte temps réel de tous les livreurs.
     */
    public function map()
    {
        $deliverymen = DeliveryMan::with('user')
            ->whereNotNull('current_location_lat')
            ->get()
            ->map(fn ($dm) => [
                'id' => $dm->id,
                'name' => $dm->user->name,
                'lat' => $dm->current_location_lat,
                'lng' => $dm->current_location_long,
                'is_available' => $dm->is_available,
            ]);

        $activeCollections = Collection::active()
            ->with('merchant.user', 'deliveryMan.user', 'parcels')
            ->get();

        return view('backend.admin.collection.map', compact('deliverymen', 'activeCollections'));
    }

    /**
     * API: Suivi GPS d'un livreur spécifique.
     */
    public function deliverymanLocation(DeliveryMan $deliveryman): JsonResponse
    {
        return response()->json([
            'lat' => $deliveryman->current_location_lat,
            'lng' => $deliveryman->current_location_long,
        ]);
    }

    /**
     * Statistiques temps réel (AJAX).
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'pending' => Collection::where('status', CollectionStatus::PENDING_ASSIGNMENT)->count(),
            'active' => Collection::active()->count(),
            'today' => Collection::whereDate('collection_date', now()->toDateString())->count(),
            'available' => DeliveryMan::available()->count(),
        ]);
    }
}
