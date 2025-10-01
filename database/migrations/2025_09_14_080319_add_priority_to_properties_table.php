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
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('status');
            $table->boolean('is_featured')->default(false)->after('priority');
            $table->timestamp('featured_until')->nullable()->after('is_featured');
            $table->integer('view_count')->default(0)->after('featured_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['priority', 'is_featured', 'featured_until', 'view_count']);
        });
    }
};
