<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IAMAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\IAMSeeder::class);
        // Disable throttling middleware for auth tests to avoid cache pollution
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
    }

    public function test_agency_registration_creates_tenant_and_owner()
    {
        $response = $this->postJson('/api/auth/register-agency', [
            'company_name' => 'Test Agency',
            'slug' => 'test-agency',
            'email' => 'owner@test.com',
            'password' => 'password123456',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'tenant_id']);

        $this->assertDatabaseHas('tenants', ['slug' => 'test-agency']);
        $this->assertDatabaseHas('users', ['email' => 'owner@test.com']);

        $user = User::where('email', 'owner@test.com')->first();
        $this->assertTrue($user->roles()->where('name', 'Agency Owner')->exists());
    }

    public function test_login_requires_tenant_context()
    {
        // 1. Register
        $this->postJson('/api/auth/register-agency', [
            'company_name' => 'Test Agency',
            'slug' => 'test-agency',
            'email' => 'owner@test.com',
            'password' => 'password123456',
        ]);

        // 2. Login without slug
        $response = $this->postJson('/api/auth/login', [
            'email' => 'owner@test.com',
            'password' => 'password123456',
        ]);
        $response->assertStatus(422);

        // 3. Login with correct slug
        $response = $this->postJson('/api/auth/login', [
            'slug' => 'test-agency',
            'email' => 'owner@test.com',
            'password' => 'password123456',
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_tenant_isolation_prevents_cross_access()
    {
        // 1. Create two tenants
        $tenant1 = Tenant::create(['name' => 'T1', 'slug' => 't1', 'type' => 'agency']);
        $tenant2 = Tenant::create(['name' => 'T2', 'slug' => 't2', 'type' => 'agency']);

        $user1 = User::create([
            'tenant_id' => $tenant1->id,
            'email' => 'u1@t1.com',
            'password' => bcrypt('password123456'),
        ]);

        // 2. Try to login to T2 with U1 credentials
        $response = $this->postJson('/api/auth/login', [
            'slug' => 't2',
            'email' => 'u1@t1.com',
            'password' => 'password123456',
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['email' => ['Invalid credentials for this workspace.']]);
    }
}
