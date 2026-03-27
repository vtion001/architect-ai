<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\SocialPublishingService;
use Tests\TestCase;

/**
 * Unit tests for SocialPublishingService.
 */
class SocialPublishingServiceTest extends TestCase
{
    private SocialPublishingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SocialPublishingService::class);
    }

    /** @test */
    public function it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(SocialPublishingService::class, $this->service);
    }

    /** @test */
    public function is_video_returns_true_for_mp4_urls(): void
    {
        $this->assertTrue($this->service->isVideo('https://example.com/video.mp4'));
        $this->assertTrue($this->service->isVideo('https://example.com/video.MP4'));
    }

    /** @test */
    public function is_video_returns_true_for_mov_urls(): void
    {
        $this->assertTrue($this->service->isVideo('https://example.com/video.mov'));
        $this->assertTrue($this->service->isVideo('https://example.com/video.MOV'));
    }

    /** @test */
    public function is_video_returns_true_for_webm_urls(): void
    {
        $this->assertTrue($this->service->isVideo('https://example.com/video.webm'));
    }

    /** @test */
    public function is_video_returns_false_for_image_urls(): void
    {
        $this->assertFalse($this->service->isVideo('https://example.com/image.jpg'));
        $this->assertFalse($this->service->isVideo('https://example.com/image.png'));
        $this->assertFalse($this->service->isVideo('https://example.com/image.gif'));
    }

    /** @test */
    public function clean_markdown_for_social_removes_bold_markers(): void
    {
        $input = 'This is **bold** text';
        $result = $this->service->cleanMarkdownForSocial($input);

        $this->assertEquals('This is bold text', $result);
    }

    /** @test */
    public function clean_markdown_for_social_removes_italic_markers(): void
    {
        $input = 'This is *italic* text';
        $result = $this->service->cleanMarkdownForSocial($input);

        $this->assertEquals('This is italic text', $result);
    }

    /** @test */
    public function clean_markdown_for_social_removes_headers(): void
    {
        $input = "# Header 1\n## Header 2\nNormal text";
        $result = $this->service->cleanMarkdownForSocial($input);

        $this->assertStringContainsString('Header 1', $result);
        $this->assertStringNotContainsString('#', $result);
    }

    /** @test */
    public function clean_markdown_for_social_removes_code_blocks(): void
    {
        $input = "Some text\n```code```\nMore text";
        $result = $this->service->cleanMarkdownForSocial($input);

        $this->assertStringNotContainsString('```', $result);
    }

    /** @test */
    public function clean_markdown_for_social_removes_inline_code(): void
    {
        $input = 'This has `inline code` in it';
        $result = $this->service->cleanMarkdownForSocial($input);

        $this->assertEquals('This has inline code in it', $result);
    }

    /** @test */
    public function clean_markdown_for_social_trims_whitespace(): void
    {
        $input = '   Some text with whitespace   ';
        $result = $this->service->cleanMarkdownForSocial($input);

        $this->assertEquals('Some text with whitespace', $result);
    }
}
