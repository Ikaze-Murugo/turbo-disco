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
            $table->json('amenities')->nullable()->after('description');
            $table->json('features')->nullable()->after('amenities');
            $table->decimal('area', 10, 2)->nullable()->after('features'); // Property area in square meters
            $table->string('furnishing_status')->nullable()->after('area'); // furnished, semi-furnished, unfurnished
            $table->integer('parking_spaces')->default(0)->after('furnishing_status');
            $table->boolean('has_balcony')->default(false)->after('parking_spaces');
            $table->boolean('has_garden')->default(false)->after('has_balcony');
            $table->boolean('has_pool')->default(false)->after('has_garden');
            $table->boolean('has_gym')->default(false)->after('has_pool');
            $table->boolean('has_security')->default(false)->after('has_gym');
            $table->boolean('has_elevator')->default(false)->after('has_security');
            $table->boolean('has_air_conditioning')->default(false)->after('has_elevator');
            $table->boolean('has_heating')->default(false)->after('has_air_conditioning');
            $table->boolean('has_internet')->default(false)->after('has_heating');
            $table->boolean('has_cable_tv')->default(false)->after('has_internet');
            $table->boolean('pets_allowed')->default(false)->after('has_cable_tv');
            $table->boolean('smoking_allowed')->default(false)->after('pets_allowed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'amenities', 'features', 'area', 'furnishing_status', 'parking_spaces',
                'has_balcony', 'has_garden', 'has_pool', 'has_gym', 'has_security',
                'has_elevator', 'has_air_conditioning', 'has_heating', 'has_internet',
                'has_cable_tv', 'pets_allowed', 'smoking_allowed'
            ]);
        });
    }
};