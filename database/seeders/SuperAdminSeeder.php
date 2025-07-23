<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder to create or update the Super Admin user with necessary permissions.
 *
 * @class SuperAdminSeeder
 */
class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@massdata.rs'],
            [
                'name'       => 'Admin',
                'email'      => 'admin@massdata.rs',
                'password'   => Hash::make('massdata'),
                'created_at' => Carbon::now(),
                'updated_at' => null,
            ]
        );

        $permissions = [
            'user-management',
            'import-orders',
            'import-products',
        ];

        $missingPermissions = collect($permissions)->reject(fn($perm) => $admin->hasPermissionTo($perm));

        if ($missingPermissions->isNotEmpty()) {
            $admin->givePermissionTo($missingPermissions->toArray());
        }
    }
}
