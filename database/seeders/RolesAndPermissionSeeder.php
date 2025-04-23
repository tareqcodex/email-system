<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {      
        $roles = [
            ['name' => 'admin'],
            ['name' => 'writer'],
            ['name' => 'user'],
            ['name' => 'customer'],

        ]; 

        foreach ($roles as  $role) {
            Role::create($role);
        }

        $permissions = [
            ['name' => 'Create'],
            ['name' => 'Show'],
            ['name' => 'Edit'],
            ['name' => 'Delete'],

        ]; 

        foreach ($permissions as  $permission) {
            Permission::create($permission);
        }

        
        $user = User::first();
        $role = Role::first();
        $role->syncPermissions(Permission::all());
        $user->assignRole($role);
        $user->syncPermissions(Permission::all());
    }
}
