<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add ML-related fields to images table for duplicate detection.
     */
    public function up(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('image_hash', 64)->nullable()->after('image_path')->index(); // Perceptual hash for duplicate detection
            $table->json('exif_data')->nullable()->after('image_hash'); // EXIF metadata
            $table->integer('image_size')->nullable()->after('exif_data'); // File size in bytes
            $table->integer('image_width')->nullable()->after('image_size'); // Width in pixels
            $table->integer('image_height')->nullable()->after('image_width'); // Height in pixels
            $table->string('mime_type')->nullable()->after('image_height'); // image/jpeg, image/png, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn([
                'image_hash',
                'exif_data',
                'image_size',
                'image_width',
                'image_height',
                'mime_type'
            ]);
        });
    }
};
