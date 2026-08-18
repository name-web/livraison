<?php

namespace App\Http\Controllers;

use App\Enums\CollectionStatus;
use App\Enums\ParcelStatus;
use App\Enums\StatementType;
use App\Enums\UserType;
use App\Models\Backend\Account;
use App\Models\Backend\Collection;
use App\Models\Backend\BankTransaction;
use App\Models\Backend\CourierStatement;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\DeliverymanStatement;
use App\Models\Backend\Hub;
use App\Models\Backend\HubStatement;
use App\Models\Backend\Merchant;
use App\Models\Backend\MerchantStatement;
use App\Models\Backend\Parcel;
use App\Models\Backend\VatStatement;
use App\Models\MerchantShops;
use App\Models\User;
use App\Repositories\Dashboard\DashboardInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashbordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $repo;

    public function __construct(DashboardInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {

        if (Auth::user()->user_type == UserType::MERCHANT) {

            $merchant = Auth::user()->merchant;
            if (blank($merchant)) {
                abort(403);
            }

            $period = $this->resolveMerchantPeriod($request);

            // Période par défaut : le mois en cours (comme la maquette « CA du mois »).
            // $period reste null si aucun filtre n'est appliqué par l'utilisateur.
            if ($period) {
                $start = $period['from'];
                $end = $period['to'];
                $scope = $period;
            } else {
                $start = Carbon::today()->startOfMonth()->startOfDay()->toDateTimeString();
                $end = Carbon::today()->endOfDay()->toDateTimeString();
                $scope = ['from' => $start, 'to' => $end];
            }

            $data = $this->repo->merchantDashboardData($merchant->id, $scope);

            $series = $this->repo->merchantDashboardDailySeries($merchant->id, $start, $end);

            // Écart de colis vs période précédente de même longueur (affiché dans le hero)
            $windowDays = (int) Carbon::parse($start)->startOfDay()->diffInDays(Carbon::parse($end)->startOfDay()) + 1;
            $prevEnd = Carbon::parse($start)->subDay()->endOfDay();
            $prevStart = Carbon::parse($start)->subDays($windowDays)->startOfDay();
            $prevCount = Parcel::where('merchant_id', $merchant->id)
                ->whereBetween('updated_at', [$prevStart->toDateTimeString(), $prevEnd->toDateTimeString()])
                ->count();
            $parcelDelta = $data['counts']['total'] - $prevCount;

            $currency = settings()->currency;
            $dateRange = '';
            $filterParams = [];
            if ($period) {
                $dateRange = Carbon::parse($period['from'])->format('m/d/Y').' To '.Carbon::parse($period['to'])->format('m/d/Y');
                $filterParams = ['parcel_date' => $dateRange];
            }

            $recentParcels = Parcel::where('merchant_id', $merchant->id)
                ->with('merchantShop')
                ->orderByDesc('updated_at')
                ->take(5)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'tracking_id' => $p->tracking_id,
                    'customer_name' => $p->customer_name,
                    'customer_phone' => $p->customer_phone,
                    'cash_collection' => (float) $p->cash_collection,
                    'delivery_charge' => (float) $p->delivery_charge,
                    'status' => $p->status,
                    'status_label' => $p->parcel_status,
                    'created_at' => $p->created_at?->format('d/m/Y'),
                    'updated_at' => $p->updated_at?->format('d/m/Y H:i'),
                ]);

            $recentTransactions = MerchantStatement::where('merchant_id', $merchant->id)
                ->with('parcel')
                ->orderByDesc('id')
                ->take(7)
                ->get()
                ->map(fn ($t) => [
                    'id' => $t->id,
                    'type' => (int) $t->type,
                    'amount' => (float) $t->amount,
                    'tracking_id' => optional($t->parcel)->tracking_id,
                    'created_at' => $t->created_at?->format('d/m/Y H:i'),
                ]);

            // Colis du jour vs hier (carte du bas « Colis aujourd'hui »)
            $todayCount = Parcel::where('merchant_id', $merchant->id)
                ->whereBetween('updated_at', [Carbon::today()->startOfDay()->toDateTimeString(), Carbon::today()->endOfDay()->toDateTimeString()])
                ->count();
            $yesterdayCount = Parcel::where('merchant_id', $merchant->id)
                ->whereBetween('updated_at', [Carbon::yesterday()->startOfDay()->toDateTimeString(), Carbon::yesterday()->endOfDay()->toDateTimeString()])
                ->count();
            if ($yesterdayCount > 0) {
                $todayDeltaPct = round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100);
                $todayDeltaLabel = ($todayDeltaPct >= 0 ? '+' : '').$todayDeltaPct.'% '.__('merchant.vs_hier');
            } else {
                $todayDeltaLabel = __('merchant.vs_hier');
            }

            $shopsQuery = Parcel::where('merchant_id', $merchant->id)
                ->whereNotNull('merchant_shop_id');
            if ($period) {
                $shopsQuery->whereBetween('updated_at', [$period['from'], $period['to']]);
            }
            $shopCounts = $shopsQuery
                ->groupBy('merchant_shop_id')
                ->selectRaw('merchant_shop_id, COUNT(*) as parcels_count')
                ->pluck('parcels_count', 'merchant_shop_id');

            $shops = MerchantShops::where('merchant_id', $merchant->id)
                ->get()
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'address' => $s->address,
                    'parcels' => (int) ($shopCounts[$s->id] ?? 0),
                    'initials' => collect(preg_split('/\s+/', trim((string) $s->name)))
                        ->filter()
                        ->take(2)
                        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                        ->implode(''),
                ])
                ->sortByDesc('parcels')
                ->values()
                ->take(4)
                ->all();

            // ── KPIs Collectes ──
            $collectionStats = [
                'today' => Collection::where('merchant_id', $merchant->id)
                    ->whereDate('collection_date', now()->toDateString())->count(),
                'active' => Collection::where('merchant_id', $merchant->id)
                    ->whereIn('status', [
                        CollectionStatus::PENDING_ASSIGNMENT,
                        CollectionStatus::ASSIGNED,
                        CollectionStatus::PICKING_UP,
                        CollectionStatus::COLLECTED,
                    ])->count(),
                'pending' => Collection::where('merchant_id', $merchant->id)
                    ->where('status', CollectionStatus::PENDING_ASSIGNMENT)->count(),
                'completed' => Collection::where('merchant_id', $merchant->id)
                    ->where('status', CollectionStatus::COMPLETED)
                    ->whereDate('collection_date', now()->toDateString())->count(),
            ];

            return view('backend.merchant_panel.dashboard', compact('merchant', 'data', 'series', 'currency', 'dateRange', 'period', 'filterParams', 'recentParcels', 'recentTransactions', 'shops', 'parcelDelta', 'todayCount', 'todayDeltaLabel', 'collectionStats'));

        } elseif (Auth::user()->user_type == UserType::ADMIN) {

            $c_income = CourierStatement::whereNot('parcel_id', null)->where('type', StatementType::INCOME)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $c_expense = CourierStatement::whereNot('parcel_id', null)->where('type', StatementType::EXPENSE)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $d_income = DeliverymanStatement::where('type', StatementType::INCOME)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $d_expense = DeliverymanStatement::where('type', StatementType::EXPENSE)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $m_income = MerchantStatement::where('type', StatementType::INCOME)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $m_expense = MerchantStatement::where('type', StatementType::EXPENSE)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $v_income = VatStatement::where('type', StatementType::INCOME)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $v_expense = VatStatement::where('type', StatementType::EXPENSE)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $b_income = BankTransaction::where('type', StatementType::INCOME)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $b_expense = BankTransaction::where('type', StatementType::EXPENSE)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $h_income = HubStatement::where('type', StatementType::INCOME)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $h_expense = HubStatement::where('type', StatementType::EXPENSE)->whereBetween('updated_at', $this->repo->FromTo($request))->sum('amount');
            $data = [];

            $data['recent_parcels'] = Parcel::whereBetween('created_at', $this->repo->FromTo($request))->orderByDesc('id')->limit(5)->get();
            $data['total_parcel'] = Parcel::whereBetween('created_at', $this->repo->FromTo($request))->count(); // total parcel
            $data['total_user'] = User::whereBetween('created_at', $this->repo->FromTo($request))->count(); // total user
            $data['total_merchant'] = Merchant::whereBetween('created_at', $this->repo->FromTo($request))->count(); // total merchant
            $data['total_delivery_man'] = DeliveryMan::whereBetween('created_at', $this->repo->FromTo($request))->count(); // total delivery man
            $data['total_hubs'] = Hub::whereBetween('created_at', $this->repo->FromTo($request))->count(); // total hubs
            $data['total_accounts'] = Account::whereBetween('created_at', $this->repo->FromTo($request))->count(); // total accounts
            // status wise parcel count
            $data['total_deliveryman_assigned'] = $this->repo->parcelPosition($request, ParcelStatus::DELIVERY_MAN_ASSIGN, $this->repo->FromTo($request))->count();
            $data['total_partial_deliverd'] = $this->repo->parcelPosition($request, ParcelStatus::PARTIAL_DELIVERED, $this->repo->FromTo($request))->count();
            $data['total_deliverd'] = $this->repo->parcelPosition($request, ParcelStatus::DELIVERED, $this->repo->FromTo($request))->count();
            // end status wise parcel count
            $data['hub_parcels'] = Hub::with(['parcels'])->whereBetween('updated_at', $this->repo->FromTo($request))->limit(4)->get();
            // end salary

            $dates = $this->repo->Dates($request); // 7days
            $data['incomeDates'] = $dates;
            $data['expenseDates'] = $dates;
            $data['merchantRevDates'] = $dates;
            $data['DeliverymanRevDates'] = $dates;

            $fromTo = $this->repo->FromTo($request); // from/to date
            $request['date'] = Carbon::parse($fromTo['from'])->format('m/d/Y').' To '.Carbon::parse($fromTo['to'])->format('m/d/Y');

            $data['income'] = $this->repo->income($fromTo);
            $data['expense'] = $this->repo->expense($fromTo);
            $data['merchantIncome'] = $this->repo->merchantIncome($fromTo);
            $data['merchantExpense'] = $this->repo->merchantExpense($fromTo);
            $data['deliverymanIncome'] = $this->repo->deliverymanIncome($fromTo);
            $data['deliverymanExpense'] = $this->repo->deliverymanExpense($fromTo);
            $data['bank_transactions'] = $this->repo->bankTransaction($fromTo);
            $data['courier_income'] = $this->repo->courierIncome($fromTo);
            $data['courier_expense'] = $this->repo->courierExpense($fromTo);

            return view('backend.dashboard', compact('c_income', 'c_expense', 'd_income', 'd_expense', 'm_income', 'm_expense', 'v_income', 'v_expense', 'b_income', 'b_expense', 'h_income', 'h_expense', 'data', 'request'));
        } else {
            return redirect()->route('home');
        }
    }

    /**
     * Résout la période du filtre marchand à partir des paramètres GET from/to (format Y-m-d).
     * Aucune donnée issue de la requête n'est utilisée hors de ces deux dates.
     */
    private function resolveMerchantPeriod(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        if (! is_string($from) || ! is_string($to) || $from === '' || $to === '') {
            return null;
        }
        try {
            $fromDate = Carbon::parse($from);
            $toDate = Carbon::parse($to);
        } catch (\Throwable $e) {
            return null;
        }
        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        return [
            'from' => $fromDate->startOfDay()->toDateTimeString(),
            'to' => $toDate->endOfDay()->toDateTimeString(),
        ];
    }

    public function searchCharts(Request $request)
    {
        $data = [];
        $data['dates'] = $this->repo->dates($request);
        $fromTo = $this->repo->FromTo($request);
        if ($request->type == 'income_expense') {
            $data['income'] = $this->repo->income($fromTo);
            $data['expense'] = $this->repo->expense($fromTo);
        } elseif ($request->type == 'merchant') {
            $data['merchantIncome'] = $this->repo->merchantIncome($fromTo);
            $data['merchantExpense'] = $this->repo->merchantExpense($fromTo);
        } elseif ($request->type == 'deliveryman') {
            $data['deliverymanIncome'] = $this->repo->deliverymanIncome($fromTo);
            $data['deliverymanExpense'] = $this->repo->deliverymanExpense($fromTo);
        }

        return $data;

    }

    public function merchantDashboardFilter(Request $request)
    {
        $date = $request->input('date');
        $to = null;
        if (is_string($date) && str_contains($date, 'To')) {
            $parts = explode('To', $date);
            $date = trim($parts[0]);
            $to = trim($parts[1] ?? '');
        }
        try {
            $fromDate = Carbon::parse($date);
            $toDate = ($to !== null && $to !== '') ? Carbon::parse($to) : $fromDate;
        } catch (\Throwable $e) {
            return redirect()->route('dashboard.index');
        }
        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        return redirect()->route('dashboard.index', [
            'from' => $fromDate->format('Y-m-d'),
            'to' => $toDate->format('Y-m-d'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
