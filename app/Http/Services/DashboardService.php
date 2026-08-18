<?php

namespace App\Http\Services;

use App\Enums\AccountHeads;
use App\Enums\ApprovalStatus;
use App\Models\Backend\DeliverymanStatement;
use App\Models\Backend\Expense;
use App\Models\Backend\Income;
use App\Models\Backend\MerchantStatement;
use App\Models\Backend\Parcel;
use App\Models\Backend\Payment;
use Carbon\Carbon;

class DashboardService
{
    // ──────────────────────────────────────────
    //  Revenus / Dépenses jour par jour
    // ──────────────────────────────────────────

    public function dayIncome(string $date): float
    {
        return Income::where('date', Carbon::parse($date)->format('Y-m-d'))
            ->sum('amount');
    }

    public function dayExpense(string $date): float
    {
        return Expense::where('date', Carbon::parse($date)->format('Y-m-d'))
            ->sum('amount');
    }

    public function dayMerchantRevenueIncome(string $date): float
    {
        return MerchantStatement::where('type', AccountHeads::INCOME)
            ->where('date', Carbon::parse($date)->format('Y-m-d'))
            ->sum('amount');
    }

    public function dayMerchantRevenueExpense(string $date): float
    {
        return MerchantStatement::where('type', AccountHeads::EXPENSE)
            ->where('date', Carbon::parse($date)->format('Y-m-d'))
            ->sum('amount');
    }

    public function dayDeliverymanRevenueIncome(string $date): float
    {
        return DeliverymanStatement::where('type', AccountHeads::INCOME)
            ->where('date', Carbon::parse($date)->format('Y-m-d'))
            ->sum('amount');
    }

    public function dayDeliverymanRevenueExpense(string $date): float
    {
        return DeliverymanStatement::where('type', AccountHeads::EXPENSE)
            ->where('date', Carbon::parse($date)->format('Y-m-d'))
            ->sum('amount');
    }

    // ──────────────────────────────────────────
    //  Paiements marchands
    // ──────────────────────────────────────────

    public function merchantPayments(array $merchantIds): array
    {
        $query = Payment::whereIn('merchant_id', $merchantIds);

        return [
            'paidAmount' => (clone $query)->where('status', ApprovalStatus::APPROVED)->sum('amount'),
            'pendingAmount' => (clone $query)->where('status', ApprovalStatus::PENDING)->sum('amount'),
        ];
    }

    // ──────────────────────────────────────────
    //  Dépenses colis
    // ──────────────────────────────────────────

    public function parcelExpense(int $parcelId): float
    {
        $query = DeliverymanStatement::where('parcel_id', $parcelId)->where('cash_collection', 0);

        $income = (clone $query)->where('type', AccountHeads::INCOME)->sum('amount');
        $expense = (clone $query)->where('type', AccountHeads::EXPENSE)->sum('amount');

        return $income - $expense;
    }

    public function parcelExpenseTotal(array $parcelIds): float
    {
        $query = DeliverymanStatement::whereIn('parcel_id', $parcelIds)->where('cash_collection', 0);

        $income = (clone $query)->where('type', AccountHeads::INCOME)->sum('amount');
        $expense = (clone $query)->where('type', AccountHeads::EXPENSE)->sum('amount');

        return $income - $expense;
    }

    // ──────────────────────────────────────────
    //  Colis marchand
    // ──────────────────────────────────────────

    public function merchantParcels(int $merchantId): object
    {
        $query = Parcel::where('merchant_id', $merchantId);

        return (object) [
            'total_parcels' => (clone $query)->count(),
            'total_cash_amount' => (clone $query)->sum('cash_collection'),
            'total_current_payable' => (clone $query)->sum('current_payable'),
        ];
    }

    // ──────────────────────────────────────────
    //  Agrégations colis
    // ──────────────────────────────────────────

    public function totalParcelsCashCollection($parcels): float
    {
        $total = 0;
        foreach ($parcels as $parcel) {
            $total += $parcel->sum('cash_collection');
        }

        return $total;
    }

    public function parcelsStatus($parcels, string $ids = '', string $parcelIds = ''): array|Collection
    {
        if ($parcelIds === '') {
            $parcelIds = [];
            foreach ($parcels as $group) {
                foreach ($group as $parcel) {
                    $parcelIds[] = $parcel->id;
                }
            }
        }

        $collected = Parcel::whereIn('id', $parcelIds)->get();

        if ($ids !== '') {
            return $parcelIds;
        }

        return $collected->groupBy('status');
    }

    public function idWiseParcels($parcels, string $needId = '', string $idParcels = '')
    {
        if ($idParcels !== '') {
            return Parcel::whereIn('id', $idParcels)->get();
        }

        if ($needId !== '') {
            return $parcels->pluck('id')->toArray();
        }

        return collect();
    }

    // ──────────────────────────────────────────
    //  Salaire
    // ──────────────────────────────────────────

    public function salaryPayments(string $userId, array $payments = []): float
    {
        $amount = 0;
        foreach ($payments as $payment) {
            if ($payment->user_id == $userId && $payment->amount > 0) {
                $amount += $payment->amount;
            }
        }

        return $amount;
    }
}
