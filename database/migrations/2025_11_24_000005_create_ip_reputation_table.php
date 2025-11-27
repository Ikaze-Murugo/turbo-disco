<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Track IP reputation scores for fraud detection.
     */
    public function up(): void
    {
        Schema::create('ip_reputation', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->integer('risk_score')->default(0); // 0-100, higher = more risky
            $table->string('country_code', 2)->nullable();
            $table->string('isp')->nullable();
            $table->boolean('is_proxy')->default(false);
            $table->boolean('is_vpn')->default(false);
            $table->boolean('is_tor')->default(false);
            $table->boolean('is_datacenter')->default(false);
            $table->integer('abuse_confidence_score')->nullable(); // From AbuseIPDB
            $table->json('additional_data')->nullable(); // Store extra metadata
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('risk_score');
            $table->index(['is_proxy', 'is_vpn', 'is_tor']);
            $table->index('last_checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_reputation');
    }
};
