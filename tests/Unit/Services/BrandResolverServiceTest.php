<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\BrandResolverService;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for BrandResolverService.
 */
class BrandResolverServiceTest extends TestCase
{
    use RefreshDatabase;

    private BrandResolverService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(BrandResolverService::class);
    }

    /** @test */
    public function it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(BrandResolverService::class, $this->service);
    }

    /** @test */
    public function resolve_returns_default_colors_when_no_brand_provided(): void
    {
        $result = $this->service->resolve(null);

        $this->assertArrayHasKey('primary_color', $result);
        $this->assertArrayHasKey('logo_url', $result);
        $this->assertArrayHasKey('brand', $result);
        $this->assertNull($result['brand']);
    }

    /** @test */
    public function get_blueprint_returns_null_for_null_brand_id(): void
    {
        $result = $this->service->getBlueprint(null, 'proposal');
        
        $this->assertNull($result);
    }

    /** @test */
    public function get_blueprint_returns_null_for_invalid_brand_id(): void
    {
        $result = $this->service->getBlueprint('invalid-uuid', 'proposal');
        
        $this->assertNull($result);
    }

    /** @test */
    public function build_brand_instructions_returns_empty_string_for_null_brand(): void
    {
        $result = $this->service->buildBrandInstructions(null, 'proposal');
        
        $this->assertEquals('', $result);
    }

    /** @test */
    public function build_brand_context_returns_null_for_null_brand_id(): void
    {
        $result = $this->service->buildBrandContext(null);
        
        $this->assertNull($result);
    }

    /** @test */
    public function build_brand_context_returns_null_for_invalid_brand_id(): void
    {
        $result = $this->service->buildBrandContext('invalid-uuid-that-does-not-exist');
        
        $this->assertNull($result);
    }
}
