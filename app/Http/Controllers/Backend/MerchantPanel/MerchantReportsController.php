<?php

namespace App\Http\Controllers\Backend\MerchantPanel;

use App\Enums\CollectionStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Backend\Collection;
use App\Models\Backend\Parcel;
use App\Repositories\Reports\ReportsInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MerchantReportsController extends Controller
{
    protected $hub;
    protected $repo;
    public function __construct(ReportsInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Rapport récapitulatif des collectes du marchand.
     */
    public function collectionReports(Request $request)
    {
        $merchant = Auth::user()->merchant;
        $collections = collect();
        $stats = null;

        return view('backend.merchant_panel.reports.collection_reports', compact('collections', 'stats', 'request'));
    }

    /**
     * Rapport collectes filtré par date.
     */
    public function collectionSReports(Request $request)
    {
        $merchant = Auth::user()->merchant;
        $dateRange = $request->input('parcel_date');

        $query = Collection::where('merchant_id', $merchant->id)
            ->with('parcels', 'deliveryMan.user', 'shop');

        if ($dateRange && str_contains($dateRange, 'To')) {
            $parts = explode(' To ', $dateRange);
            $from = Carbon::parse(trim($parts[0]))->startOfDay();
            $to = Carbon::parse(trim($parts[1] ?? $parts[0]))->endOfDay();
            $query->whereBetween('collection_date', [$from, $to]);
        } elseif ($dateRange) {
            $query->whereDate('collection_date', Carbon::parse($dateRange));
        }

        $collections = $query->orderByDesc('collection_date')
            ->orderByDesc('created_at')
            ->get();

        // KPI stats
        $stats = [
            'total' => $collections->count(),
            'completed' => $collections->where('status', CollectionStatus::COMPLETED)->count(),
            'cancelled' => $collections->where('status', CollectionStatus::CANCELLED)->count(),
            'pending' => $collections->where('status', CollectionStatus::PENDING_ASSIGNMENT)->count(),
            'assigned' => $collections->where('status', CollectionStatus::ASSIGNED)->count(),
            'picking_up' => $collections->where('status', CollectionStatus::PICKING_UP)->count(),
            'collected' => $collections->where('status', CollectionStatus::COLLECTED)->count(),
            'total_parcels' => $collections->sum('parcel_count'),
            'total_cash' => $collections->sum('total_cash_collection'),
            'total_delivery' => $collections->sum('total_delivery_amount'),
        ];

        // ── Graphique évolution ──
        // Par jour : 30 derniers jours
        $dailyStart = $dateRange && str_contains((string) $dateRange, 'To')
            ? Carbon::parse(explode(' To ', $dateRange)[0])->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();
        $dailyEnd = $dateRange && str_contains((string) $dateRange, 'To')
            ? Carbon::parse(explode(' To ', $dateRange)[1])->endOfDay()
            : Carbon::now()->endOfDay();

        $dailyRows = Collection::where('merchant_id', $merchant->id)
            ->whereBetween('collection_date', [$dailyStart, $dailyEnd])
            ->selectRaw('collection_date as d, COUNT(*) as total, SUM(parcel_count) as parcels, SUM(total_cash_collection) as cash')
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $chartDaily = [
            'labels' => $dailyRows->pluck('d')->map(fn ($d) => Carbon::parse($d)->format('d/m'))->values()->all(),
            'total' => $dailyRows->pluck('total')->values()->all(),
            'parcels' => $dailyRows->pluck('parcels')->map(fn ($v) => (int) $v)->values()->all(),
            'cash' => $dailyRows->pluck('cash')->map(fn ($v) => round((float) $v, 2))->values()->all(),
        ];

        // Par semaine : 12 dernières semaines
        $weeklyStart = $dailyStart->copy()->startOfWeek(Carbon::MONDAY);
        $weeklyRows = Collection::where('merchant_id', $merchant->id)
            ->where('collection_date', '>=', $weeklyStart)
            ->selectRaw("DATE_FORMAT(collection_date, '%x-W%v') as wk, MIN(collection_date) as wk_start, COUNT(*) as total, SUM(parcel_count) as parcels, SUM(total_cash_collection) as cash")
            ->groupBy('wk', 'wk_start')
            ->orderBy('wk_start')
            ->get();

        $chartWeekly = [
            'labels' => $weeklyRows->pluck('wk_start')->map(fn ($d) => 'Sem '.Carbon::parse($d)->format('W'))->values()->all(),
            'total' => $weeklyRows->pluck('total')->values()->all(),
            'parcels' => $weeklyRows->pluck('parcels')->map(fn ($v) => (int) $v)->values()->all(),
            'cash' => $weeklyRows->pluck('cash')->map(fn ($v) => round((float) $v, 2))->values()->all(),
        ];

        return view('backend.merchant_panel.reports.collection_reports', compact(
            'collections', 'stats', 'request',
            'chartDaily', 'chartWeekly',
        ));
    }

    public function parcelReports(Request $request){
        $parcels = [];
        return view('backend.merchant_panel.reports.parcel_reports',compact('request','parcels'));
    }

    public function parcelSReports(Request $request){
        if($this->repo->merchantParcelReports($request)){
            $parcels      =  $this->repo->merchantParcelReports($request);
            $print        =   true;
            $parcel_ids   = '';
            foreach ($parcels as $key=>$parcel) {
                foreach ($parcel as $key => $parcl) {
                    $parcel_ids  = $parcl->id.','.$parcel_ids;
                }
            }
            return view('backend.merchant_panel.reports.parcel_reports',compact('parcels','request','print','parcel_ids'));
        }else{
            return redirect()->back();
        }
    }
    public function parcelReportsPrint(Request $request,$array){
        $parcel_ids  = [];
        foreach (explode(',',$array) as  $id) {
            if($id !== ""):
            $parcel_ids [] = $id;
            endif;
        }
        $parcels    = Parcel::whereIn('id',$parcel_ids)->orderBy('id')->get();
        $parcels    = $parcels->groupBy('status');
        return view('backend.merchant_panel.reports.parcel_reports_print',compact('parcels'));
    }
}
