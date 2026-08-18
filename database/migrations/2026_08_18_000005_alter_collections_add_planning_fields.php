<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            // Ajouter shop_id si pas déjà présent
            if (! Schema::hasColumn('collections', 'shop_id')) {
                $table->foreignId('shop_id')->nullable()->after('delivery_man_id')->constrained('merchant_shops')->nullOnDelete();
            }

            // Ajouter time_slot si pas déjà présent
            if (! Schema::hasColumn('collections', 'time_slot')) {
                $table->string('time_slot')->nullable()->after('collection_date')->comment('Créneau horaire souhaité');
            }

            // Ajouter scheduled_at si pas déjà présent
            if (! Schema::hasColumn('collections', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable()->after('time_slot')->comment('Date/heure précise planifiée');
            }

            // Ajouter index scheduled_at
            if (! Schema::hasIndex('collections', 'collections_scheduled_at_index')) {
                $table->index('scheduled_at');
            }

            // Ajouter index shop_id
            if (! Schema::hasIndex('collections', 'collections_shop_id_index')) {
                $table->index('shop_id');
            }
        });

        // Supprimer la contrainte unique si elle existe
        // (Schema::hasIndex ne fonctionne pas toujours pour les unique, on utilise une protection)
        try {
            Schema::table('collections', function (Blueprint $table) {
                $table->dropUnique(['merchant_id', 'collection_date']);
            });
        } catch (\Exception $e) {
            // La contrainte n'existe pas, rien à faire
        }
    }

    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn(['shop_id', 'time_slot', 'scheduled_at']);
        });
    }
};
