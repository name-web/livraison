<?php

namespace App\Http\Controllers\Backend\MerchantPanel;

use App\Enums\AccountHeads;
use App\Http\Controllers\Controller;
use App\Models\Backend\MerchantStatement;
use App\Models\Backend\Parcel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatementsController extends Controller
{
    public function index()
    {
        $merchantId = Auth::user()->merchant->id;
        $statements = MerchantStatement::where('merchant_id', $merchantId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(40);
        $stats = $this->computeStats($merchantId);

        return view('backend.merchant_panel.statements.index', compact('statements', 'stats'));
    }

    public function filter(Request $request)
    {
        $id = Auth::user()->merchant->id;

        $statements = MerchantStatement::where('merchant_id', $id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->where(function ($query) use ($request) {
                if ($request->filled('date')) {
                    [$from, $to] = $this->parseDateRange($request->date);
                    $query->whereBetween('created_at', [$from, $to]);
                }
                if ($request->filled('type')) {
                    $query->where('type', $request->type);
                }
                if ($request->filled('parcel_tracking_id')) {
                    $parcelId = Parcel::where('tracking_id', $request->parcel_tracking_id)->value('id');
                    $query->where('parcel_id', $parcelId ?? 0);
                }
            })
            ->paginate(40)
            ->withQueryString();

        $stats = $this->computeStats($id, $request);

        return view('backend.merchant_panel.statements.index', compact('statements', 'stats', 'request'));
    }

    private function computeStats(int $merchantId, ?Request $request = null): array
    {
        $query = MerchantStatement::where('merchant_id', $merchantId);

        if ($request && $request->filled('date')) {
            [$from, $to] = $this->parseDateRange($request->date);
            $query->whereBetween('created_at', [$from, $to]);
        }
        if ($request && $request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request && $request->filled('parcel_tracking_id')) {
            $parcelId = Parcel::where('tracking_id', $request->parcel_tracking_id)->value('id');
            $query->where('parcel_id', $parcelId ?? 0);
        }

        $income = (clone $query)->where('type', AccountHeads::INCOME)->sum('amount');
        $expense = (clone $query)->where('type', AccountHeads::EXPENSE)->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
            'count' => (clone $query)->count(),
            'count_income' => (clone $query)->where('type', AccountHeads::INCOME)->count(),
            'count_expense' => (clone $query)->where('type', AccountHeads::EXPENSE)->count(),
        ];
    }

    private function parseDateRange(string $date): array
    {
        $parts = explode('To', $date);
        $from = Carbon::parse(trim($parts[0]))->startOfDay()->toDateTimeString();
        $to = isset($parts[1])
            ? Carbon::parse(trim($parts[1]))->endOfDay()->toDateTimeString()
            : Carbon::now()->endOfDay()->toDateTimeString();

        return [$from, $to];
    }
}
