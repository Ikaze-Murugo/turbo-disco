<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminRole;
use App\Models\AdminPermission;

class AdminRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Permissions
        $permissions = [
            // Report Management
            ['name' => 'reports.view', 'description' => 'View all reports', 'category' => 'reports'],
            ['name' => 'reports.view_assigned', 'description' => 'View only assigned reports', 'category' => 'reports'],
            ['name' => 'reports.create', 'description' => 'Create reports', 'category' => 'reports'],
            ['name' => 'reports.edit', 'description' => 'Edit reports', 'category' => 'reports'],
            ['name' => 'reports.delete', 'description' => 'Delete reports', 'category' => 'reports'],
            ['name' => 'reports.assign', 'description' => 'Assign reports to admins', 'category' => 'reports'],
            ['name' => 'reports.resolve', 'description' => 'Resolve reports', 'category' => 'reports'],
            
            // Message Reports
            ['name' => 'message_reports.view', 'description' => 'View message reports', 'category' => 'message_reports'],
            ['name' => 'message_reports.view_assigned', 'description' => 'View assigned message reports', 'category' => 'message_reports'],
            ['name' => 'message_reports.assign', 'description' => 'Assign message reports', 'category' => 'message_reports'],
            ['name' => 'message_reports.resolve', 'description' => 'Resolve message reports', 'category' => 'message_reports'],
            
            // Analytics
            ['name' => 'analytics.view', 'description' => 'View analytics dashboard', 'category' => 'analytics'],
            ['name' => 'analytics.view_basic', 'description' => 'View basic analytics only', 'category' => 'analytics'],
            ['name' => 'analytics.export', 'description' => 'Export analytics data', 'category' => 'analytics'],
            
            // Admin Management
            ['name' => 'admins.view', 'description' => 'View admin list', 'category' => 'admins'],
            ['name' => 'admins.create', 'description' => 'Create new admins', 'category' => 'admins'],
            ['name' => 'admins.edit', 'description' => 'Edit admin details', 'category' => 'admins'],
            ['name' => 'admins.delete', 'description' => 'Delete admins', 'category' => 'admins'],
            ['name' => 'admins.assign_roles', 'description' => 'Assign roles to admins', 'category' => 'admins'],
            
            // User Management
            ['name' => 'users.view', 'description' => 'View user list', 'category' => 'users'],
            ['name' => 'users.edit', 'description' => 'Edit user details', 'category' => 'users'],
            ['name' => 'users.suspend', 'description' => 'Suspend users', 'category' => 'users'],
            ['name' => 'users.activate', 'description' => 'Activate users', 'category' => 'users'],
            
            // System Settings
            ['name' => 'settings.view', 'description' => 'View system settings', 'category' => 'settings'],
            ['name' => 'settings.edit', 'description' => 'Edit system settings', 'category' => 'settings'],
            ['name' => 'settings.categories', 'description' => 'Manage report categories', 'category' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            AdminPermission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create Admin Roles
        $roles = [
            [
                'name' => 'Super Admin',
                'level' => 4,
                'description' => 'Full system access with all permissions',
                'permissions' => [
                    'reports.view', 'reports.create', 'reports.edit', 'reports.delete', 'reports.assign', 'reports.resolve',
                    'message_reports.view', 'message_reports.assign', 'message_reports.resolve',
                    'analytics.view', 'analytics.export',
                    'admins.view', 'admins.create', 'admins.edit', 'admins.delete', 'admins.assign_roles',
                    'users.view', 'users.edit', 'users.suspend', 'users.activate',
                    'settings.view', 'settings.edit', 'settings.categories'
                ]
            ],
            [
                'name' => 'Admin Manager',
                'level' => 3,
                'description' => 'Manage admins and handle high-priority reports',
                'permissions' => [
                    'reports.view', 'reports.edit', 'reports.assign', 'reports.resolve',
                    'message_reports.view', 'message_reports.assign', 'message_reports.resolve',
                    'analytics.view', 'analytics.export',
                    'admins.view', 'admins.create', 'admins.edit', 'admins.assign_roles',
                    'users.view', 'users.edit', 'users.suspend', 'users.activate',
                    'settings.view', 'settings.categories'
                ]
            ],
            [
                'name' => 'Senior Admin',
                'level' => 2,
                'description' => 'Handle high-priority reports and assign tickets',
                'permissions' => [
                    'reports.view', 'reports.edit', 'reports.assign', 'reports.resolve',
                    'message_reports.view', 'message_reports.assign', 'message_reports.resolve',
                    'analytics.view_basic',
                    'users.view', 'users.edit'
                ]
            ],
            [
                'name' => 'Junior Admin',
                'level' => 1,
                'description' => 'Handle assigned tickets and basic operations',
                'permissions' => [
                    'reports.view_assigned', 'reports.edit', 'reports.resolve',
                    'message_reports.view_assigned', 'message_reports.resolve',
                    'analytics.view_basic'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);
            
            $role = AdminRole::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
            
            // Attach permissions to role (only if not already attached)
            $permissionIds = AdminPermission::whereIn('name', $permissions)->pluck('id');
            $existingPermissionIds = $role->permissions()->pluck('admin_permissions.id');
            $newPermissionIds = $permissionIds->diff($existingPermissionIds);
            
            if ($newPermissionIds->isNotEmpty()) {
                $role->permissions()->attach($newPermissionIds);
            }
        }
    }
}
