<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentRole = Role::create(['name' => 'student']);
        $curatorRole = Role::create(['name' => 'curator']);
        $adminRole = Role::create(['name' => 'admin']);

        $basicPermission = Permission::create(['name' => 'use-Api', 'guard_name' => 'web']);
        $adminPermission = Permission::create(['name' => 'administration', 'guard_name' => 'web']);
        $curatorPermission = Permission::create(['name' => 'manage', 'guard_name' => 'web']);

        $studentRole->givePermissionTo($basicPermission);
        $curatorRole->givePermissionTo($basicPermission,$curatorPermission);
        $adminRole->givePermissionTo([$basicPermission, $curatorPermission, $adminPermission]);
    }
}
