<?php
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            'dashboard view',
            'location view',
            'leads approve',
            'leads reject',
            'leads block',
            'leads delete',
            'user approve',
            'user reject',
            'user block',
            'user edit',
            'user delete',
            'admin approve',
            'admin reject',
            'admin block',
            'admin edit',
            'admin delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'sub admin']);

        // Assign permissions to roles
        $adminRole->syncPermissions(Permission::all());

        // Create a default admin user only if it doesn't exist
        if (!\App\Models\User::where('email', 'trustwave@mailinator.com')->exists()) {
            $user = \App\Models\User::create([
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'trustwave@mailinator.com',
                'password' => \Hash::make('123456'),
                'ti_status' => 1
            ]);

            // Assign role to the user
            $user->assignRole('admin');
        }
    }
}
