<?php

namespace App\Http\Controllers\Backend\MerchantPanel;

use App\Enums\ApprovalStatus;
use App\Http\Controllers\Controller;
use App\Models\Backend\Payment;
use App\Models\MerchantPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountTransactionController extends Controller
{
    public function index()
    {
        $merchantId = Auth::user()->merchant->id;
        $accounts = MerchantPayment::where('merchant_id', $merchantId)->get();
        $transactions = Payment::where('merchant_id', $merchantId)->orderByDesc('id')->paginate(15);
        $stats = $this->computeStats($merchantId);

        return view('backend.merchant_panel.account_transaction.index', compact('accounts', 'transactions', 'stats'));
    }

    public function filter(Request $request)
    {
        $id = Auth::user()->merchant->id;
        $query = Payment::where('merchant_id', $id)->orderByDesc('id');

        // Date range
        if ($request->filled('date')) {
            [$from, $to] = $this->parseDateRange($request->date);
            $query->whereBetween('created_at', [$from, $to]);
        }

        // Status type
        if ($request->filled('type')) {
            $query->where('status', $request->type);
        }

        // Account
        if ($request->filled('account')) {
            $query->where('merchant_account', $request->account);
        }

        $transactions = $query->paginate(15)->withQueryString();
        $accounts = MerchantPayment::where('merchant_id', $id)->get();
        $stats = $this->computeStats($id, $request);

        return view('backend.merchant_panel.account_transaction.index', compact('accounts', 'transactions', 'stats', 'request'));
    }

    private function computeStats(int $merchantId, ?Request $request = null): array
    {
        $query = Payment::where('merchant_id', $merchantId);

        if ($request && $request->filled('date')) {
            [$from, $to] = $this->parseDateRange($request->date);
            $query->whereBetween('created_at', [$from, $to]);
        }
        if ($request && $request->filled('account')) {
            $query->where('merchant_account', $request->account);
        }

        $all = (clone $query)->get();

        $total = $all->sum('amount');
        $pending = (clone $query)->where('status', ApprovalStatus::PENDING)->sum('amount');
        $approved = (clone $query)->where('status', ApprovalStatus::APPROVED)->sum('amount');
        $rejected = (clone $query)->where('status', ApprovalStatus::REJECT)->sum('amount');

        $countPending = (clone $query)->where('status', ApprovalStatus::PENDING)->count();
        $countApproved = (clone $query)->where('status', ApprovalStatus::APPROVED)->count();
        $countRejected = (clone $query)->where('status', ApprovalStatus::REJECT)->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'count_all' => $all->count(),
            'count_pending' => $countPending,
            'count_approved' => $countApproved,
            'count_rejected' => $countRejected,
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
