<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super-User
        $superUser = Role::firstOrCreate(
            ['name' => 'super-user'],
            ['description' => 'Has access to all system features']
        );

        $superUser->permissions()->sync(
            Permission::all()->pluck('id')->toArray()
        );

        // Admin
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator with most system privileges']
        );

        // Admin gets everything EXCEPT:
        // - role.delete (from your table)
        $admin->permissions()->sync(
            Permission::where('name', '!=', 'role.delete')->pluck('id')->toArray()
        );

        // Staff
        $client = Role::firstOrCreate(
            ['name' => 'staff'],
            ['description' => 'Basic user role']
        );

        // Client
        $client = Role::firstOrCreate(
            ['name' => 'client'],
            ['description' => 'Basic user role']
        );

        $client->permissions()->sync([]);
    }
}
