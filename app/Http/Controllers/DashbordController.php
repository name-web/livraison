<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Enums\ParcelStatus;
use App\Models\Backend\CourierStatement;
use App\Models\Backend\DeliverymanStatement;
use App\Models\Backend\MerchantStatement;
use App\Models\Backend\VatStatement;
use App\Models\User;
use App\Enums\StatementType;
use App\Models\Backend\Account;
use App\Models\Backend\BankTransaction;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Hub;
use App\Models\Backend\HubStatement;
use App\Models\Backend\Merchant;
use App\Models\Backend\Parcel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\Dashboard\DashboardInterface;

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
        $this->repo    = $repo;
     }
public function index(Request $request)
    {
      
        if(Auth::user()->user_type == UserType::MERCHANT){

            $merchant = Auth::user()->merchant;
            if (blank($merchant)) {
                abort(403);
            }

            $period  = $this->resolveMerchantPeriod($request);
            $data    = $this->repo->merchantDashboardData($merchant->id, $period);

            if ($period) {
                $start = $period['from'];
                $end   = $period['to'];
            } else {
                $start = Carbon::today()->subDays(7)->startOfDay()->toDateTimeString();
                $end   = Carbon::today()->endOfDay()->toDateTimeString();
            }
            $series = $this->repo->merchantDashboardDailySeries($merchant->id, $start, $end);

            $currency  = settings()->currency;
            $dateRange = '';
            $filterParams = [];
            if ($period) {
                $dateRange = Carbon::parse($period['from'])->format('m/d/Y') . ' To ' . Carbon::parse($period['to'])->format('m/d/Y');
                $filterParams = ['parcel_date' => $dateRange];
            }

            return view('backend.merchant_panel.dashboard', compact('merchant', 'data', 'series', 'currency', 'dateRange', 'period', 'filterParams'));

        }elseif(Auth::user()->user_type == UserType::ADMIN){

            $c_income       = CourierStatement::whereNot('parcel_id',null)->where('type',StatementType::INCOME)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $c_expense      = CourierStatement::whereNot('parcel_id',null)->where('type',StatementType::EXPENSE)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $d_income       = DeliverymanStatement::where('type',StatementType::INCOME)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $d_expense      = DeliverymanStatement::where('type',StatementType::EXPENSE)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $m_income       = MerchantStatement::where('type',StatementType::INCOME)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $m_expense      = MerchantStatement::where('type',StatementType::EXPENSE)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $v_income       = VatStatement::where('type',StatementType::INCOME)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $v_expense      = VatStatement::where('type',StatementType::EXPENSE)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $b_income       = BankTransaction::where('type',StatementType::INCOME)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $b_expense      = BankTransaction::where('type',StatementType::EXPENSE)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $h_income       = HubStatement::where('type',StatementType::INCOME)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $h_expense      = HubStatement::where('type',StatementType::EXPENSE)->whereBetween('updated_at',$this->repo->FromTo($request))->sum('amount');
            $data           = [];

            $data['recent_parcels']             = Parcel::whereBetween('created_at',$this->repo->FromTo($request))->orderByDesc('id')->limit(5)->get();
            $data['total_parcel']               = Parcel::whereBetween('created_at',$this->repo->FromTo($request))->count();//total parcel
            $data['total_user']                 = User::whereBetween('created_at',$this->repo->FromTo($request))->count();//total user
            $data['total_merchant']             = Merchant::whereBetween('created_at',$this->repo->FromTo($request))->count();//total merchant
            $data['total_delivery_man']         = DeliveryMan::whereBetween('created_at',$this->repo->FromTo($request))->count();//total delivery man
            $data['total_hubs']                 = Hub::whereBetween('created_at',$this->repo->FromTo($request))->count();//total hubs
            $data['total_accounts']             = Account::whereBetween('created_at',$this->repo->FromTo($request))->count();//total accounts
            //status wise parcel count
            $data['total_deliveryman_assigned'] = $this->repo->parcelPosition($request,ParcelStatus::DELIVERY_MAN_ASSIGN,$this->repo->FromTo($request))->count();
            $data['total_partial_deliverd']     = $this->repo->parcelPosition($request,ParcelStatus::PARTIAL_DELIVERED,$this->repo->FromTo($request))->count();
            $data['total_deliverd']             = $this->repo->parcelPosition($request,ParcelStatus::DELIVERED,$this->repo->FromTo($request))->count();
            //end status wise parcel count
            $data['hub_parcels']                = Hub::with(['parcels'])->whereBetween('updated_at',$this->repo->FromTo($request))->limit(4)->get();
            //end salary

            $dates                           =  $this->repo->Dates($request);// 7days
            $data['incomeDates']             =   $dates;
            $data['expenseDates']            =   $dates;
            $data['merchantRevDates']        =   $dates;
            $data['DeliverymanRevDates']     =   $dates;

            $fromTo                         = $this->repo->FromTo($request);//from/to date
            $request['date']  = Carbon::parse($fromTo['from'])->format('m/d/Y').' To '.Carbon::parse($fromTo['to'])->format('m/d/Y');
            
            $data['income']                 = $this->repo->income($fromTo);
            $data['expense']                = $this->repo->expense($fromTo);
            $data['merchantIncome']         = $this->repo->merchantIncome($fromTo);
            $data['merchantExpense']        = $this->repo->merchantExpense($fromTo);
            $data['deliverymanIncome']      = $this->repo->deliverymanIncome($fromTo);
            $data['deliverymanExpense']     = $this->repo->deliverymanExpense($fromTo);
            $data['bank_transactions']      = $this->repo->bankTransaction($fromTo);
            $data['courier_income']         = $this->repo->courierIncome($fromTo);
            $data['courier_expense']         = $this->repo->courierExpense($fromTo);

            return view('backend.dashboard', compact('c_income','c_expense','d_income','d_expense','m_income','m_expense','v_income','v_expense','b_income','b_expense','h_income','h_expense','data','request'));
        }else{
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
        $to   = $request->input('to');
        if (!is_string($from) || !is_string($to) || $from === '' || $to === '') {
            return null;
        }
        try {
            $fromDate = Carbon::parse($from);
            $toDate   = Carbon::parse($to);
        } catch (\Throwable $e) {
            return null;
        }
        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }
        return [
            'from' => $fromDate->startOfDay()->toDateTimeString(),
            'to'   => $toDate->endOfDay()->toDateTimeString(),
        ];
    }

    public function searchCharts(Request $request){
        $data    = [];
        $data['dates']                      = $this->repo->dates($request);
        $fromTo                             = $this->repo->FromTo($request);
        if($request->type     == 'income_expense'):
            $data['income']                 = $this->repo->income($fromTo);
            $data['expense']                = $this->repo->expense($fromTo);
        elseif($request->type == 'merchant'):
            $data['merchantIncome']         = $this->repo->merchantIncome($fromTo);
            $data['merchantExpense']        = $this->repo->merchantExpense($fromTo);
        elseif($request->type == 'deliveryman'):
            $data['deliverymanIncome']      = $this->repo->deliverymanIncome($fromTo);
            $data['deliverymanExpense']     = $this->repo->deliverymanExpense($fromTo);
        endif;

        return $data;

    }


    public function merchantDashboardFilter(Request $request){
        $date = $request->input('date');
        $to   = null;
        if (is_string($date) && str_contains($date, 'To')) {
            $parts = explode('To', $date);
            $date  = trim($parts[0]);
            $to    = trim($parts[1] ?? '');
        }
        try {
            $fromDate = Carbon::parse($date);
            $toDate   = ($to !== null && $to !== '') ? Carbon::parse($to) : $fromDate;
        } catch (\Throwable $e) {
            return redirect()->route('dashboard.index');
        }
        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }
        return redirect()->route('dashboard.index', [
            'from' => $fromDate->format('Y-m-d'),
            'to'   => $toDate->format('Y-m-d'),
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
     * @param  \Illuminate\Http\Request  $request
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
     * @param  \Illuminate\Http\Request  $request
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
