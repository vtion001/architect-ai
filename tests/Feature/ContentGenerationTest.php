<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Services\ContentService;
use App\Services\Factories\ContentGeneratorFactory;
use App\Services\ContentGenerators\SocialPostGenerator;
use App\Services\ContentGenerators\VideoScriptGenerator;
use App\Services\ContentGenerators\BlogPostGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContentGenerationTest extends TestCase
{
    // use RefreshDatabase; // Removed to avoid SQLite migration issues

    protected function setUp(): void
    {
        parent::setUp();
        // Mock OpenAI and HikerAPI
        Http::preventStrayRequests();
        
        // Bypass RAG logic which requires DB
        $this->app->bind(\App\Models\Tenant::class, fn() => null);
    }

    public function test_factory_returns_correct_generator_instance()
    {
        $factory = new ContentGeneratorFactory();

        $this->assertInstanceOf(SocialPostGenerator::class, $factory->make('post'));
        $this->assertInstanceOf(SocialPostGenerator::class, $factory->make('social-media post'));
        $this->assertInstanceOf(VideoScriptGenerator::class, $factory->make('video'));
        $this->assertInstanceOf(BlogPostGenerator::class, $factory->make('blog'));
        // Default
        $this->assertInstanceOf(SocialPostGenerator::class, $factory->make('unknown'));
    }

    public function test_social_post_generator_constructs_correct_prompt()
    {
        Http::fake([
            'api.openai.com/*' => Http::response(['choices' => [['message' => ['content' => 'Generated Post']]]], 200),
        ]);

        $service = app(ContentService::class);
        $result = $service->generateText('My Topic', 'post', null, ['tone' => 'Funny']);

        $this->assertEquals('Generated Post', $result);

        // Verify request sent to OpenAI contained expected prompt keywords
        Http::assertSent(function ($request) {
            $body = $request->data();
            $systemPrompt = $body['messages'][0]['content'];
            return str_contains($systemPrompt, 'viral content creator') && 
                   str_contains($systemPrompt, 'Funny');
        });
    }

    public function test_video_script_generator_constructs_correct_prompt()
    {
        Http::fake([
            'api.openai.com/*' => Http::response(['choices' => [['message' => ['content' => 'Video Script']]]], 200),
        ]);

        $service = app(ContentService::class);
        $result = $service->generateText('My Video', 'video', null, [
            'generator' => 'video',
            'video_platform' => 'tiktok',
            'video_duration' => '30s'
        ]);

        $this->assertEquals('Video Script', $result);

        Http::assertSent(function ($request) {
            $body = $request->data();
            $systemPrompt = $body['messages'][0]['content'];
            return str_contains($systemPrompt, 'creative video storyteller') && 
                   str_contains($systemPrompt, 'tiktok') &&
                   str_contains($systemPrompt, '30s');
        });
    }

    public function test_blog_post_generator_constructs_correct_prompt()
    {
        Http::fake([
            'api.openai.com/*' => Http::response(['choices' => [['message' => ['content' => 'Blog Content']]]], 200),
        ]);

        $service = app(ContentService::class);
        $result = $service->generateText('My Blog', 'blog', null, [
            'generator' => 'blog',
            'blog_structure' => 'Listicle'
        ]);

        $this->assertEquals('Blog Content', $result);

        Http::assertSent(function ($request) {
            $body = $request->data();
            $systemPrompt = $body['messages'][0]['content'];
            return str_contains($systemPrompt, 'insightful thought leader') && 
                   str_contains($systemPrompt, 'Listicle');
        });
    }

    public function test_viral_post_injection_logic()
    {
        // Mock HikerAPI to return examples
        Http::fake([
            'api.hikerapi.com/*' => Http::response(['response' => [['caption' => ['text' => 'Viral Example 1']]]], 200),
            'api.openai.com/*' => Http::response(['choices' => [['message' => ['content' => 'Viral Post']]]], 200),
        ]);

        // Explicitly set HikerAPI key in config for this test
        config(['services.hiker_api.key' => 'test-key']);
        
        // Re-resolve service to pick up config change if needed (though config() helper checks runtime)
        $service = app(ContentService::class);
        
        $service->generateText('Viral Topic', 'post');

        Http::assertSent(function ($request) {
            if ($request->url() === 'https://api.openai.com/v1/chat/completions') {
                $body = $request->data();
                $systemPrompt = $body['messages'][0]['content'];
                return str_contains($systemPrompt, 'Viral Example 1');
            }
            return true;
        });
    }
}
