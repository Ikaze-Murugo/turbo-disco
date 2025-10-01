<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\User;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user or create one
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $admin = User::create([
                'name' => 'System Admin',
                'email' => 'admin@murugo.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);
        }

        $templates = [
            [
                'name' => 'Welcome Email',
                'subject' => 'Welcome to {{platform_name}}!',
                'content' => "Hello {{user_name}},\n\nWelcome to {{platform_name}}! We're excited to have you join our community.\n\nYour account has been successfully created and verified. You can now start exploring properties, connecting with landlords, and finding your perfect home.\n\nIf you have any questions, feel free to contact our support team.\n\nBest regards,\nThe {{platform_name}} Team",
                'category' => 'system',
                'variables' => ['user_name', 'platform_name'],
                'is_active' => true,
            ],
            [
                'name' => 'Property Listing Approved',
                'subject' => 'Your property listing has been approved!',
                'content' => "Hello {{user_name}},\n\nGreat news! Your property listing has been approved and is now live on {{platform_name}}.\n\nYour property is now visible to potential renters and you can start receiving inquiries.\n\nThank you for using {{platform_name}}!\n\nBest regards,\nThe {{platform_name}} Team",
                'category' => 'system',
                'variables' => ['user_name', 'platform_name'],
                'is_active' => true,
            ],
            [
                'name' => 'Monthly Newsletter',
                'subject' => '{{platform_name}} Monthly Newsletter - {{current_date}}',
                'content' => "Hello {{user_name}},\n\nHere's what's happening this month on {{platform_name}}:\n\n• New features and improvements\n• Market insights and trends\n• Success stories from our community\n• Tips for landlords and renters\n\nThank you for being part of our community!\n\nBest regards,\nThe {{platform_name}} Team",
                'category' => 'newsletter',
                'variables' => ['user_name', 'platform_name', 'current_date'],
                'is_active' => true,
            ],
            [
                'name' => 'Promotional Offer',
                'subject' => 'Special Offer: Premium Listing Features',
                'content' => "Hello {{user_name}},\n\nWe have an exclusive offer for you!\n\nGet 50% off on premium listing features for your properties. This includes:\n\n• Featured placement in search results\n• Priority in property recommendations\n• Enhanced visibility\n• Advanced analytics\n\nThis offer is valid until the end of the month. Don't miss out!\n\nBest regards,\nThe {{platform_name}} Team",
                'category' => 'promotional',
                'variables' => ['user_name', 'platform_name'],
                'is_active' => true,
            ],
            [
                'name' => 'Platform Announcement',
                'subject' => 'Important Update: New Features Available',
                'content' => "Hello {{user_name}},\n\nWe're excited to announce new features on {{platform_name}}:\n\n• Enhanced search filters\n• Improved messaging system\n• Mobile app updates\n• New property comparison tools\n\nThese updates are designed to make your experience even better.\n\nBest regards,\nThe {{platform_name}} Team",
                'category' => 'announcement',
                'variables' => ['user_name', 'platform_name'],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create([
                'name' => $template['name'],
                'subject' => $template['subject'],
                'content' => $template['content'],
                'category' => $template['category'],
                'variables' => json_encode($template['variables']),
                'is_active' => $template['is_active'],
                'created_by' => $admin->id,
            ]);
        }
    }
}