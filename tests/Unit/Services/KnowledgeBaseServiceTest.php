<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\KnowledgeBaseService;
use Tests\TestCase;

/**
 * Unit tests for KnowledgeBaseService.
 */
class KnowledgeBaseServiceTest extends TestCase
{
    private KnowledgeBaseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(KnowledgeBaseService::class);
    }

    /** @test */
    public function it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(KnowledgeBaseService::class, $this->service);
    }

    /** @test */
    public function get_context_returns_null_for_empty_query(): void
    {
        $result = $this->service->getContext('');

        $this->assertNull($result);
    }

    /** @test */
    public function get_context_returns_null_when_no_assets_found(): void
    {
        // With no knowledge base assets in test db, should return null
        $result = $this->service->getContext('some random query');

        $this->assertNull($result);
    }

    /** @test */
    public function format_context_formats_assets_correctly(): void
    {
        $assets = [
            (object) [
                'title' => 'Test Asset 1',
                'content' => 'This is test content for asset 1.',
            ],
            (object) [
                'title' => 'Test Asset 2',
                'content' => 'This is test content for asset 2.',
            ],
        ];

        $result = $this->service->formatContext($assets);

        $this->assertStringContainsString('[KNOWLEDGE BASE CONTEXT]', $result);
        $this->assertStringContainsString('Test Asset 1', $result);
        $this->assertStringContainsString('Test Asset 2', $result);
        $this->assertStringContainsString('test content for asset 1', $result);
    }

    /** @test */
    public function format_context_returns_null_for_empty_array(): void
    {
        $result = $this->service->formatContext([]);

        $this->assertNull($result);
    }
}
