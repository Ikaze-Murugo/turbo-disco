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
        Schema::create('user_email_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->boolean('receive_announcements')->default(true);
            $table->boolean('receive_promotions')->default(true);
            $table->boolean('receive_system_emails')->default(true);
            $table->boolean('receive_newsletters')->default(true);
            $table->boolean('receive_property_updates')->default(true);
            $table->boolean('receive_message_notifications')->default(true);
            $table->enum('frequency', ['immediate', 'daily', 'weekly', 'monthly'])->default('immediate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_email_preferences');
    }
};