<?php

namespace Tests\Feature;

use App\Enums\ApprovalStatus;
use App\Enums\ParcelStatus;
use App\Enums\Status;
use App\Enums\UserType;
use App\Models\Backend\Merchant;
use App\Models\Backend\Parcel;
use App\Models\Backend\Payment;
use App\Models\MerchantShops;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MerchantDashboardTest extends TestCase
{
    use DatabaseTransactions;

    private function createMerchantUser(string $name): array
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

    private function createParcel(int $merchantId, int $status, float $cash, string $invoiceNo, array $extra = []): Parcel
    {
        return Parcel::create(array_merge([
            'merchant_id' => $merchantId,
            'customer_name' => 'Client '.$invoiceNo,
            'invoice_no' => $invoiceNo,
            'cash_collection' => $cash,
            'selling_price' => $cash * 0.8,
            'delivery_charge' => 1000,
            'vat_amount' => 180,
            'cod_amount' => 500,
            'total_delivery_amount' => 1180,
            'status' => $status,
            'parcel_payment_method' => 1,
        ], $extra));
    }

    public function test_merchant_sees_only_its_own_dashboard_data(): void
    {
        [$userA, $merchantA] = $this->createMerchantUser('Marchand A');
        [$userB, $merchantB] = $this->createMerchantUser('Marchand B');

        $this->createParcel($merchantA->id, ParcelStatus::PENDING, 25000, 'T-A-1');
        $this->createParcel($merchantA->id, ParcelStatus::DELIVERED, 30000, 'T-A-2');
        $this->createParcel($merchantA->id, ParcelStatus::RETURN_RECEIVED_BY_MERCHANT, 8000, 'T-A-3');
        $this->createParcel($merchantB->id, ParcelStatus::DELIVERED, 99999, 'T-B-1');

        $response = $this->actingAs($userA)->get(route('dashboard.index'));

        $response->assertOk();
        $view = $response->viewData('data');

        $this->assertSame(3, $view['counts']['total'], 'Le marchand A doit voir 3 colis (les siens)');
        $this->assertSame(1, $view['counts']['delivered']);
        $this->assertSame(1, $view['counts']['returned']);
        $this->assertSame(1, $view['counts']['on_going']);
        $this->assertSame(55000.0, $view['amounts']['cash_collection'], 'Les colis retournés (30) sont exclus des encaissements');
        $this->assertNotContains(99999.0, $view['amounts']);
    }

    public function test_filter_redirects_and_persists_in_url(): void
    {
        [$userA, $merchantA] = $this->createMerchantUser('Marchand Filtre');

        $today = now();
        $this->createParcel($merchantA->id, ParcelStatus::PENDING, 25000, 'T-F-1', [
            'updated_at' => $today->toDateTimeString(),
        ]);
        $this->createParcel($merchantA->id, ParcelStatus::DELIVERED, 30000, 'T-F-2', [
            'updated_at' => $today->copy()->subDays(5)->toDateTimeString(),
        ]);

        $from = $today->format('m/d/Y');
        $to = $today->format('m/d/Y');

        $response = $this->actingAs($userA)->post(route('merchant-panel.dashboard.filter'), ['date' => $from.' To '.$to]);
        $response->assertRedirect(route('dashboard.index', [
            'from' => $today->format('Y-m-d'),
            'to' => $today->format('Y-m-d'),
        ]));

        $filtered = $this->actingAs($userA)->get(route('dashboard.index', [
            'from' => $today->format('Y-m-d'),
            'to' => $today->format('Y-m-d'),
        ]));
        $filtered->assertOk();
        $data = $filtered->viewData('data');

        $this->assertSame(1, $data['counts']['total'], 'Seul le colis modifié aujourd\'hui doit compter');
        $this->assertSame(0, $data['counts']['delivered']);
        $this->assertSame(25000.0, $data['amounts']['cash_collection']);
    }

    public function test_empty_state_for_new_merchant(): void
    {
        [$userA, $merchantA] = $this->createMerchantUser('Marchand Vide');

        $response = $this->actingAs($userA)->get(route('dashboard.index'));

        $response->assertOk();
        $data = $response->viewData('data');
        $this->assertSame(0, $data['counts']['total']);
        $this->assertSame(0, $data['payments']['total']);
    }

    public function test_pending_and_processed_payments_are_summed(): void
    {
        [$userA, $merchantA] = $this->createMerchantUser('Marchand Paiement');

        $payment = Payment::create(['merchant_id' => $merchantA->id, 'amount' => 20000, 'created_by' => UserType::MERCHANT]);
        $payment->status = ApprovalStatus::PENDING;
        $payment->save();
        $payment = Payment::create(['merchant_id' => $merchantA->id, 'amount' => 10000, 'created_by' => UserType::MERCHANT]);
        $payment->status = ApprovalStatus::PROCESSED;
        $payment->save();
        $payment = Payment::create(['merchant_id' => $merchantA->id, 'amount' => 5000, 'created_by' => UserType::MERCHANT]);
        $payment->status = ApprovalStatus::REJECT;
        $payment->save();

        $response = $this->actingAs($userA)->get(route('dashboard.index'));
        $data = $response->viewData('data');

        $this->assertSame(20000.0, $data['payments']['pending']);
        $this->assertSame(10000.0, $data['payments']['paid']);
        $this->assertSame(3, $data['payments']['total']);
    }

    public function test_admin_cannot_access_merchant_routes(): void
    {
        $admin = User::create(['name' => 'Admin Test', 'user_type' => UserType::ADMIN]);

        $this->actingAs($admin)->get(route('merchant-panel.parcel.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('dashboard.index'))->assertOk();
    }

    public function test_deliveryman_is_redirected_from_dashboard(): void
    {
        $deliveryman = User::create(['name' => 'Livreur Test', 'user_type' => UserType::DELIVERYMAN]);

        $this->actingAs($deliveryman)->get(route('dashboard.index'))->assertRedirect(route('home'));
        $this->actingAs($deliveryman)->get(route('merchant-panel.parcel.index'))->assertForbidden();
    }

    public function test_pickup_points_page_renders_with_stats(): void
    {
        [$userA, $merchantA] = $this->createMerchantUser('Marchand Boutiques');

        MerchantShops::create([
            'merchant_id' => $merchantA->id,
            'name' => 'Boutique Abidjan',
            'contact_no' => '+2250701020304',
            'address' => 'Treichville, Abidjan',
            'status' => Status::ACTIVE,
            'default_shop' => Status::ACTIVE,
        ]);
        MerchantShops::create([
            'merchant_id' => $merchantA->id,
            'name' => 'Boutique Yamoussoukro',
            'contact_no' => '+2250705060708',
            'address' => 'Belle Ville, Yamoussoukro',
            'status' => Status::INACTIVE,
            'default_shop' => Status::INACTIVE,
        ]);

        $response = $this->actingAs($userA)->get(route('merchant-panel.shops.index'));

        $response->assertOk();
        $response->assertSee('Boutique Abidjan');
        $response->assertSee('Boutique Yamoussoukro');
        $response->assertSee('wa.me/2250701020304');

        $stats = $response->viewData('stats');
        $this->assertSame(2, $stats['total']);
        $this->assertSame(1, $stats['active']);
        $this->assertSame(1, $stats['inactive']);
        $this->assertSame(1, $stats['default']);
    }
}
