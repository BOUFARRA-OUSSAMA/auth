<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EntityTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if table exists before proceeding
        if (!Schema::hasTable('entity_types')) {
            $this->command->error('The entity_types table does not exist. Run migrations first.');
            return;
        }

        // Get database driver
        $driver = DB::connection()->getDriverName();

        // Handle table truncation based on database driver
        if ($driver === 'pgsql') {
            // PostgreSQL specific code
            // Safe delete for PostgreSQL without foreign key issues
            DB::table('entity_types')->delete();
        } else {
            // MySQL and other databases
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            DB::table('entity_types')->truncate();

            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        }

        // Insert basic entity types
        DB::table('entity_types')->insert([
            [
                'name' => 'User',
                'code' => 'user',
                'description' => 'User entity type',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Product',
                'code' => 'product',
                'description' => 'Product entity type',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Order',
                'code' => 'order',
                'description' => 'Order entity type',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
