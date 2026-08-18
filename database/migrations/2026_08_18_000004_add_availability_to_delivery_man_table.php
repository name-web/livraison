<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_man', function (Blueprint $table) {
            $table->boolean('is_available')->default(true)->after('current_location_long')->comment('Disponible pour collecte/livraison');
            $table->foreignId('current_hub_id')->nullable()->after('is_available')->constrained('hubs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('delivery_man', function (Blueprint $table) {
            $table->dropColumn(['is_available', 'current_hub_id']);
        });
    }
};
