<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['Admin', 'Editor', 'Sales'] as $role) {
            Role::findOrCreate($role, 'web');
        }

        // Password comes from ADMIN_PASSWORD in .env so it is never committed.
        $password = env('ADMIN_PASSWORD');
        if (! $password) {
            $password = Str::password(16, symbols: false);
            $this->command?->warn("ADMIN_PASSWORD not set; generated admin password: {$password}");
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@wearemorocco.local'],
            ['name' => 'Admin', 'password' => $password, 'email_verified_at' => now()],
        );
        $admin->syncRoles(['Admin']);
    }
}
