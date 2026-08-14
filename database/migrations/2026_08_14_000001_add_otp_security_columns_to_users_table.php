<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Colonnes OTP utilisées par le flux d'inscription marchand
     * (expiration 10 min, tentatives max, cooldown de renvoi).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable()->after('otp');
            }
            if (!Schema::hasColumn('users', 'otp_attempts')) {
                $table->tinyInteger('otp_attempts')->unsigned()->default(0)->after('otp_expires_at');
            }
            if (!Schema::hasColumn('users', 'otp_sent_at')) {
                $table->timestamp('otp_sent_at')->nullable()->after('otp_attempts');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_sent_at', 'otp_attempts', 'otp_expires_at']);
        });
    }
};
