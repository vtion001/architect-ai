<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class IAMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Core Permissions
        $permissions = [
            // Content
            ['resource' => 'content', 'action' => 'create', 'scope' => 'own'],
            ['resource' => 'content', 'action' => 'read', 'scope' => 'own'],
            ['resource' => 'content', 'action' => 'update', 'scope' => 'own'],
            ['resource' => 'content', 'action' => 'delete', 'scope' => 'own'],
            ['resource' => 'content', 'action' => 'publish', 'scope' => 'own'],

            ['resource' => 'content', 'action' => 'create', 'scope' => 'tenant'],
            ['resource' => 'content', 'action' => 'read', 'scope' => 'tenant'],
            ['resource' => 'content', 'action' => 'update', 'scope' => 'tenant'],
            ['resource' => 'content', 'action' => 'delete', 'scope' => 'tenant'],
            ['resource' => 'content', 'action' => 'publish', 'scope' => 'tenant'],

            // Users
            ['resource' => 'users', 'action' => 'manage', 'scope' => 'tenant'],
            ['resource' => 'users', 'action' => 'read', 'scope' => 'tenant'],

            // Analytics
            ['resource' => 'analytics', 'action' => 'read', 'scope' => 'tenant'],
            ['resource' => 'analytics', 'action' => 'read', 'scope' => 'sub_account'],

            // Billing
            ['resource' => 'billing', 'action' => 'update', 'scope' => 'tenant'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate($p);
        }

        // 2. Create System Roles
        $roles = [
            [
                'name' => 'Developer',
                'description' => 'Platform level super-admin with observability access',
                'is_system_role' => true,
                'tenant_id' => null, // Global
            ],
            [
                'name' => 'Agency Owner',
                'description' => 'Full administrative access to the agency and its sub-accounts',
                'is_system_role' => true,
                'tenant_id' => null, // Template role
            ],
            [
                'name' => 'Sub-Account Admin',
                'description' => 'Full administrative access to a specific sub-account',
                'is_system_role' => true,
                'tenant_id' => null, // Template role
            ],
            [
                'name' => 'Sub-Account Member',
                'description' => 'Regular user with restricted feature access',
                'is_system_role' => true,
                'tenant_id' => null, // Template role
            ],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name']], $r);
        }

        // 3. Map Permissions to Roles (Basic mapping for now)
        $agencyOwner = Role::where('name', 'Agency Owner')->first();
        $agencyOwner->permissions()->sync(Permission::all());

        $subAccountAdmin = Role::where('name', 'Sub-Account Admin')->first();
        $subAccountAdmin->permissions()->sync(
            Permission::whereIn('scope', ['tenant', 'own'])->where('resource', '!=', 'billing')->get()
        );

        $subAccountMember = Role::where('name', 'Sub-Account Member')->first();
        $subAccountMember->permissions()->sync(
            Permission::where('scope', 'own')->get()
        );
    }
}
