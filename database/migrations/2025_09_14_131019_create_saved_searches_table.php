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
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('search_query')->nullable();
            $table->json('filters'); // Store filter parameters as JSON
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_searched_at')->nullable();
            $table->integer('search_count')->default(0);
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index('last_searched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};