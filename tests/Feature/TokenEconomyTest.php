<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\TokenLimit;
use App\Models\TokenAllocation;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenEconomyTest extends TestCase
{
    use RefreshDatabase;

    protected TokenService $tokenService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenService = app(TokenService::class);
    }

    public function test_new_tenant_receives_welcome_bonus()
    {
        // 1. Create a tenant (Observer should trigger)
        $tenant = Tenant::create([
            'name' => 'Acme Corp',
            'slug' => 'acme',
            'type' => 'agency'
        ]);

        // 2. Check balance
        $balance = $this->tokenService->getBalance($tenant);
        $this->assertEquals(config('tokens.initial_grant', 5000), $balance);
    }

    public function test_consumption_tracks_user_and_tenant_limits()
    {
        $tenant = Tenant::create(['name' => 'Test', 'slug' => 'test', 'type' => 'agency']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
        ]);

        // Set a small user limit
        $limit = $this->tokenService->getUserLimit($user);
        $limit->update(['amount' => 100]);

        // 1. Consume within limits
        $this->tokenService->consume($user, 40, 'Test usage');
        
        $this->assertEquals(4960, $this->tokenService->getBalance($tenant)); // 5000 - 40
        $this->assertEquals(40, $limit->fresh()->used);

        // 2. Consume exactly up to limit
        $this->tokenService->consume($user, 60, 'Hit limit');
        $this->assertEquals(100, $limit->fresh()->used);

        // 3. Exceed user limit
        $this->expectException(\App\Exceptions\UserTokenLimitExceededException::class);
        $this->tokenService->consume($user, 1, 'Over limit');
    }

    public function test_tenant_balance_depletion_prevents_consumption()
    {
        $tenant = Tenant::create(['name' => 'Test', 'slug' => 'test', 'type' => 'agency']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
        ]);

        // Drain tenant balance (except for 10 tokens)
        $balance = $this->tokenService->getBalance($tenant);
        $this->tokenService->consume($user, $balance - 10, 'Drain');

        // Try to consume 11 tokens
        $result = $this->tokenService->consume($user, 11, 'Too much');
        $this->assertFalse($result);
    }

    public function test_purchase_increases_tenant_balance()
    {
        $tenant = Tenant::create(['name' => 'Test', 'slug' => 'test', 'type' => 'agency']);
        $initialBalance = $this->tokenService->getBalance($tenant);

        // Simulate a purchase
        $this->tokenService->grant($tenant, 10000, 'Token Purchase');

        $this->assertEquals($initialBalance + 10000, $this->tokenService->getBalance($tenant));
    }
}
