<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if table exists before proceeding
        if (!Schema::hasTable('permissions')) {
            $this->command->error('The permissions table does not exist. Run migrations first.');
            return;
        }

        // Get database driver
        $driver = DB::connection()->getDriverName();

        // Handle table truncation based on database driver
        if ($driver === 'pgsql') {
            // PostgreSQL specific code
            DB::table('role_permissions')->delete();
            DB::table('permissions')->delete();
        } else {
            // MySQL and other databases
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            DB::table('role_permissions')->truncate();
            DB::table('permissions')->truncate();

            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        }

        // Define permission groups
        $userPermissions = [
            [
                'name' => 'View Users',
                'code' => 'users.view',
                'description' => 'Can view user list and details',
                'group' => 'User Management'
            ],
            [
                'name' => 'Create Users',
                'code' => 'users.create',
                'description' => 'Can create new users',
                'group' => 'User Management'
            ],
            [
                'name' => 'Edit Users',
                'code' => 'users.edit',
                'description' => 'Can edit existing users',
                'group' => 'User Management'
            ],
            [
                'name' => 'Delete Users',
                'code' => 'users.delete',
                'description' => 'Can delete users',
                'group' => 'User Management'
            ]
        ];

        $rolePermissions = [
            [
                'name' => 'View Roles',
                'code' => 'roles.view',
                'description' => 'Can view role list and details',
                'group' => 'Role Management'
            ],
            [
                'name' => 'Create Roles',
                'code' => 'roles.create',
                'description' => 'Can create new roles',
                'group' => 'Role Management'
            ],
            [
                'name' => 'Edit Roles',
                'code' => 'roles.edit',
                'description' => 'Can edit existing roles',
                'group' => 'Role Management'
            ],
            [
                'name' => 'Delete Roles',
                'code' => 'roles.delete',
                'description' => 'Can delete roles',
                'group' => 'Role Management'
            ],
            [
                'name' => 'Assign Permissions',
                'code' => 'roles.assign_permissions',
                'description' => 'Can assign permissions to roles',
                'group' => 'Role Management'
            ]
        ];

        $permissionPermissions = [
            [
                'name' => 'View Permissions',
                'code' => 'permissions.view',
                'description' => 'Can view permission list and details',
                'group' => 'Permission Management'
            ],
            [
                'name' => 'Create Permissions',
                'code' => 'permissions.create',
                'description' => 'Can create new permissions',
                'group' => 'Permission Management'
            ],
            [
                'name' => 'Edit Permissions',
                'code' => 'permissions.edit',
                'description' => 'Can edit existing permissions',
                'group' => 'Permission Management'
            ],
            [
                'name' => 'Delete Permissions',
                'code' => 'permissions.delete',
                'description' => 'Can delete permissions',
                'group' => 'Permission Management'
            ]
        ];

        $patientPermissions = [
            [
                'name' => 'View Patients',
                'code' => 'patients.view',
                'description' => 'Can view patient list and details',
                'group' => 'Patient Management'
            ],
            [
                'name' => 'Create Patients',
                'code' => 'patients.create',
                'description' => 'Can create new patient profiles',
                'group' => 'Patient Management'
            ],
            [
                'name' => 'Edit Patients',
                'code' => 'patients.edit',
                'description' => 'Can edit existing patient profiles',
                'group' => 'Patient Management'
            ],
            [
                'name' => 'Delete Patients',
                'code' => 'patients.delete',
                'description' => 'Can delete patient profiles',
                'group' => 'Patient Management'
            ]
        ];

        $doctorPermissions = [
            [
                'name' => 'View Doctors',
                'code' => 'doctors.view',
                'description' => 'Can view doctor list and details',
                'group' => 'Doctor Management'
            ],
            [
                'name' => 'Create Doctors',
                'code' => 'doctors.create',
                'description' => 'Can create new doctor profiles',
                'group' => 'Doctor Management'
            ],
            [
                'name' => 'Edit Doctors',
                'code' => 'doctors.edit',
                'description' => 'Can edit existing doctor profiles',
                'group' => 'Doctor Management'
            ],
            [
                'name' => 'Delete Doctors',
                'code' => 'doctors.delete',
                'description' => 'Can delete doctor profiles',
                'group' => 'Doctor Management'
            ]
        ];

        // Combine all permissions
        $allPermissions = array_merge(
            $userPermissions,
            $rolePermissions,
            $permissionPermissions,
            $patientPermissions,
            $doctorPermissions
        );

        // Insert all permissions
        foreach ($allPermissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Assign all permissions to admin role
        $adminRole = DB::table('roles')->where('code', 'admin')->first();
        if ($adminRole) {
            $permissions = DB::table('permissions')->get();

            foreach ($permissions as $permission) {
                DB::table('role_permissions')->insert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $this->command->info('All permissions assigned to admin role.');
        } else {
            $this->command->warn('Admin role not found. Permissions were not assigned.');
        }
    }
}
