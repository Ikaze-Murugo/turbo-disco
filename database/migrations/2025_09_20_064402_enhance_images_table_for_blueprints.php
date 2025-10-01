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
        Schema::table('images', function (Blueprint $table) {
            // Check if columns don't already exist before adding them
            if (!Schema::hasColumn('images', 'image_type')) {
                $table->enum('image_type', [
                    'exterior', 
                    'interior', 
                    'kitchen', 
                    'bathroom', 
                    'bedroom', 
                    'living_room', 
                    'garden', 
                    'parking', 
                    'blueprint'
                ])->default('interior')->after('path');
            }
            
            if (!Schema::hasColumn('images', 'image_order')) {
                $table->integer('image_order')->default(0)->after('image_type');
            }
            
            // Add indexes if they don't exist
            if (!Schema::hasIndex('images', 'images_image_type_index')) {
                $table->index('image_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            if (Schema::hasIndex('images', 'images_image_type_index')) {
                $table->dropIndex(['image_type']);
            }
            if (Schema::hasColumn('images', 'image_type')) {
                $table->dropColumn('image_type');
            }
            if (Schema::hasColumn('images', 'image_order')) {
                $table->dropColumn('image_order');
            }
        });
    }
};