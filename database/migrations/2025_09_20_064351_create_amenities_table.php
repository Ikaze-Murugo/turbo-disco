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
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', [
                'restaurant', 
                'cafe', 
                'school', 
                'hospital', 
                'bank', 
                'pharmacy', 
                'supermarket', 
                'gym', 
                'park', 
                'bus_stop',
                'shopping_mall',
                'gas_station',
                'post_office',
                'police_station',
                'fire_station'
            ]);
            $table->text('address');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('phone', 50)->nullable();
            $table->string('website', 500)->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes for location-based queries
            $table->index(['latitude', 'longitude']);
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};