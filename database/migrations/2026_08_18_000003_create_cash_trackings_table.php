<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->nullable()->constrained('collections')->nullOnDelete();
            $table->foreignId('parcel_id')->nullable()->constrained('parcels')->nullOnDelete();
            $table->foreignId('delivery_man_id')->constrained('delivery_man')->cascadeOnDelete();
            $table->foreignId('merchant_id')->nullable()->constrained('merchants')->nullOnDelete();
            $table->decimal('amount_expected', 13, 2)->default(0)->comment('Montant COD attendu');
            $table->decimal('amount_collected', 13, 2)->default(0)->comment('Montant réellement encaissé');
            $table->decimal('amount_handed_over', 13, 2)->default(0)->comment('Montant remis à WeCourier');
            $table->decimal('amount_remaining', 13, 2)->default(0)->comment('Montant restant à remettre');
            $table->unsignedTinyInteger('status')->default(1)->comment('1=pending,2=collected,3=handed_over,4=reconciled,5=anomaly');
            $table->text('anomaly_note')->nullable()->comment('Note en cas d\'écart');
            $table->string('handed_over_to')->nullable()->comment('Nom de la personne qui reçoit');
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('handed_over_at')->nullable();
            $table->timestamps();

            $table->index('delivery_man_id');
            $table->index('merchant_id');
            $table->index('collection_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_trackings');
    }
};
