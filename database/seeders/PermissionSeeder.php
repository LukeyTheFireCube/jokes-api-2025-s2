<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // joke permissions
            ['name' => 'joke.browse'],
            ['name' => 'joke.read'],
            ['name' => 'joke.add'],
            ['name' => 'joke.edit'],
            ['name' => 'joke.delete'],

            // vote permissions
            ['name' => 'vote.add'],
            ['name' => 'vote.edit'],
            ['name' => 'vote.delete'],
            ['name' => 'vote.clearall'],

            // category permissions
            ['name' => 'category.browse'],
            ['name' => 'category.read'],
            ['name' => 'category.add'],
            ['name' => 'category.edit'],
            ['name' => 'category.delete'],

            // role management (admin-only)
            ['name' => 'role.browse'],
            ['name' => 'role.read'],
            ['name' => 'role.add'],
            ['name' => 'role.edit'],
            ['name' => 'role.delete'],

            // users
            ['name' => 'user.browse'],
            ['name' => 'user.read'],
            ['name' => 'user.add'],
            ['name' => 'user.edit'],
            ['name' => 'user.delete'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(
                ['name' => $p['name'], 'guard_name' => 'web']
            );
        }
    }
}
