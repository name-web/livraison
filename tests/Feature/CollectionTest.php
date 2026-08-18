<?php

namespace Tests\Feature;

use App\Enums\CashTrackingStatus;
use App\Enums\CollectionStatus;
use App\Enums\ParcelStatus;
use App\Enums\Status;
use App\Enums\UserType;
use App\Models\Backend\Collection;
use App\Models\Backend\CashTracking;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Hub;
use App\Models\Backend\Merchant;
use App\Models\Backend\Parcel;
use App\Models\MerchantShops;
use App\Models\User;
use App\Services\CollectionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    use DatabaseTransactions;

    private CollectionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CollectionService::class);

        // Nettoyer les livreurs existants pour isoler les tests
        DeliveryMan::query()->delete();
    }

    // ─── Helpers ───────────────────────────────────

    private function createMerchant(string $name = 'Marchand Test'): array
    {
        $user = User::create([
            'name' => $name,
            'user_type' => UserType::MERCHANT,
        ]);
        $merchant = Merchant::create([
            'business_name' => $name,
            'merchant_unique_id' => strtoupper(substr(md5($name), 0, 8)),
            'current_balance' => 0,
            'opening_balance' => 0,
            'vat' => 0,
            'user_id' => $user->id,
        ]);

        return [$user, $merchant];
    }

    private function createDeliveryMan(string $name = 'Livreur Test', bool $available = true, ?int $hubId = null): DeliveryMan
    {
        $user = User::create([
            'name' => $name,
            'user_type' => UserType::DELIVERYMAN,
        ]);

        return DeliveryMan::create([
            'user_id' => $user->id,
            'status' => Status::ACTIVE,
            'is_available' => $available,
            'current_hub_id' => $hubId,
            'delivery_charge' => 500,
            'pickup_charge' => 300,
            'current_balance' => 0,
            'opening_balance' => 0,
        ]);
    }

    private function createParcel(int $merchantId, float $cash = 5000, int $status = ParcelStatus::PENDING): Parcel
    {
        return Parcel::create([
            'merchant_id' => $merchantId,
            'customer_name' => 'Client Test',
            'customer_phone' => '0701020304',
            'cash_collection' => $cash,
            'selling_price' => $cash * 0.8,
            'delivery_charge' => 500,
            'total_delivery_amount' => 500,
            'current_payable' => $cash - 500,
            'status' => $status,
            'parcel_payment_method' => 1,
            'tracking_id' => 'TRK-'.strtoupper(uniqid()),
        ]);
    }

    private function createShop(int $merchantId): MerchantShops
    {
        return MerchantShops::create([
            'merchant_id' => $merchantId,
            'name' => 'Boutique Test',
            'address' => 'Abidjan, Treichville',
            'status' => Status::ACTIVE,
            'default_shop' => Status::ACTIVE,
        ]);
    }

    private function createHub(string $name = 'Hub Abidjan'): Hub
    {
        return Hub::create([
            'name' => $name,
            'phone' => '0701020304',
            'address' => 'Abidjan',
        ]);
    }

    // ─── TEST 1 : Collecte prévue demain → aucun dispatch ──

    public function test_collecte_prevue_demain_aucun_dispatch(): void
    {
        [$user, $merchant] = $this->createMerchant();
        $dm = $this->createDeliveryMan('Livreur 1');
        $parcel = $this->createParcel($merchant->id);

        $collection = $this->service->createCollection(
            $merchant,
            [$parcel->id],
            null,
            now()->addDay()->toDateString(), // demain
            '10:00-12:00',
        );

        $this->assertEquals(CollectionStatus::PENDING_ASSIGNMENT, $collection->status, 'Collecte demain ne doit PAS être dispatchée');
        $this->assertNull($collection->delivery_man_id, 'Aucun livreur ne doit être assigné');

        $dm->refresh();
        $this->assertTrue($dm->is_available, 'Le livreur ne doit pas être affecté');
    }

    // ─── TEST 2 : Collecte aujourd'hui créneau futur → aucun dispatch ──

    public function test_collecte_aujourd_hui_creneau_futur_aucun_dispatch(): void
    {
        [$user, $merchant] = $this->createMerchant();
        $dm = $this->createDeliveryMan('Livreur 2');
        $parcel = $this->createParcel($merchant->id);

        // Créneau dans 2 heures (garanti dans le futur aujourd'hui)
        $futureStart = now()->addHours(2)->format('H:00');
        $futureEnd = now()->addHours(4)->format('H:00');
        $futureSlot = $futureStart.'-'.$futureEnd;

        $collection = $this->service->createCollection(
            $merchant,
            [$parcel->id],
            null,
            now()->toDateString(),
            $futureSlot,
        );

        $this->assertEquals(CollectionStatus::PENDING_ASSIGNMENT, $collection->status, 'Collecte avec créneau futur ne doit PAS être dispatchée');
        $this->assertNull($collection->delivery_man_id);

        $dm->refresh();
        $this->assertTrue($dm->is_available);
    }

    // ─── TEST 3 : Collecte dont scheduled_at est arrivé → dispatch ──

    public function test_collecte_scheduled_at_passe_dispatch(): void
    {
        [$user, $merchant] = $this->createMerchant();
        $hub = $this->createHub();
        $dm = $this->createDeliveryMan('Livreur 3', true, $hub->id);
        $shop = $this->createShop($merchant->id);
        $parcel = $this->createParcel($merchant->id);

        // scheduled_at = il y a 1 heure
        $collection = Collection::create([
            'merchant_id' => $merchant->id,
            'shop_id' => $shop->id,
            'status' => CollectionStatus::PENDING_ASSIGNMENT,
            'pickup_address' => $shop->address,
            'collection_date' => now()->toDateString(),
            'scheduled_at' => now()->subHour(),
            'parcel_count' => 1,
            'total_cash_collection' => 5000,
            'total_delivery_amount' => 500,
        ]);

        $collection->parcels()->attach($parcel->id, ['status' => 1]);
        $parcel->update(['status' => ParcelStatus::PICKUP_ASSIGN]);

        $result = $this->service->dispatchDeliveryman($collection);

        $this->assertNotNull($result, 'Un livreur doit être assigné');

        $collection->refresh();
        $this->assertEquals(CollectionStatus::ASSIGNED, $collection->status);

        $dm->refresh();
        $this->assertFalse($dm->is_available, 'Le livreur doit être occupé');
    }

    // ─── TEST 4 : COLLECTED → livreur toujours indisponible ──

    public function test_collected_livreur_indisponible(): void
    {
        [$user, $merchant] = $this->createMerchant();
        $dm = $this->createDeliveryMan('Livreur 4');
        $parcel = $this->createParcel($merchant->id);

        // Créer avec scheduled_at passé pour déclencher le dispatch
        $collection = Collection::create([
            'merchant_id' => $merchant->id,
            'status' => CollectionStatus::PENDING_ASSIGNMENT,
            'collection_date' => now()->toDateString(),
            'scheduled_at' => now()->subHour(),
            'parcel_count' => 1,
            'total_cash_collection' => 5000,
            'total_delivery_amount' => 500,
        ]);
        $collection->parcels()->attach($parcel->id, ['status' => 1]);
        $parcel->update(['status' => ParcelStatus::PICKUP_ASSIGN]);

        $this->service->dispatchDeliveryman($collection);

        $dm->refresh();
        $this->assertFalse($dm->is_available, 'Livreur doit être occupé après assignment');

        // Passer à PICKING_UP
        $this->service->updateStatus($collection->fresh(), CollectionStatus::PICKING_UP);
        $dm->refresh();
        $this->assertFalse($dm->is_available, 'Livreur doit rester occupé en PICKING_UP');

        // Passer à COLLECTED
        $this->service->updateStatus($collection->fresh(), CollectionStatus::COLLECTED);
        $dm->refresh();
        $this->assertFalse($dm->is_available, 'Livreur doit rester occupé en COLLECTED');
    }

    // ─── TEST 5 : COMPLETED → livreur AVAILABLE ──

    public function test_completed_livreur_disponible(): void
    {
        [$user, $merchant] = $this->createMerchant();
        $dm = $this->createDeliveryMan('Livreur 5');
        $parcel = $this->createParcel($merchant->id);

        $collection = Collection::create([
            'merchant_id' => $merchant->id,
            'status' => CollectionStatus::PENDING_ASSIGNMENT,
            'collection_date' => now()->toDateString(),
            'scheduled_at' => now()->subHour(),
            'parcel_count' => 1,
            'total_cash_collection' => 5000,
            'total_delivery_amount' => 500,
        ]);
        $collection->parcels()->attach($parcel->id, ['status' => 1]);
        $parcel->update(['status' => ParcelStatus::PICKUP_ASSIGN]);

        $this->service->dispatchDeliveryman($collection);

        // Collecte complète : ASSIGNED → PICKING_UP → COLLECTED → COMPLETED
        $this->service->updateStatus($collection->fresh(), CollectionStatus::PICKING_UP);
        $this->service->updateStatus($collection->fresh(), CollectionStatus::COLLECTED);
        $this->service->updateStatus($collection->fresh(), CollectionStatus::COMPLETED);

        $dm->refresh();
        $this->assertTrue($dm->is_available, 'Livreur doit être AVAILABLE après COMPLETED');
    }

    // ─── TEST 6 : Après COMPLETED → collecte PENDING échue attribuée ──

    public function test_apres_completed_collecte_pending_attribuee(): void
    {
        [$user, $merchant] = $this->createMerchant();
        $dm = $this->createDeliveryMan('Livreur 6');
        $parcel1 = $this->createParcel($merchant->id, 3000);
        $parcel2 = $this->createParcel($merchant->id, 7000);

        // Collecte 1 : scheduled_at = il y a 2h (prête à dispatch)
        $collection1 = Collection::create([
            'merchant_id' => $merchant->id,
            'status' => CollectionStatus::PENDING_ASSIGNMENT,
            'collection_date' => now()->toDateString(),
            'scheduled_at' => now()->subHours(2),
            'parcel_count' => 1,
            'total_cash_collection' => 3000,
            'total_delivery_amount' => 500,
        ]);
        $collection1->parcels()->attach($parcel1->id, ['status' => 1]);

        // Assigner manuellement la collecte 1
        $this->service->assignDeliveryman($collection1, $dm);

        $dm->refresh();
        $this->assertFalse($dm->is_available, 'Livreur doit être occupé après assignDeliveryman');

        // Collecte 2 : aussi prête
        $collection2 = Collection::create([
            'merchant_id' => $merchant->id,
            'status' => CollectionStatus::PENDING_ASSIGNMENT,
            'collection_date' => now()->toDateString(),
            'scheduled_at' => now()->subHours(1),
            'parcel_count' => 1,
            'total_cash_collection' => 7000,
            'total_delivery_amount' => 500,
        ]);
        $collection2->parcels()->attach($parcel2->id, ['status' => 1]);

        // Terminer la collecte 1
        $this->service->updateStatus($collection1->fresh(), CollectionStatus::COMPLETED);

        // Vérifier que la collecte 2 a été attribuée automatiquement
        $collection2->refresh();
        $this->assertEquals(CollectionStatus::ASSIGNED, $collection2->status, 'La collecte 2 PENDING échue doit être attribuée');
        $this->assertEquals($dm->id, $collection2->delivery_man_id, 'Le même livreur doit être ré-affecté');
    }

    // ─── TEST 7 : Collecte PENDING future → jamais attribuée ──

    public function test_collecte_pending_future_jamais_attribuee(): void
    {
        [$user, $merchant] = $this->createMerchant();
        $dm = $this->createDeliveryMan('Livreur 7');
        $parcel1 = $this->createParcel($merchant->id, 3000);
        $parcel2 = $this->createParcel($merchant->id, 7000);

        // Collecte 1 : prête (scheduled_at = il y a 1h)
        $collection1 = Collection::create([
            'merchant_id' => $merchant->id,
            'status' => CollectionStatus::PENDING_ASSIGNMENT,
            'collection_date' => now()->toDateString(),
            'scheduled_at' => now()->subHour(),
            'parcel_count' => 1,
            'total_cash_collection' => 3000,
            'total_delivery_amount' => 500,
        ]);
        $collection1->parcels()->attach($parcel1->id, ['status' => 1]);

        // Collecte 2 : FUTURE (scheduled_at = demain)
        $collection2 = Collection::create([
            'merchant_id' => $merchant->id,
            'status' => CollectionStatus::PENDING_ASSIGNMENT,
            'collection_date' => now()->addDay()->toDateString(),
            'scheduled_at' => now()->addDay(),
            'parcel_count' => 1,
            'total_cash_collection' => 7000,
            'total_delivery_amount' => 500,
        ]);
        $collection2->parcels()->attach($parcel2->id, ['status' => 1]);

        // Assigner la collecte 1
        $this->service->assignDeliveryman($collection1, $dm);

        // Terminer la collecte 1
        $this->service->updateStatus($collection1->fresh(), CollectionStatus::COMPLETED);

        // Vérifier que la collecte 2 FUTURE n'est PAS attribuée
        $collection2->refresh();
        $this->assertEquals(CollectionStatus::PENDING_ASSIGNMENT, $collection2->status, 'Collecte future ne doit PAS être attribuée');
        $this->assertNull($collection2->delivery_man_id);
    }

    // ─── TEST 8 : Deux collectes simultanées → même livreur impossible ──

    public function test_deux_collectes_simultanees_meme_livreur_impossible(): void
    {
        [$userA, $merchantA] = $this->createMerchant('Marchand A');
        [$userB, $merchantB] = $this->createMerchant('Marchand B');
        $dm = $this->createDeliveryMan('Livreur 8');

        $parcelA = $this->createParcel($merchantA->id, 5000);
        $parcelB = $this->createParcel($merchantB->id, 8000);

        // Deux collectes prêtes
        $collectionA = Collection::create([
            'merchant_id' => $merchantA->id,
            'status' => CollectionStatus::PENDING_ASSIGNMENT,
            'collection_date' => now()->toDateString(),
            'scheduled_at' => now()->subHour(),
            'parcel_count' => 1,
            'total_cash_collection' => 5000,
            'total_delivery_amount' => 500,
        ]);
        $collectionA->parcels()->attach($parcelA->id, ['status' => 1]);

        $collectionB = Collection::create([
            'merchant_id' => $merchantB->id,
            'status' => CollectionStatus::PENDING_ASSIGNMENT,
            'collection_date' => now()->toDateString(),
            'scheduled_at' => now()->subHour(),
            'parcel_count' => 1,
            'total_cash_collection' => 8000,
            'total_delivery_amount' => 500,
        ]);
        $collectionB->parcels()->attach($parcelB->id, ['status' => 1]);

        // Assigner la collecte A
        $this->service->assignDeliveryman($collectionA, $dm);

        // Tenter d'assigner la collecte B au même livreur
        $thrown = false;
        try {
            $this->service->assignDeliveryman($collectionB, $dm);
        } catch (\Exception $e) {
            $thrown = true;
            $this->assertStringContainsString('disponible', $e->getMessage());
        }

        $this->assertTrue($thrown, 'La deuxième assignation doit échouer');

        $collectionA->refresh();
        $collectionB->refresh();
        $this->assertEquals($dm->id, $collectionA->delivery_man_id);
        $this->assertNull($collectionB->delivery_man_id);
    }

    // ─── TEST 9 : collectCash() déjà effectué → refusé ──

    public function test_collect_cash_deja_effectue_refuse(): void
    {
        [$user, $merchant] = $this->createMerchant();
        $dm = $this->createDeliveryMan('Livreur 9');
        $parcel = $this->createParcel($merchant->id, 5000);

        $collection = Collection::create([
            'merchant_id' => $merchant->id,
            'status' => CollectionStatus::PENDING_ASSIGNMENT,
            'collection_date' => now()->toDateString(),
            'scheduled_at' => now()->subHour(),
            'parcel_count' => 1,
            'total_cash_collection' => 5000,
            'total_delivery_amount' => 500,
        ]);
        $collection->parcels()->attach($parcel->id, ['status' => 1]);
        $parcel->update(['status' => ParcelStatus::PICKUP_ASSIGN]);

        $this->service->dispatchDeliveryman($collection);
        $this->service->updateStatus($collection->fresh(), CollectionStatus::PICKING_UP);
        $this->service->updateStatus($collection->fresh(), CollectionStatus::COLLECTED);

        $tracking = CashTracking::where('parcel_id', $parcel->id)->first();
        $this->assertNotNull($tracking);

        // Premier encaissement
        $this->service->collectCash($tracking, 5000);

        // Deuxième tentative
        $thrown = false;
        try {
            $this->service->collectCash($tracking->fresh(), 5000);
        } catch (\Exception $e) {
            $thrown = true;
            $this->assertStringContainsString('déjà été encaissé', $e->getMessage());
        }

        $this->assertTrue($thrown, 'Deuxième collectCash doit échouer');
    }

    // ─── TEST 10 : handOverCash() supérieur au montant encaissé → refusé ──

    public function test_handover_superieur_encaisse_refuse(): void
    {
        [$user, $merchant] = $this->createMerchant();
        $dm = $this->createDeliveryMan('Livreur 10');
        $parcel = $this->createParcel($merchant->id, 5000);

        $collection = Collection::create([
            'merchant_id' => $merchant->id,
            'status' => CollectionStatus::PENDING_ASSIGNMENT,
            'collection_date' => now()->toDateString(),
            'scheduled_at' => now()->subHour(),
            'parcel_count' => 1,
            'total_cash_collection' => 5000,
            'total_delivery_amount' => 500,
        ]);
        $collection->parcels()->attach($parcel->id, ['status' => 1]);
        $parcel->update(['status' => ParcelStatus::PICKUP_ASSIGN]);

        $this->service->dispatchDeliveryman($collection);
        $this->service->updateStatus($collection->fresh(), CollectionStatus::PICKING_UP);
        $this->service->updateStatus($collection->fresh(), CollectionStatus::COLLECTED);

        $tracking = CashTracking::where('parcel_id', $parcel->id)->first();

        // Encaisser 3000
        $this->service->collectCash($tracking, 3000);

        // Tenter de remettre 5000 (> 3000 encaissé)
        $thrown = false;
        try {
            $this->service->handOverCash($tracking->fresh(), 5000, 'Agent WeCourier');
        } catch (\Exception $e) {
            $thrown = true;
            $this->assertStringContainsString('dépasser', $e->getMessage());
        }

        $this->assertTrue($thrown, 'handOverCash avec montant trop élevé doit échouer');
    }

    // ─── TEST 11 : Plusieurs collectes même marchand même jour → autorisées ──

    public function test_plusieurs_collectes_meme_marchand_meme_jour_autorisees(): void
    {
        [$user, $merchant] = $this->createMerchant();

        $parcel1 = $this->createParcel($merchant->id, 3000);
        $parcel2 = $this->createParcel($merchant->id, 7000);

        // Collecte 1 — demain matin
        $collection1 = $this->service->createCollection(
            $merchant,
            [$parcel1->id],
            null,
            now()->addDay()->toDateString(),
            '09:00-11:00',
        );

        // Collecte 2 — demain après-midi (même jour, créneau différent)
        $collection2 = $this->service->createCollection(
            $merchant,
            [$parcel2->id],
            null,
            now()->addDay()->toDateString(),
            '14:00-16:00',
        );

        $this->assertNotEquals($collection1->id, $collection2->id, 'Deux collectes différentes doivent exister');
        $this->assertEquals(CollectionStatus::PENDING_ASSIGNMENT, $collection1->status);
        $this->assertEquals(CollectionStatus::PENDING_ASSIGNMENT, $collection2->status);

        $count = Collection::where('merchant_id', $merchant->id)
            ->whereDate('collection_date', now()->addDay()->toDateString())
            ->count();
        $this->assertEquals(2, $count, 'Deux collectes doivent exister pour le même jour');
    }

    // ─── TEST 12 : Colis déjà dans une collecte active → impossible ──

    public function test_colis_deja_dans_collecte_active_impossible(): void
    {
        [$user, $merchant] = $this->createMerchant();
        $parcel = $this->createParcel($merchant->id);

        // Créer la première collecte avec ce colis
        $collection1 = $this->service->createCollection(
            $merchant,
            [$parcel->id],
            null,
            now()->addDay()->toDateString(),
            '09:00-11:00',
        );

        $this->assertEquals(CollectionStatus::PENDING_ASSIGNMENT, $collection1->status);

        // Tenter de créer une deuxième collecte avec le même colis
        $thrown = false;
        try {
            $this->service->createCollection(
                $merchant,
                [$parcel->id],
                null,
                now()->addDay()->toDateString(),
                '14:00-16:00',
            );
        } catch (\Exception $e) {
            $thrown = true;
            // Le colis n'est plus PENDING (statut changé à PICKUP_ASSIGN)
            // donc la première vérification le rejette
            $this->assertStringContainsString('disponible', $e->getMessage());
        }

        $this->assertTrue($thrown, 'Deuxième collecte avec le même colis doit échouer');
    }
}
