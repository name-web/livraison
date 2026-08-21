<?php

namespace App\Http\Controllers\Backend\MerchantPanel;

use App\Enums\CollectionStatus;
use App\Enums\ParcelStatus;
use App\Http\Controllers\Controller;
use App\Models\Backend\Collection;
use App\Models\Backend\Parcel;
use App\Models\MerchantShops;
use App\Services\CollectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    public function __construct(
        protected CollectionService $collectionService
    ) {}

    /**
     * Page principale des collectes du marchand.
     * Filtres : all, today, upcoming, completed, pending_assignment, active.
     */
    public function index(Request $request)
    {
        $merchant = Auth::user()->merchant;
        $filter = $request->get('filter', 'all');

        // ── KPI sidebar ──
        $stats = [
            'today' => Collection::where('merchant_id', $merchant->id)
                ->whereDate('collection_date', now()->toDateString())->count(),
            'active' => Collection::where('merchant_id', $merchant->id)
                ->whereIn('status', [
                    CollectionStatus::PENDING_ASSIGNMENT,
                    CollectionStatus::ASSIGNED,
                    CollectionStatus::PICKING_UP,
                    CollectionStatus::COLLECTED,
                ])->count(),
            'completed' => Collection::where('merchant_id', $merchant->id)
                ->where('status', CollectionStatus::COMPLETED)
                ->whereDate('collection_date', now()->toDateString())->count(),
            'pending_assignment' => Collection::where('merchant_id', $merchant->id)
                ->where('status', CollectionStatus::PENDING_ASSIGNMENT)->count(),
            'pending_parcels' => Parcel::where('merchant_id', $merchant->id)
                ->where('status', ParcelStatus::PENDING)->count(),
            'total_cash_today' => Collection::where('merchant_id', $merchant->id)
                ->whereDate('collection_date', now()->toDateString())
                ->sum('total_cash_collection'),
        ];

        // ── Query collections ──
        $query = Collection::where('merchant_id', $merchant->id)
            ->with('parcels', 'deliveryMan.user', 'shop');

        $query = match ($filter) {
            'today' => $query->whereDate('collection_date', now()->toDateString()),
            'upcoming' => $query->where('collection_date', '>', now()->toDateString())
                ->whereIn('status', [CollectionStatus::PENDING_ASSIGNMENT, CollectionStatus::ASSIGNED]),
            'completed' => $query->where('status', CollectionStatus::COMPLETED),
            'cancelled' => $query->where('status', CollectionStatus::CANCELLED),
            'pending_assignment' => $query->where('status', CollectionStatus::PENDING_ASSIGNMENT),
            'active' => $query->whereIn('status', [
                CollectionStatus::PENDING_ASSIGNMENT,
                CollectionStatus::ASSIGNED,
                CollectionStatus::PICKING_UP,
                CollectionStatus::COLLECTED,
            ]),
            default => $query,
        };

        $collections = $query->orderByDesc('collection_date')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // ── Parcels en attente (pour le formulaire de création) ──
        $pendingParcels = Parcel::where('merchant_id', $merchant->id)
            ->where('status', ParcelStatus::PENDING)
            ->get();

        $shops = MerchantShops::where('merchant_id', $merchant->id)
            ->where('status', 1)
            ->get();

        // ── JSON prêt pour le formulaire JS ──
        $parcelOptions = $pendingParcels->map(function ($p) {
            $created = $p->created_at->startOfDay();
            $today = now()->startOfDay();
            $diff = $created->diffInDays($today);
            $label = match (true) {
                $diff === 0 => 'Aujourd\'hui',
                $diff === 1 => 'Hier',
                $diff === 2 => 'Il y a 2 jours',
                $diff <= 6 => 'Il y a '.$diff.' jours',
                default => $created->format('d/m/Y'),
            };

            return [
                'id' => $p->id,
                'tracking' => $p->tracking_id,
                'name' => $p->customer_name,
                'address' => $p->customer_address,
                'cash' => $p->cash_collection,
                'date' => $created->toDateString(),
                'date_label' => $label,
                'diff' => (int) $diff,
            ];
        })->values()->all();

        // ── Collecte active pour le suivi GPS ──
        $gpsCollection = Collection::where('merchant_id', $merchant->id)
            ->whereIn('status', [CollectionStatus::ASSIGNED, CollectionStatus::PICKING_UP])
            ->with('deliveryMan.user')
            ->first();

        return view('backend.merchant_panel.collection.index', compact(
            'stats',
            'collections',
            'pendingParcels',
            'parcelOptions',
            'shops',
            'filter',
            'gpsCollection',
        ));
    }

    /**
     * API JSON : colis disponibles pour le formulaire.
     * GET /merchant/collection/available-parcels?shop_id=X
     */
    public function availableParcels(Request $request): JsonResponse
    {
        $merchant = Auth::user()->merchant;

        $query = Parcel::where('merchant_id', $merchant->id)
            ->where('status', ParcelStatus::PENDING);

        if ($request->filled('shop_id')) {
            $query->where('merchant_shop_id', $request->shop_id);
        }

        // Recherche par tracking, nom ou adresse
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_id', 'LIKE', '%'.$search.'%')
                    ->orWhere('customer_name', 'LIKE', '%'.$search.'%')
                    ->orWhere('customer_address', 'LIKE', '%'.$search.'%');
            });
        }

        $parcels = $query->get([
            'id', 'tracking_id', 'customer_name', 'customer_phone',
            'customer_address', 'cash_collection', 'merchant_shop_id',
            'total_delivery_amount', 'created_at',
        ]);

        return response()->json([
            'success' => true,
            'parcels' => $parcels,
        ]);
    }

    /**
     * API JSON : créneaux horaires disponibles pour une date.
     */
    public function availableSlots(Request $request): JsonResponse
    {
        $date = $request->get('date', now()->toDateString());

        $slots = [
            '08:00-10:00' => '08:00 - 10:00',
            '10:00-12:00' => '10:00 - 12:00',
            '12:00-14:00' => '12:00 - 14:00',
            '14:00-16:00' => '14:00 - 16:00',
            '16:00-18:00' => '16:00 - 18:00',
        ];

        // Si date = aujourd'hui, retirer les créneaux passés
        if ($date === now()->toDateString()) {
            $currentHour = (int) now()->format('H');
            $slots = array_filter($slots, function ($key) use ($currentHour) {
                $startHour = (int) explode('-', $key)[0];

                return $startHour > $currentHour;
            }, ARRAY_FILTER_USE_KEY);
        }

        return response()->json([
            'success' => true,
            'slots' => $slots,
        ]);
    }

    /**
     * Créer une collecte planifiée.
     */
    public function store(Request $request)
    {
        $merchant = Auth::user()->merchant;

        $request->validate([
            'parcel_ids' => 'required|array|min:1',
            'parcel_ids.*' => 'exists:parcels,id',
            'shop_id' => 'nullable|exists:merchant_shops,id',
            'collection_date' => 'nullable|date|after_or_equal:today',
            'time_slot' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $collection = $this->collectionService->createCollection(
                $merchant,
                $request->parcel_ids,
                $request->shop_id,
                $request->collection_date,
                $request->time_slot,
                $request->note,
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Collecte #'.$collection->id.' créée avec succès.',
                    'collection' => $collection->load('parcels', 'deliveryMan.user'),
                ]);
            }

            return back()->with('success', 'Collecte #'.$collection->id.' créée avec succès.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Détails d'une collecte (AJAX).
     */
    public function show(Collection $collection): JsonResponse
    {
        $merchant = Auth::user()->merchant;

        if ($collection->merchant_id !== $merchant->id) {
            abort(403);
        }

        $collection->load('parcels', 'deliveryMan.user', 'cashTrackings', 'shop');

        return response()->json($collection);
    }

    /**
     * Page détail complète d'une collecte (render HTML).
     */
    public function detail(Collection $collection)
    {
        $merchant = Auth::user()->merchant;

        if ($collection->merchant_id !== $merchant->id) {
            abort(403);
        }

        $collection->load('parcels', 'deliveryMan.user', 'shop', 'cashTrackings');

        return view('backend.merchant_panel.collection.show', compact('collection'));
    }

    /**
     * Suivi temps réel d'une collecte (AJAX polling fallback).
     */
    public function tracking(Collection $collection): JsonResponse
    {
        $merchant = Auth::user()->merchant;

        if ($collection->merchant_id !== $merchant->id) {
            abort(403);
        }

        $collection->load('deliveryMan.user');

        return response()->json([
            'collection' => [
                'id' => $collection->id,
                'status' => $collection->status,
                'status_label' => $collection->status_label,
                'status_color' => $collection->status_color,
                'parcel_count' => $collection->parcel_count,
                'collected_count' => $collection->parcels->count(),
                'delivery_man' => $collection->deliveryMan ? [
                    'name' => $collection->deliveryMan->user->name,
                    'lat' => $collection->deliveryMan->current_location_lat,
                    'lng' => $collection->deliveryMan->current_location_long,
                ] : null,
            ],
        ]);
    }

    /**
     * Annuler une collecte.
     */
    public function cancel(Collection $collection, Request $request)
    {
        $merchant = Auth::user()->merchant;

        if ($collection->merchant_id !== $merchant->id) {
            abort(403);
        }

        if (! in_array($collection->status, [
            CollectionStatus::PENDING_ASSIGNMENT,
            CollectionStatus::ASSIGNED,
        ])) {
            $msg = 'Impossible d\'annuler cette collecte.';

            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 400)
                : back()->with('error', $msg);
        }

        $this->collectionService->updateStatus(
            $collection,
            CollectionStatus::CANCELLED,
            $request->input('cancel_reason')
        );

        $msg = 'Collecte #'.$collection->id.' annulée.';

        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('success', $msg);
    }

    /**
     * Ajouter un colis à une collecte existante.
     * POST /merchant/collection/{collection}/add-parcel
     */
    public function addParcel(Collection $collection, Request $request)
    {
        $merchant = Auth::user()->merchant;

        if ($collection->merchant_id !== $merchant->id) {
            abort(403);
        }

        $request->validate([
            'parcel_id' => 'required|exists:parcels,id',
        ]);

        $parcel = Parcel::findOrFail($request->parcel_id);

        try {
            $this->collectionService->addParcelToCollection($collection, $parcel);

            return response()->json([
                'success' => true,
                'message' => 'Colis #'.$parcel->tracking_id.' ajouté à la collecte #'.$collection->id.'.',
                'collection' => $collection->fresh(['parcels', 'deliveryMan.user', 'shop']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * API : Trouver une collecte compatible pour un colis.
     * GET /merchant/collection/find-compatible?parcel_id=X
     */
    public function findCompatible(Request $request)
    {
        $merchant = Auth::user()->merchant;

        $request->validate([
            'parcel_id' => 'required|exists:parcels,id',
        ]);

        $parcel = Parcel::findOrFail($request->parcel_id);

        if ($parcel->merchant_id !== $merchant->id) {
            return response()->json(['success' => false, 'message' => 'Colis inconnu.'], 403);
        }

        $collection = $this->collectionService->findCompatibleCollection($merchant, $parcel);

        if (! $collection) {
            return response()->json([
                'success' => true,
                'compatible' => false,
                'message' => 'Aucune collecte active disponible pour ce colis.',
            ]);
        }

        return response()->json([
            'success' => true,
            'compatible' => true,
            'collection' => [
                'id' => $collection->id,
                'parcel_count' => $collection->parcel_count,
                'status' => $collection->status_label,
                'delivery_man' => $collection->deliveryMan?->user?->name,
                'shop' => $collection->shop?->name,
                'date' => $collection->collection_date,
                'time_slot' => $collection->time_slot,
            ],
        ]);
    }
}
