<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Store fraud scores for users and properties (Phase 1 implementation).
     */
    public function up(): void
    {
        Schema::create('fraud_scores', function (Blueprint $table) {
            $table->id();
            $table->string('scoreable_type'); // 'App\Models\User' or 'App\Models\Property'
            $table->unsignedBigInteger('scoreable_id');
            $table->integer('fraud_score')->default(0); // 0-100, higher = more suspicious
            $table->string('risk_level')->default('low'); // 'low', 'medium', 'high', 'critical'
            $table->json('risk_factors')->nullable(); // Array of detected risk factors
            $table->json('score_breakdown')->nullable(); // Detailed scoring breakdown
            $table->string('model_version')->nullable(); // Which model/rule version was used
            $table->boolean('is_flagged')->default(false); // Auto-flagged for review
            $table->boolean('admin_reviewed')->default(false);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            // Polymorphic index
            $table->index(['scoreable_type', 'scoreable_id']);
            $table->index('fraud_score');
            $table->index('risk_level');
            $table->index(['is_flagged', 'admin_reviewed']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fraud_scores');
    }
};
