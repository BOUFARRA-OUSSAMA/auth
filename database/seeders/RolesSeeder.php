<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if table exists before proceeding
        if (!Schema::hasTable('roles')) {
            $this->command->error('The roles table does not exist. Run migrations first.');
            return;
        }

        // Get database driver
        $driver = DB::connection()->getDriverName();

        // Handle table truncation based on database driver
        if ($driver === 'pgsql') {
            // PostgreSQL specific code - just delete all rows
            if (Schema::hasTable('user_roles')) {
                DB::table('user_roles')->delete();
            }
            DB::table('roles')->delete();
        } else {
            // MySQL and other databases
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            if (Schema::hasTable('user_roles')) {
                DB::table('user_roles')->truncate();
            }
            DB::table('roles')->truncate();

            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        }

        // Insert basic roles
        DB::table('roles')->insert([
            [
                'name' => 'Administrator',
                'code' => 'admin',
                'description' => 'System administrator with full access',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'User',
                'code' => 'user',
                'description' => 'Regular user with standard access',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Manager',
                'code' => 'manager',
                'description' => 'Department manager with elevated access',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
