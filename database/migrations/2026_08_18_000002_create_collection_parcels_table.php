<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignId('parcel_id')->constrained('parcels')->cascadeOnDelete();
            $table->unsignedTinyInteger('status')->default(1)->comment('1=pending,2=picked_up,3=collected');
            $table->timestamps();

            $table->index('collection_id');
            $table->index('parcel_id');

            // Un colis ne peut appartenir qu'à une seule collecte active
            $table->unique('parcel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_parcels');
    }
};
