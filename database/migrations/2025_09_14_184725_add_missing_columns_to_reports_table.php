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
        Schema::table('reports', function (Blueprint $table) {
            // Add resolution_actions column if it doesn't exist
            if (!Schema::hasColumn('reports', 'resolution_actions')) {
                $table->json('resolution_actions')->nullable()->after('resolved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'resolution_actions')) {
                $table->dropColumn('resolution_actions');
            }
        });
    }
};