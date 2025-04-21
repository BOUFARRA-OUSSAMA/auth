<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if table exists before proceeding
        if (!Schema::hasTable('attributes')) {
            $this->command->error('The attributes table does not exist. Run migrations first.');
            return;
        }

        // Get database driver
        $driver = DB::connection()->getDriverName();

        // Handle table truncation based on database driver
        if ($driver === 'pgsql') {
            // PostgreSQL specific code
            if (Schema::hasTable('attribute_values')) {
                DB::table('attribute_values')->delete();
            }
            DB::table('attributes')->delete();
        } else {
            // MySQL and other databases
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            if (Schema::hasTable('attribute_values')) {
                DB::table('attribute_values')->truncate();
            }
            DB::table('attributes')->truncate();

            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        }

        // Get entity type IDs
        $userEntityType = DB::table('entity_types')->where('code', 'user')->first();
        $productEntityType = DB::table('entity_types')->where('code', 'product')->first();

        if (!$userEntityType || !$productEntityType) {
            $this->command->error('Entity types not found. Make sure the EntityTypesSeeder has run successfully.');
            return;
        }

        // Insert user attributes
        DB::table('attributes')->insert([
            [
                'code' => 'address',
                'name' => 'Address',
                'type' => 'text',
                'description' => 'User\'s physical address',
                'is_required' => false,
                'entity_type_id' => $userEntityType->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'birth_date',
                'name' => 'Birth Date',
                'type' => 'date',
                'description' => 'User\'s date of birth',
                'is_required' => false,
                'entity_type_id' => $userEntityType->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'bio',
                'name' => 'Biography',
                'type' => 'text',
                'description' => 'User\'s personal biography',
                'is_required' => false,
                'entity_type_id' => $userEntityType->id,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insert product attributes
        DB::table('attributes')->insert([
            [
                'code' => 'price',
                'name' => 'Price',
                'type' => 'decimal',
                'description' => 'Product price',
                'is_required' => true,
                'entity_type_id' => $productEntityType->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'color',
                'name' => 'Color',
                'type' => 'string',
                'description' => 'Product color',
                'is_required' => false,
                'entity_type_id' => $productEntityType->id,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
