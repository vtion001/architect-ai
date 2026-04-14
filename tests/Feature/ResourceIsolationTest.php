<?php

namespace Tests\Feature;

use App\Models\AgentConversation;
use App\Models\AiAgent;
use App\Models\Brand;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResourceIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected $tenant1;

    protected $tenant2;

    protected $user1;

    protected $user2;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup two distinct tenants with pro plan (required for ai_agents feature)
        $this->tenant1 = Tenant::factory()->create(['name' => 'Tenant 1', 'plan' => 'pro']);
        $this->tenant2 = Tenant::factory()->create(['name' => 'Tenant 2', 'plan' => 'pro']);

        $this->user1 = User::factory()->create(['tenant_id' => $this->tenant1->id]);
        $this->user2 = User::factory()->create(['tenant_id' => $this->tenant2->id]);

        // Mock OpenAI API to prevent actual network calls and allow success flow
        \Illuminate\Support\Facades\Http::fake([
            'api.openai.com/*' => \Illuminate\Support\Facades\Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'This is a mock response from the AI agent.',
                        ],
                    ],
                ],
            ], 200),
        ]);
    }

    public function test_brands_are_scoped_to_tenant()
    {
        // Create brands for both tenants
        Brand::factory()->create(['tenant_id' => $this->tenant1->id, 'name' => 'Brand T1']);
        Brand::factory()->create(['tenant_id' => $this->tenant2->id, 'name' => 'Brand T2']);

        // Authenticate User 1
        $this->actingAs($this->user1);
        session(['current_tenant_id' => $this->tenant1->id]);

        // Explicitly check the scope
        $brands = Brand::all();

        $this->assertCount(1, $brands);
        $this->assertEquals('Brand T1', $brands->first()->name);

        // Ensure User 1 cannot access Brand T2 via API
        // Note: '/brands' (index) is /settings/brands. '/brands/{brand}' is /settings/brands/{brand}.
        // There is no SHOW route, so we use UPDATE to verify access control.
        $brand2 = Brand::withoutGlobalScopes()->where('name', 'Brand T2')->first();
        $response = $this->putJson("/settings/brands/{$brand2->id}", ['name' => 'Should Fail']);

        // Should be 403 or 404 (Implicit binding often 404s if scope fails match)
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_ai_agents_are_scoped_to_tenant()
    {
        AiAgent::factory()->create(['tenant_id' => $this->tenant1->id, 'name' => 'Agent T1']);
        AiAgent::factory()->create(['tenant_id' => $this->tenant2->id, 'name' => 'Agent T2']);

        $this->actingAs($this->user1);
        session(['current_tenant_id' => $this->tenant1->id]);

        $agents = AiAgent::all();
        $this->assertCount(1, $agents);
        $this->assertEquals('Agent T1', $agents->first()->name);
    }

    public function test_agent_conversation_creation_enforces_tenant()
    {
        $agent = AiAgent::factory()->create(['tenant_id' => $this->tenant1->id]);

        $this->actingAs($this->user1);
        session(['current_tenant_id' => $this->tenant1->id]);

        $this->withoutExceptionHandling();
        $this->withoutMiddleware(\App\Http\Middleware\MfaMiddleware::class);

        // Simulate chat request
        $response = $this->postJson('/ai-agents/chat', [
            'agent_id' => $agent->id,
            'message' => 'Hello',
            'session_id' => Str::uuid()->toString(),
        ]);

        // Assert successful response
        $this->assertEquals(200, $response->status());

        $conversation = AgentConversation::first();

        $this->assertNotNull($conversation, 'Conversation was not created.');
        $this->assertEquals($this->tenant1->id, $conversation->tenant_id);
    }

    public function test_cannot_chat_with_other_tenant_agent()
    {
        $agent2 = AiAgent::factory()->create(['tenant_id' => $this->tenant2->id]);

        $this->actingAs($this->user1);
        session(['current_tenant_id' => $this->tenant1->id]);

        $response = $this->postJson('/ai-agents/chat', [
            'agent_id' => $agent2->id,
            'message' => 'Hello',
        ]);

        // Should return 403 Forbidden (Policy check) or 404 Not Found (Global Scope)
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_policy_prevents_unauthorized_brand_update()
    {
        $brand2 = Brand::factory()->create(['tenant_id' => $this->tenant2->id]);

        $this->actingAs($this->user1);
        session(['current_tenant_id' => $this->tenant1->id]);

        // Attempt to update Brand 2
        $response = $this->putJson("/settings/brands/{$brand2->id}", [
            'name' => 'Hacked Brand',
        ]);

        $this->assertTrue(in_array($response->status(), [403, 404]));
    }
}
