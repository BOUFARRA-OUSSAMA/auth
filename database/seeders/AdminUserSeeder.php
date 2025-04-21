<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if required tables exist
        if (!Schema::hasTable('users') || !Schema::hasTable('roles')) {
            $this->command->error('The users or roles table does not exist. Run migrations first.');
            return;
        }

        // Check if admin user already exists
        $existingAdmin = DB::table('users')->where('email', 'admin@example.com')->first();

        if (!$existingAdmin) {
            // Create admin user
            $userId = DB::table('users')->insertGetId([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'phone' => '1234567890',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Find admin role
            $adminRole = DB::table('roles')->where('code', 'admin')->first();

            // Assign role if both user and role exist
            if ($userId && $adminRole && Schema::hasTable('user_roles')) {
                // Check if the assignment already exists
                $existingAssignment = DB::table('user_roles')
                    ->where('user_id', $userId)
                    ->where('role_id', $adminRole->id)
                    ->first();

                if (!$existingAssignment) {
                    DB::table('user_roles')->insert([
                        'user_id' => $userId,
                        'role_id' => $adminRole->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        } else {
            $this->command->info('Admin user already exists, skipping creation.');
        }
    }
}
