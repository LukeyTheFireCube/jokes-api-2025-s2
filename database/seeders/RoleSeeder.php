<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super-user (999)
        $superUser = Role::firstOrCreate(
            ['name' => 'super-user', 'guard_name' => 'web'],
            ['description' => 'Has access to all system features', 'level' => 999]
        );
        $superUser->syncPermissions(Permission::all());

        // Admin (750)
        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['description' => 'Administrator with most system privileges', 'level' => 750]
        );
        $admin->syncPermissions(
            Permission::where('name', '!=', 'role.delete')->get()
        );

        // Staff (500)
        $staff = Role::firstOrCreate(
            ['name' => 'staff', 'guard_name' => 'web'],
            ['description' => 'Staff role', 'level' => 500]
        );
        $staff->syncPermissions([
            'joke.browse',
            'joke.read',
            'joke.add',
            'joke.edit',

            'vote.add',
            'vote.edit',
            'vote.delete',
            'vote.clearall',

            'category.browse',
            'category.read',

            'user.browse',
            'user.read',
        ]);


        // Client (100)
        $client = Role::firstOrCreate(
            ['name' => 'client', 'guard_name' => 'web'],
            ['description' => 'Client role', 'level' => 100]
        );
        $client->syncPermissions([
            'joke.browse',
            'joke.read',

            'vote.add',
            'vote.edit',
            'vote.delete',
        ]);
    }
}
