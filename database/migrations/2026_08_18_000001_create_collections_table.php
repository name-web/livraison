<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->foreignId('delivery_man_id')->nullable()->constrained('delivery_man')->nullOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained('merchant_shops')->nullOnDelete();
            $table->unsignedTinyInteger('status')->default(1)->comment('1=pending,2=assigned,3=picking_up,4=collected,5=completed,6=cancelled');
            $table->string('pickup_address')->nullable();
            $table->decimal('pickup_lat', 10, 7)->nullable();
            $table->decimal('pickup_long', 10, 7)->nullable();
            $table->date('collection_date')->nullable()->comment('Date souhaitée de la collecte');
            $table->string('time_slot')->nullable()->comment('Créneau horaire souhaité');
            $table->timestamp('scheduled_at')->nullable()->comment('Date/heure précise planifiée');
            $table->integer('parcel_count')->default(0);
            $table->decimal('total_cash_collection', 13, 2)->default(0);
            $table->decimal('total_delivery_amount', 13, 2)->default(0);
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('merchant_id');
            $table->index('delivery_man_id');
            $table->index('shop_id');
            $table->index('status');
            $table->index('collection_date');
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
