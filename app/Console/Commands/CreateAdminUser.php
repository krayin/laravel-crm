<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Create admin user';

    public function handle()
    {
        // Create role
        $role = Role::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Administrator',
                'description' => 'Admin Role',
                'permission_type' => 'all'
            ]
        );

        $this->info("Role created: {$role->name}");

        // Create user
        $user = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'role_id' => 1,
                'status' => 1,
                'view_permission' => 'global'
            ]
        );

        $this->info("User created: {$user->email}");
        $this->info("Login at: http://127.0.0.1:8000/admin/login");
        $this->info("Email: admin@admin.com");
        $this->info("Password: admin123");

        return 0;
    }
}
