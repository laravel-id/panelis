<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    private array $permissions = [
        // users
        'Create user' => 'Permission to create a new user.',
        'Update user' => 'Permission to update an existing user.',
        'Delete user' => 'Permission to delete a user.',
        'View user' => 'Permission to view an existing user.',
        'Create role' => 'Permission to create a new role.',
        'Update role' => 'Permission to update an existing role.',
        'Delete role' => 'Permission to delete a role.',
        'View role' => 'Permission to view an existing role.',
        'Create permission' => 'Permission to create a new permission.',
        'Update permission' => 'Permission to update an existing permission.',
        'Delete permission' => 'Permission to delete a permission.',
        'View permission' => 'Permission to view an existing permission.',

        // locations
        'Create country' => 'Permission to create a new country.',
        'Update country' => 'Permission to update an existing country.',
        'Delete country' => 'Permission to delete a country.',
        'View country' => 'Permission to view an existing country.',
        'Create region' => 'Permission to create a new region.',
        'Update region' => 'Permission to update an existing region.',
        'Delete region' => 'Permission to delete a region.',
        'View region' => 'Permission to view an existing region.',
        'Create district' => 'Permission to create a new district.',
        'Update district' => 'Permission to update an existing district.',
        'Delete district' => 'Permission to delete a district.',
        'View district' => 'Permission to view an existing district.',

        // todos
        'View todo' => 'View details of a todo.',
        'View all todos' => 'View list of all todos.',
        'Create todo' => 'Create a new todo.',
        'Update todo' => 'Update an existing todo.',
        'Delete todo' => 'Delete an existing todo.',

        // blogs
        'View blog category' => 'Allows users to view a list of all blog categories.',
        'Create blog category' => 'Enables users to add new blog categories.',
        'Update blog category' => 'Permits users to modify existing blog categories.',
        'Delete blog category' => 'Grants users the ability to remove blog categories.',
        'View blog post' => 'Gives users the capability to browse and read published blog posts.',
        'Create blog post' => 'Empowers users to craft and publish new blog posts.',
        'Update blog post' => 'Empowers users to edit and update existing blog posts.',
        'Delete blog post' => 'Empowers users to remove blog posts.',
    ];


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $name => $description) {
            $permission = Permission::FirstOrNew(['name' => $name]);
            $permission->fill([
                'is_default' => true,
                'description' => empty($description) ? null : __($description),
            ]);
            $permission->save();
        }
    }
}
