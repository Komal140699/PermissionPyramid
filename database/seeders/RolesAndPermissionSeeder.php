<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;


class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //admin roles
        Role::create(['name'=>'admin', 'guard_name'=>'api']);
        
        Role::create(['name'=>'dept_head', 'guard_name'=>'api']);

        Role::create(['name'=>'employee','guard_name'=>'api']);

        Permission::create(['name' => 'create-employee']);
        Permission::create(['name' => 'edit-employee']);
        Permission::create(['name' => 'delete-employee']);
        Permission::create(['name' => 'view-employee']);
        Permission::create(['name' => 'allocate-task']);
        Permission::create(['name' => 'create-task']);
        Permission::create(['name' => 'assign-task']);
        Permission::create(['name' => 'view-task']);
        Permission::create(['name' => 'edit-task']);
        Permission::create(['name' => 'delete-task']);
        Permission::create(['name' => 'view-report']);

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->syncPermissions([
                'create-employee',
                'delete-employee',
                'view-employee',
                'view-task',
                'delete-task',
                'view-report'
            ]);
        }

        $adminRole = Role::where('name', 'dept_head')->first();
        if ($adminRole) {
            $adminRole->syncPermissions([
                'create-task',
                'view-task',
                'allocate-task'
            ]);
        }
        
        $adminRole = Role::where('name', 'employee')->first();
        if ($adminRole) {
            $adminRole->syncPermissions([
                'view-task',
            ]);
        }
        
        $user=\App\Models\User::create(
        [
            'name' => 'Super Admin',
            'email' => 'superadmin@ultivic.com',
            'password' => Hash::make('superadmin@123'),
        ]);

        $user->assignRole('admin');
    }
}