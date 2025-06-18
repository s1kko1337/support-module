<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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

        $user = User::firstOrCreate(
            ['email' => 'sikko4890@gmail.com'],
            [
                'name' => 'Admin',
                'surname' => 'User',
                'password' => Hash::make('sikko4890@gmail.com'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('curator');

        $user->tokens()->delete();
        $permissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();

        $token = $user->createToken(
            name: "Seeder Token for {$user->email}",
            abilities: $permissions
        )->plainTextToken;

        $this->command->info("User created:");
        $this->command->line("Email: sikko4890@gmail.com");
        $this->command->line("Password: sikko4890@gmail.com");
        $this->command->line("Token: $token");
    }
}
