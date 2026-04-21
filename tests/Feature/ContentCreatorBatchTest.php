<?php

namespace Tests\Feature;

use App\Enums\FeatureType;
use App\Models\User;
use App\Services\FeatureCreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentCreatorBatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_store_requires_feature_credit(): void
    {
        // Setup: create user with exhausted blog_generator credit
        $user = User::factory()->create();
        $user->tenant->update(['plan' => 'starter']);

        // Provision and exhaust the blog generator credit
        $fcs = new FeatureCreditService();
        $fcs->provisionCreditsForUser($user);
        $credit = $fcs->getUserCredit($user, FeatureType::BLOG_GENERATOR);
        $credit->update(['limit' => 0, 'used' => 1]);

        $this->actingAs($user);

        $response = $this->postJson('/content-creator/blog/batch', [
            'topic' => 'Test Topic',
            'count' => 2,
        ]);

        $response->assertStatus(402)
            ->assertJson([
                'success' => false,
                'error' => 'credit_exhausted',
            ]);
    }

    public function test_developer_bypasses_batch_feature_credit_check(): void
    {
        // Setup: developer user (is_developer = true via email match in iam.developer_email)
        $user = User::factory()->create([
            'email' => config('iam.developer_email'),
        ]);
        $user->tenant->update(['plan' => 'starter']);

        // Exhaust credit
        $fcs = new FeatureCreditService();
        $fcs->provisionCreditsForUser($user);
        $credit = $fcs->getUserCredit($user, FeatureType::BLOG_GENERATOR);
        $credit->update(['limit' => 0, 'used' => 1]);

        $this->actingAs($user);

        $response = $this->postJson('/content-creator/blog/batch', [
            'topic' => 'Test Topic',
            'count' => 2,
        ]);

        // Developer should bypass credit check — should NOT get 402
        $this->assertNotEquals(402, $response->status());
    }

    public function test_batch_store_validation_count_min_syntax(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // count < 1 should fail validation
        $response = $this->postJson('/content-creator/blog/batch', [
            'topic' => 'Test Topic',
            'count' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['count']);
    }

    public function test_batch_store_validation_count_max_3(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // count > 3 should fail validation
        $response = $this->postJson('/content-creator/blog/batch', [
            'topic' => 'Test Topic',
            'count' => 4,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['count']);
    }
}
