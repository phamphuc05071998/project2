<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Define permissions
        $permissions = [
            'manage users',
            'manage roles',
            'manage suppliers',
            'manage product categories',
            'manage stock entries',
            'approve stock entries',
            'manage items' // Thêm quyền quản lý mặt hàng
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Tạo vai trò và gán quyền hạn
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $managerPermissions = [
            'manage suppliers',
            'manage product categories',
            'manage stock entries',
            'approve stock entries',
            'manage items' // Thêm quyền quản lý mặt hàng cho quản lý
        ];
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->syncPermissions($managerPermissions);

        $employeePermissions = ['manage stock entries'];
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeeRole->syncPermissions($employeePermissions);

        // Create the first admin user if they do not exist
        $admin1 = User::firstOrCreate(
            ['email' => 'admin1@example.com'],
            [
                'name' => 'Admin User 1',
                'password' => bcrypt('123456'), // Change this to a secure password
            ]
        );

        // Assign the admin role to the first admin user
        $admin1->assignRole($adminRole);

        // Create the second admin user if they do not exist
        $admin2 = User::firstOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'name' => 'Admin User 2',
                'password' => bcrypt('123456'), // Change this to a secure password
            ]
        );

        // Assign the admin role to the second admin user
        $admin2->assignRole($adminRole);
    }
}
