<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add phone verification fields for enhanced user authentication.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('phone_verified_at')->nullable()->after('phone_number');
            $table->string('phone_verification_code', 10)->nullable()->after('phone_verified_at');
            $table->timestamp('phone_verification_code_expires_at')->nullable()->after('phone_verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_verified_at',
                'phone_verification_code',
                'phone_verification_code_expires_at'
            ]);
        });
    }
};
