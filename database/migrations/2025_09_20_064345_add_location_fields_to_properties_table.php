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
        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('location');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->text('address')->nullable()->after('longitude');
            $table->string('neighborhood')->nullable()->after('address');
            
            // Add indexes for location-based queries
            $table->index(['latitude', 'longitude']);
            $table->index('neighborhood');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['neighborhood']);
            $table->dropColumn(['latitude', 'longitude', 'address', 'neighborhood']);
        });
    }
};