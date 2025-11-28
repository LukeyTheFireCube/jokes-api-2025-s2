<?php

namespace Database\Seeders;

use App\Models\Permission;
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
            ['name' => 'joke.browse', 'label' => 'Browse jokes'],
            ['name' => 'joke.read', 'label' => 'Read joke'],
            ['name' => 'joke.add', 'label' => 'Add joke'],
            ['name' => 'joke.edit', 'label' => 'Edit joke'],
            ['name' => 'joke.delete', 'label' => 'Delete joke'],

            // category permissions
            ['name' => 'category.browse', 'label' => 'Browse categories'],
            ['name' => 'category.read', 'label' => 'Read category'],
            ['name' => 'category.add', 'label' => 'Add category'],
            ['name' => 'category.edit', 'label' => 'Edit category'],
            ['name' => 'category.delete', 'label' => 'Delete category'],

            // roles (admin-only)
            ['name' => 'role.browse', 'label' => 'Browse roles'],
            ['name' => 'role.read', 'label' => 'Read role'],
            ['name' => 'role.add', 'label' => 'Add role'],
            ['name' => 'role.edit', 'label' => 'Edit role'],
            ['name' => 'role.delete', 'label' => 'Delete role'],

            // users
            ['name' => 'user.browse', 'label' => 'Browse users'],
            ['name' => 'user.read', 'label' => 'Read user'],
            ['name' => 'user.edit', 'label' => 'Edit user'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }
    }
}
