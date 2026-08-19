<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collection_parcels', function (Blueprint $table) {
            // Supprimer la contrainte unique sur parcel_id
            // (empêchait d'ajouter un colis à une nouvelle collecte après annulation)
            $table->dropUnique(['parcel_id']);
        });

        Schema::table('collections', function (Blueprint $table) {
            // Raison d'annulation
            $table->text('cancel_reason')->nullable()->after('note');
            $table->timestamp('cancelled_at')->nullable()->after('collected_at');
        });
    }

    public function down(): void
    {
        Schema::table('collection_parcels', function (Blueprint $table) {
            $table->unique('parcel_id');
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['cancel_reason', 'cancelled_at']);
        });
    }
};
