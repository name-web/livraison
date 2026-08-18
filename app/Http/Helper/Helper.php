<?php

/*
|--------------------------------------------------------------------------
| WeCourier Global Helpers
|--------------------------------------------------------------------------
|
| Fonctions globales legerees. La logique metier est deleguee aux Services :
|   - CurrencyService    → currency(), currencyAmount(), formatPrice()
|   - ParcelStatusService→ parcelStatus(), StatusParcel(), statusIcon(), TodoStatus()
|   - SettingsService    → settings(), settingHelper(), runtimeConfig(), etc.
|   - DashboardService   → dayIncomeCount(), merchantPayments(), etc.
|   - NotificationService→ notifications(), calendarnewsoffer()
|
*/

use App\Enums\Currency;
use App\Enums\ParcelStatus;
use App\Enums\Status;
use App\Enums\TodoStatus;
use App\Enums\UserType;
use App\Http\Services\CurrencyService;
use App\Http\Services\DashboardService;
use App\Http\Services\NotificationService;
use App\Http\Services\ParcelStatusService;
use App\Http\Services\SettingsService;
use App\Models\Backend\FrontWeb\Section;
use App\Models\Backend\Hub;
use App\Models\Backend\HubInCharge;
use App\Models\Backend\Parcel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

// ──────────────────────────────────────────────
//  Service accessors (lazy singletons)
// ──────────────────────────────────────────────

function currencyService(): CurrencyService
{
    return app(CurrencyService::class);
}

function parcelStatusService(): ParcelStatusService
{
    return app(ParcelStatusService::class);
}

function settingsService(): SettingsService
{
    return app(SettingsService::class);
}

function dashboardService(): DashboardService
{
    return app(DashboardService::class);
}

function notificationService(): NotificationService
{
    return app(NotificationService::class);
}

// ──────────────────────────────────────────────
//  Settings helpers (backward compat)
// ──────────────────────────────────────────────

if (! function_exists('settings')) {
    function settings()
    {
        return settingsService()->getGeneral();
    }
}

if (! function_exists('settingHelper')) {
    function settingHelper(string $key)
    {
        return settingsService()->get($key);
    }
}

if (! function_exists('runtimeConfig')) {
    function runtimeConfig(string $key, $default = null)
    {
        return settingsService()->getRuntime($key, $default);
    }
}

if (! function_exists('saveRuntimeConfig')) {
    function saveRuntimeConfig(string $key, $value): void
    {
        settingsService()->set($key, $value);
    }
}

if (! function_exists('globalSettings')) {
    function globalSettings(string $key)
    {
        return settingsService()->getGlobal($key);
    }
}

if (! function_exists('smsSettings')) {
    function smsSettings(string $key)
    {
        return settingsService()->getSms($key);
    }
}

if (! function_exists('MerchantSettings')) {
    function MerchantSettings(string $key)
    {
        return settingsService()->getMerchant(Auth::user()->merchant->id, $key);
    }
}

if (! function_exists('MerchantSearchSettings')) {
    function MerchantSearchSettings(int $merchantId, string $key)
    {
        return settingsService()->getMerchant($merchantId, $key);
    }
}

if (! function_exists('googleMapSettingKey')) {
    function googleMapSettingKey(): string
    {
        return settingsService()->getGoogleMapKey();
    }
}

if (! function_exists('SmsSendSettingHelper')) {
    function SmsSendSettingHelper(string $status): bool
    {
        return settingsService()->isSmsSendActive($status);
    }
}

if (! function_exists('notificationSettings')) {
    function notificationSettings()
    {
        return settingsService()->getNotificationSettings();
    }
}

// ──────────────────────────────────────────────
//  Currency helpers (backward compat)
// ──────────────────────────────────────────────

if (! function_exists('currencyAmount')) {
    function currencyAmount($amount = 0)
    {
        return currencyService()->toDisplayCurrency((float) $amount);
    }
}

if (! function_exists('currencyAmountDevide')) {
    function currencyAmountDevide($amount = 0, $currency_type = null)
    {
        return currencyService()->fromDisplayCurrency((float) $amount, $currency_type);
    }
}

if (! function_exists('currency')) {
    function currency($amount = 0)
    {
        return currencyService()->format((float) $amount);
    }
}

if (! function_exists('formatPrice')) {
    function formatPrice($amount = 0, $currency = null)
    {
        return currencyService()->formatFrench((float) $amount, $currency);
    }
}

// ──────────────────────────────────────────────
//  Parcel status helpers (backward compat)
// ──────────────────────────────────────────────

if (! function_exists('parcelStatus')) {
    function parcelStatus($parcel, $request = null)
    {
        return parcelStatusService()->buildStatusDropdown($parcel);
    }
}

if (! function_exists('StatusParcel')) {
    function StatusParcel($status_id)
    {
        return parcelStatusService()->buildBadge($status_id);
    }
}

if (! function_exists('statusIcon')) {
    function statusIcon($status)
    {
        return parcelStatusService()->getIcon($status) ?? '';
    }
}

if (! function_exists('TodoStatus')) {
    function TodoStatus($todo)
    {
        return parcelStatusService()->buildTodoStatusDropdown($todo) ?? '';
    }
}

// ──────────────────────────────────────────────
//  Dashboard helpers (backward compat)
// ──────────────────────────────────────────────

if (! function_exists('dayIncomeCount')) {
    function dayIncomeCount($date)
    {
        return dashboardService()->dayIncome($date);
    }
}

if (! function_exists('dayExpenseCount')) {
    function dayExpenseCount($date)
    {
        return dashboardService()->dayExpense($date);
    }
}

if (! function_exists('dayMerchantRevIncomeCount')) {
    function dayMerchantRevIncomeCount($date)
    {
        return dashboardService()->dayMerchantRevenueIncome($date);
    }
}

if (! function_exists('dayMerchantRevExpenseCount')) {
    function dayMerchantRevExpenseCount($date)
    {
        return dashboardService()->dayMerchantRevenueExpense($date);
    }
}

if (! function_exists('dayDeliverymanRevIncomeCount')) {
    function dayDeliverymanRevIncomeCount($date)
    {
        return dashboardService()->dayDeliverymanRevenueIncome($date);
    }
}

if (! function_exists('dayDeliverymanRevExpenseCount')) {
    function dayDeliverymanRevExpenseCount($date)
    {
        return dashboardService()->dayDeliverymanRevenueExpense($date);
    }
}

if (! function_exists('merchantPayments')) {
    function merchantPayments($merchantID)
    {
        return dashboardService()->merchantPayments((array) $merchantID);
    }
}

if (! function_exists('parcelExpense')) {
    function parcelExpense($id)
    {
        return dashboardService()->parcelExpense($id);
    }
}

if (! function_exists('parcelExpenseTotal')) {
    function parcelExpenseTotal($ids)
    {
        return dashboardService()->parcelExpenseTotal((array) $ids);
    }
}

if (! function_exists('MerchantParcels')) {
    function MerchantParcels($merchant_id)
    {
        return dashboardService()->merchantParcels($merchant_id);
    }
}

if (! function_exists('totalParcelsCashcollection')) {
    function totalParcelsCashcollection($parcels)
    {
        return dashboardService()->totalParcelsCashCollection($parcels);
    }
}

if (! function_exists('parcelsStatus')) {
    function parcelsStatus($parcels, $ids = '', $parcel_ids = '')
    {
        return dashboardService()->parcelsStatus($parcels, $ids, $parcel_ids);
    }
}

if (! function_exists('idWiseParcels')) {
    function idWiseParcels($parcels, $neeId = '', $IdParcels = '')
    {
        return dashboardService()->idWiseParcels($parcels, $neeId, $IdParcels);
    }
}

if (! function_exists('salaryPayments')) {
    function salaryPayments($user_id = '', $salaryPayments = [])
    {
        return dashboardService()->salaryPayments($user_id, $salaryPayments);
    }
}

// ──────────────────────────────────────────────
//  Notification helpers (backward compat)
// ──────────────────────────────────────────────

if (! function_exists('notifications')) {
    function notifications()
    {
        return notificationService()->getForCurrentUser();
    }
}

if (! function_exists('calendarnewsoffer')) {
    function calendarnewsoffer($date)
    {
        return notificationService()->getNewsForDate($date);
    }
}

// ──────────────────────────────────────────────
//  Permission helper
// ──────────────────────────────────────────────

if (! function_exists('hasPermission')) {
    function hasPermission($permission = null): bool
    {
        return in_array($permission, Auth::user()->permissions ?? []);
    }
}

// ──────────────────────────────────────────────
//  Theme helpers
// ──────────────────────────────────────────────

if (! function_exists('active_theme')) {
    function active_theme(): string
    {
        return Cache::remember('active_theme', 3600, function () {
            $default = 'frontend.theme-1';

            try {
                $theme = \App\Models\Backend\Theme::where('is_active', true)->first();
                $path = str_replace('/', '.', $theme?->file_path ?? 'frontend/theme-1');

                if (\Illuminate\Support\Facades\View::exists($path.'.layouts.master')) {
                    return $path;
                }
            } catch (\Throwable $e) {
                // fall through to default
            }

            return $default;
        });
    }
}

if (! function_exists('theme_view')) {
    function theme_view(string $name, array $data = [])
    {
        return view(active_theme().'.'.$name, $data);
    }
}

// ──────────────────────────────────────────────
//  User helpers
// ──────────────────────────────────────────────

if (! function_exists('withoutUser')) {
    function withoutUser($ids)
    {
        $users = User::whereNotIn('id', $ids)
            ->whereNotIn('user_type', [UserType::DELIVERYMAN, UserType::MERCHANT])
            ->where('status', Status::ACTIVE)
            ->get();

        return $users->isNotEmpty() ? $users : [];
    }
}

if (! function_exists('unpaidUser')) {
    function unpaidUser($ids)
    {
        $users = User::whereIn('id', $ids)
            ->whereNotIn('user_type', [UserType::DELIVERYMAN, UserType::MERCHANT])
            ->where('status', Status::ACTIVE)
            ->get();

        return $users->isNotEmpty() ? $users : [];
    }
}

if (! function_exists('user')) {
    function user($id = null)
    {
        if ($id === null) {
            return User::all();
        }

        $user = User::find($id);

        return $user ?: '';
    }
}

if (! function_exists('singleUser')) {
    function singleUser($id)
    {
        $user = User::find($id);

        return $user ?: '';
    }
}

// ──────────────────────────────────────────────
//  Hub helpers
// ──────────────────────────────────────────────

if (! function_exists('hubs')) {
    function hubs()
    {
        return Hub::all();
    }
}

if (! function_exists('hubIncharge')) {
    function hubIncharge()
    {
        $hub = HubInCharge::where('user_id', Auth::id())->first();

        return $hub?->hub_id ?? 0;
    }
}

// ──────────────────────────────────────────────
//  Misc utility helpers
// ──────────────────────────────────────────────

if (! function_exists('dateFormat')) {
    function dateFormat($newDate = null)
    {
        $day = date('dS', strtotime($newDate));
        $month = strtolower(date('F', strtotime($newDate)));
        $year = date('Y', strtotime($newDate));

        return $day.' '.$month.' '.$year;
    }
}

if (! function_exists('oldLogDetails')) {
    function oldLogDetails($oldLogs, $newLogs)
    {
        foreach ($oldLogs as $key => $value) {
            if ($newLogs == $key) {
                return $value;
            }
        }

        return null;
    }
}

if (! function_exists('static_asset')) {
    function static_asset($path = '')
    {
        if (strpos(php_sapi_name(), 'cli') !== false || defined('LARAVEL_START_FROM_PUBLIC')) {
            return app('url')->asset($path);
        }

        return app('url')->asset('public/'.$path);
    }
}

if (! function_exists('paginate_redirect')) {
    function paginate_redirect($request)
    {
        return $request->page ? 'admin/parcel/index?page='.$request->page : 'admin/parcel/index';
    }
}

if (! function_exists('pluck')) {
    function pluck($array, $value, $key = null)
    {
        $returnArray = [];

        if (count($array)) {
            foreach ($array as $item) {
                if ($key !== null) {
                    $returnArray[$item->$key] = strtolower($value) === 'obj' ? $item : $item->$value;
                } else {
                    $returnArray[] = ($value === 'obj') ? $item : $item->$value;
                }
            }
        }

        return $returnArray;
    }
}

if (! function_exists('section')) {
    function section($type, $key)
    {
        $all_sections = Section::with('upload')->select('type', 'key', 'value')->get();
        $sections = [];

        foreach ($all_sections as $section) {
            if (str_contains($section->key, 'image') || str_contains($section->key, 'banner')) {
                $sections[$section->type][$section->key] = $section->image;
            } else {
                $sections[$section->type][$section->key] = $section->value;
            }
        }

        return data_get($sections, $type.'.'.$key, '');
    }
}
