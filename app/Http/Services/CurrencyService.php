<?php

namespace App\Http\Services;

use App\Enums\Currency;
use App\Enums\UserType;
use Illuminate\Support\Facades\Auth;

class CurrencyService
{
    /**
     * Convertir un montant de base vers la devise d'affichage.
     */
    public function toDisplayCurrency(float $amount = 0): float
    {
        if (Auth::user()?->user_type === UserType::MERCHANT) {
            $merchantCurrency = Auth::user()->merchant->currency;
            if ($merchantCurrency === Currency::POUND) {
                return $amount * settings()->pound_rate;
            }
        } else {
            if (settings()->active_currency === Currency::POUND) {
                return $amount * settings()->pound_rate;
            }
        }

        return $amount;
    }

    /**
     * Reconversion : montant affiché vers la devise de base.
     */
    public function fromDisplayCurrency(float $amount = 0, ?string $currencyType = null): float
    {
        if ($currencyType) {
            return $currencyType === Currency::POUND
                ? (float) $amount / (float) settings()->pound_rate
                : $amount;
        }

        if (Auth::user()?->user_type === UserType::MERCHANT) {
            if (Auth::user()->merchant->currency === Currency::POUND) {
                return $amount / settings()->pound_rate;
            }
        } else {
            if (settings()->active_currency === Currency::POUND) {
                return $amount / settings()->pound_rate;
            }
        }

        return $amount;
    }

    /**
     * Formater un montant avec le symbole de devise.
     */
    public function format(float $amount = 0): string
    {
        if (Auth::user()?->user_type === UserType::MERCHANT) {
            if (Auth::user()->merchant->currency === Currency::POUND) {
                $amount = $amount * settings()->pound_rate;

                return 'LBP '.number_format($amount, 2);
            }

            return settings()->currency.number_format($amount, 2);
        }

        if (settings()->active_currency === Currency::POUND) {
            $amount = $amount * settings()->pound_rate;

            return 'LBP '.number_format($amount, 2);
        }

        return settings()->currency.number_format($amount, 2);
    }

    /**
     * Format monétaire français : 41 234,50 FCFA.
     */
    public function formatFrench(float $amount = 0, ?string $currency = null): string
    {
        $currency = $currency ?: (settings()->currency ?: 'FCFA');

        return number_format($amount, 2, ',', ' ').' '.$currency;
    }
}
