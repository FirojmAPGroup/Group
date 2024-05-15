<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        Permission::create(['name' => 'dashboard view']);
        Permission::create(['name' => 'location view']);
        Permission::create(['name' => 'leads approve']);
        Permission::create(['name' => 'leads reject']);
        Permission::create(['name' => 'leads block']);
        Permission::create(['name' => 'leads delete']);
        Permission::create(['name' => 'team approve']);
        Permission::create(['name' => 'team reject']);
        Permission::create(['name' => 'team block']);
        Permission::create(['name' => 'team edit']);
        Permission::create(['name' => 'team delete']);
        Permission::create(['name' => 'admin approve']);
        Permission::create(['name' => 'admin reject']);
        Permission::create(['name' => 'admin block']);
        Permission::create(['name' => 'admin edit']);
        Permission::create(['name' => 'admin delete']);

        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'sub admin']);
        // Role::create(['name' => 'user']);

        $adminRole->givePermissionTo(Permission::all());

        $user = \App\Models\User::create([
            'first_name' => 'Test',
            'last_name'=>'User',
            'email' => 'admintrustwave@mailinator.com',
            'password'=>\Hash::make('123456'),
            'ti_status'=>1
        ]);

        $user->assignRole('admin');
    }
}
