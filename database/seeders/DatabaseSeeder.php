<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Main database seeder class.
 *
 * @class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
