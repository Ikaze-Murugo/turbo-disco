<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analytics_cache', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key')->unique();
            $table->json('data');
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['cache_key', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_cache');
    }
};
