<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AdminRole;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or find the Super Admin role
        $superAdminRole = AdminRole::where('name', 'Super Admin')->first();
        
        if (!$superAdminRole) {
            $this->command->error('Super Admin role not found. Please run AdminRolesAndPermissionsSeeder first.');
            return;
        }
        
        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@murugo.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_verified' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        
        // Assign Super Admin role if not already assigned
        if (!$superAdmin->adminRoles()->where('role_id', $superAdminRole->id)->exists()) {
            $superAdmin->adminRoles()->attach($superAdminRole->id, [
                'assigned_by' => $superAdmin->id, // Self-assigned for the first admin
                'assigned_at' => now(),
                'is_active' => true,
            ]);
        }
        
        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: admin@murugo.com');
        $this->command->info('Password: admin123');
    }
}
