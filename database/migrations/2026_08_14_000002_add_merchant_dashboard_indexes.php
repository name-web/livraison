<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index composites pour les requêtes agrégées du dashboard marchand.
     */
    public function up(): void
    {
        if (Schema::hasTable('parcels') && !Schema::hasIndex('parcels', 'parcels_merchant_status_updated_index')) {
            DB::statement('ALTER TABLE `parcels` ADD INDEX `parcels_merchant_status_updated_index` (`merchant_id`, `status`, `updated_at`)');
        }
        if (Schema::hasTable('payments') && !Schema::hasIndex('payments', 'payments_merchant_status_index')) {
            DB::statement('ALTER TABLE `payments` ADD INDEX `payments_merchant_status_index` (`merchant_id`, `status`)');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('parcels') && Schema::hasIndex('parcels', 'parcels_merchant_status_updated_index')) {
            DB::statement('ALTER TABLE `parcels` DROP INDEX `parcels_merchant_status_updated_index`');
        }
        if (Schema::hasTable('payments') && Schema::hasIndex('payments', 'payments_merchant_status_index')) {
            DB::statement('ALTER TABLE `payments` DROP INDEX `payments_merchant_status_index`');
        }
    }
};