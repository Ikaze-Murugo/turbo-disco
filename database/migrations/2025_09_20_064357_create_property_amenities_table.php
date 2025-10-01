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
        Schema::create('property_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained()->onDelete('cascade');
            $table->decimal('distance_km', 5, 2);
            $table->integer('walking_time_minutes')->nullable();
            $table->integer('driving_time_minutes')->nullable();
            $table->timestamps();
            
            // Ensure unique property-amenity combinations
            $table->unique(['property_id', 'amenity_id']);
            
            // Add indexes for performance
            $table->index('property_id');
            $table->index('amenity_id');
            $table->index('distance_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_amenities');
    }
};