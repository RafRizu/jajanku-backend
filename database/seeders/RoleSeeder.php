<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = ['admin', 'seller', 'buyer', 'driver'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'api']);
        }

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@jajanku.id'],
            [
                'name'     => 'Admin Jajanku',
                'password' => Hash::make('password'),
                'phone'    => '08100000001',
            ]
        );
        $admin->syncRoles(['admin']);

        // Demo Seller
        $seller = User::firstOrCreate(
            ['email' => 'seller@jajanku.id'],
            [
                'name'     => 'Warung Bu Siti',
                'password' => Hash::make('password'),
                'phone'    => '08100000002',
            ]
        );
        $seller->syncRoles(['seller']);

        // Demo Buyer
        $buyer = User::firstOrCreate(
            ['email' => 'buyer@jajanku.id'],
            [
                'name'     => 'Budi Santoso',
                'password' => Hash::make('password'),
                'phone'    => '08100000003',
            ]
        );
        $buyer->syncRoles(['buyer']);

        // Demo Driver
        $driver = User::firstOrCreate(
            ['email' => 'driver@jajanku.id'],
            [
                'name'     => 'Joko Driver',
                'password' => Hash::make('password'),
                'phone'    => '08100000004',
            ]
        );
        $driver->syncRoles(['driver']);

        $this->command->info('Roles and demo users seeded successfully!');
    }
}
