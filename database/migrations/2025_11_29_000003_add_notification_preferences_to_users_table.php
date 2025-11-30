<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_notifications')) {
                $table->boolean('email_notifications')->default(true)->after('is_verified');
            }
            if (!Schema::hasColumn('users', 'push_notifications')) {
                $table->boolean('push_notifications')->default(true)->after('email_notifications');
            }
            if (!Schema::hasColumn('users', 'notify_property_updates')) {
                $table->boolean('notify_property_updates')->default(true)->after('push_notifications');
            }
            if (!Schema::hasColumn('users', 'notify_messages')) {
                $table->boolean('notify_messages')->default(true)->after('notify_property_updates');
            }
            if (!Schema::hasColumn('users', 'notify_reviews')) {
                $table->boolean('notify_reviews')->default(true)->after('notify_messages');
            }
            if (!Schema::hasColumn('users', 'marketing_emails')) {
                $table->boolean('marketing_emails')->default(false)->after('notify_reviews');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_notifications',
                'push_notifications',
                'notify_property_updates',
                'notify_messages',
                'notify_reviews',
                'marketing_emails',
            ]);
        });
    }
};
