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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Reviewer
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade'); // Landlord being reviewed
            $table->integer('property_rating')->unsigned(); // 1-5 stars for property
            $table->integer('landlord_rating')->unsigned(); // 1-5 stars for landlord
            $table->text('property_review')->nullable(); // Review text for property
            $table->text('landlord_review')->nullable(); // Review text for landlord
            $table->boolean('is_approved')->default(false); // Admin approval
            $table->boolean('is_anonymous')->default(false); // Anonymous review option
            $table->timestamps();
            
            // Ensure a user can only review a property once
            $table->unique(['user_id', 'property_id']);
            $table->index(['property_id', 'is_approved']);
            $table->index(['landlord_id', 'is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};