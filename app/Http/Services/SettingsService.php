<?php

namespace App\Http\Services;

use App\Enums\Status;
use App\Models\Backend\GeneralSettings;
use App\Models\Backend\GoogleMapSetting;
use App\Models\Backend\MerchantSetting;
use App\Models\Backend\NotificationSettings;
use App\Models\Backend\Setting;
use App\Models\Backend\SmsSendSetting;
use App\Models\Backend\SmsSetting;
use App\Models\Config;

class SettingsService
{
    /**
     * Récupère les settings généraux (id=1) avec logos.
     */
    public function getGeneral(): ?GeneralSettings
    {
        return GeneralSettings::with('rxlogo', 'rxfavicon')->find(1);
    }

    /**
     * Récupère une valeur par clé depuis la table configs.
     */
    public function get(string $key, $default = ''): string
    {
        $data = Config::where('key', $key)->first();

        return (! blank($data) && $data->value !== null) ? $data->value : $default;
    }

    /**
     * Lecture avec fallback vers env().
     */
    public function getRuntime(string $key, $default = null)
    {
        $envMap = [
            'mail_mailer' => 'MAIL_MAILER',
            'mail_host' => 'MAIL_HOST',
            'mail_port' => 'MAIL_PORT',
            'mail_username' => 'MAIL_USERNAME',
            'mail_password' => 'MAIL_PASSWORD',
            'mail_encryption' => 'MAIL_ENCRYPTION',
            'mail_from_address' => 'MAIL_FROM_ADDRESS',
            'mail_from_name' => 'MAIL_FROM_NAME',
        ];

        try {
            $value = $this->get($key);
            if ($value !== '') {
                return $value;
            }
        } catch (\Throwable $e) {
            // configs table may not exist during install
        }

        if ($default !== null) {
            return $default;
        }

        $envKey = $envMap[$key] ?? strtoupper($key);

        return env($envKey);
    }

    /**
     * Sauvegarde une valeur dans la table configs.
     */
    public function set(string $key, $value): void
    {
        Config::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Récupère une valeur depuis la table settings.
     */
    public function getGlobal(string $key)
    {
        $setting = Setting::where('key', $key)->first();

        return $setting?->value ?? null;
    }

    /**
     * Récupère une valeur SMS.
     */
    public function getSms(string $key)
    {
        $setting = SmsSetting::where('key', $key)->first();

        return $setting?->value ?? null;
    }

    /**
     * Récupère un setting marchand.
     */
    public function getMerchant(int $merchantId, string $key)
    {
        $setting = MerchantSetting::where(['merchant_id' => $merchantId, 'key' => $key])->first();

        return $setting?->value ?? null;
    }

    /**
     * Récupère les paramètres Google Map.
     */
    public function getGoogleMapKey(): string
    {
        $data = GoogleMapSetting::where('id', 1)->first();

        return $data?->map_key ?? '';
    }

    /**
     * Vérifie si un envoi SMS est actif pour un statut donné.
     */
    public function isSmsSendActive(string $status): bool
    {
        return SmsSendSetting::where([
            'sms_send_status' => $status,
            'status' => Status::ACTIVE,
        ])->exists();
    }

    /**
     * Récupère les paramètres de notification.
     */
    public function getNotificationSettings()
    {
        return NotificationSettings::find(1);
    }
}
