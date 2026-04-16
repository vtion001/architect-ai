<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevUserSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'dev'],
            [
                'name' => 'Dev Agency',
                'type' => 'agency',
                'plan' => 'starter',
                'status' => 'active',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'admin@dev.local'],
            [
                'tenant_id' => $tenant->id,
                'password' => Hash::make('password123'),
                'status' => 'active',
            ]
        );

        $role = Role::where('name', 'Agency Owner')->first();
        if ($role && ! $user->roles()->where('name', 'Agency Owner')->exists()) {
            $user->roles()->attach($role->id, ['scope_type' => 'tenant']);
        }

        app(TokenService::class)->grant($tenant, 1000, 'initial_provisioning');

        $this->command->info('Dev user created: admin@dev.local / password123');
    }
}
