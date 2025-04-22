<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing permissions if needed
        // DB::table('permissions')->truncate(); // Be careful with this in production

        // Define permission groups
        $permissionGroups = [
            'user-management' => 'User Management',
            'role-management' => 'Role Management',
            'permission-management' => 'Permission Management',
            'content-management' => 'Content Management',
            'system' => 'System Administration',
        ];

        // Define permissions
        $permissions = [
            // User Management
            [
                'name' => 'View Users',
                'code' => 'users:view',
                'description' => 'Can view users list',
                'group' => 'User Management'
            ],
            [
                'name' => 'Create Users',
                'code' => 'users:create',
                'description' => 'Can create new users',
                'group' => 'User Management'
            ],
            [
                'name' => 'Edit Users',
                'code' => 'users:edit',
                'description' => 'Can edit existing users',
                'group' => 'User Management'
            ],
            [
                'name' => 'Delete Users',
                'code' => 'users:delete',
                'description' => 'Can delete users',
                'group' => 'User Management'
            ],
            [
                'name' => 'Assign Roles',
                'code' => 'users:assign-roles',
                'description' => 'Can assign roles to users',
                'group' => 'User Management'
            ],

            // Role Management
            [
                'name' => 'View Roles',
                'code' => 'roles:view',
                'description' => 'Can view roles list',
                'group' => 'Role Management'
            ],
            [
                'name' => 'Create Roles',
                'code' => 'roles:create',
                'description' => 'Can create new roles',
                'group' => 'Role Management'
            ],
            [
                'name' => 'Edit Roles',
                'code' => 'roles:edit',
                'description' => 'Can edit existing roles',
                'group' => 'Role Management'
            ],
            [
                'name' => 'Delete Roles',
                'code' => 'roles:delete',
                'description' => 'Can delete roles',
                'group' => 'Role Management'
            ],

            // Permission Management
            [
                'name' => 'View Permissions',
                'code' => 'permissions:view',
                'description' => 'Can view permissions list',
                'group' => 'Permission Management'
            ],
            [
                'name' => 'Assign Permissions',
                'code' => 'permissions:assign',
                'description' => 'Can assign permissions to roles',
                'group' => 'Permission Management'
            ],

            // Content Management
            [
                'name' => 'View Content',
                'code' => 'content:view',
                'description' => 'Can view content',
                'group' => 'Content Management'
            ],
            [
                'name' => 'Create Content',
                'code' => 'content:create',
                'description' => 'Can create new content',
                'group' => 'Content Management'
            ],
            [
                'name' => 'Edit Content',
                'code' => 'content:edit',
                'description' => 'Can edit existing content',
                'group' => 'Content Management'
            ],
            [
                'name' => 'Delete Content',
                'code' => 'content:delete',
                'description' => 'Can delete content',
                'group' => 'Content Management'
            ],

            // System Administration
            [
                'name' => 'System Settings',
                'code' => 'system:settings',
                'description' => 'Can modify system settings',
                'group' => 'System Administration'
            ],
            [
                'name' => 'System Logs',
                'code' => 'system:logs',
                'description' => 'Can view system logs',
                'group' => 'System Administration'
            ],
        ];

        // Insert permissions with timestamps
        $now = now();
        foreach ($permissions as &$permission) {
            $permission['created_at'] = $now;
            $permission['updated_at'] = $now;
        }

        DB::table('permissions')->insert($permissions);

        $this->command->info('Permissions seeded successfully.');

        // Assign all permissions to admin role
        $adminRole = DB::table('roles')->where('code', 'admin')->first();
        if ($adminRole) {
            $permissionIds = DB::table('permissions')->pluck('id')->toArray();
            $rolePermissions = [];

            foreach ($permissionIds as $permissionId) {
                $rolePermissions[] = [
                    'role_id' => $adminRole->id,
                    'permission_id' => $permissionId,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            DB::table('role_permissions')->insert($rolePermissions);
            $this->command->info('Assigned all permissions to admin role.');
        }
    }
}
